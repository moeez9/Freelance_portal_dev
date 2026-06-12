<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGigRequest;
use App\Http\Requests\UpdateGigRequest;
use App\Models\Gig;
use App\Models\GigCategory;
use App\Models\GigServiceType;
use App\Models\GigSubcategory;
// use App\Models\GigPackage;
// use App\Models\GigRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class GigController extends Controller
{
    public function index(Request $request)
    {
        $query = Gig::where('status', 'active')->with(['packages', 'freelancer', 'gigCategory']);

        // Search and Category Filters
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category')) {
            $query->whereHas('gigCategory', fn ($cat) => $cat->where('name', $request->category));
        }

        $gigs = $query->latest()->get();
        return view('services-default', compact('gigs'));
    }

    public function myGigs()
    {
        $gigs = Gig::where('freelancer_id', Auth::id())
            ->with(['gigCategory'])
            ->latest()
            ->get();
        return view('candidates-gigs', compact('gigs'));
    }

    public function create()
    {
        return view('create-gig', [
            'isEdit' => false,
            'gig' => null,
            'gigCategories' => GigCategory::orderBy('name')->get(['id', 'name']),
            'categoryTree' => $this->buildCategoryTree(),
        ]);
    }

    public function edit(Gig $gig)
    {
        abort_if($gig->freelancer_id !== Auth::id(), 403);

        $gig->load(['packages', 'requirements', 'gigCategory', 'gigSubcategory', 'gigServiceType']);
        return view('create-gig', [
            'gig' => $gig,
            'isEdit' => true,
            'gigCategories' => GigCategory::orderBy('name')->get(['id', 'name']),
            'categoryTree' => $this->buildCategoryTree(),
        ]);
    }

    public function store(StoreGigRequest $request)
    {
        return $this->saveGig($request);
    }

    public function update(UpdateGigRequest $request, Gig $gig)
    {
        abort_if($gig->freelancer_id !== Auth::id(), 403);
        return $this->saveGig($request, $gig->id);
    }

    protected function saveGig(Request $request, $id = null)
    {
        if (!Auth::check() || Auth::user()->role !== 'candidate') {
            abort(403);
        }

        $data = $request->only([
            'title',
            'description',
            'gig_category_id',
            'gig_subcategory_id',
            'gig_service_type_id',
            'search_tags',
        ]);

        $category = $request->filled('gig_category_id')
            ? GigCategory::find($request->gig_category_id)
            : null;
        $subcategory = $request->filled('gig_subcategory_id')
            ? GigSubcategory::find($request->gig_subcategory_id)
            : null;
        $serviceType = $request->filled('gig_service_type_id')
            ? GigServiceType::find($request->gig_service_type_id)
            : null;

        // Backward compatibility for already-migrated DBs that still store text taxonomy columns.
        if (Schema::hasColumn('gigs', 'category')) {
            $data['category'] = $category?->name ?? '';
        }
        if (Schema::hasColumn('gigs', 'sub_category')) {
            $data['sub_category'] = $subcategory?->name;
        }
        if (Schema::hasColumn('gigs', 'service_type')) {
            $data['service_type'] = $serviceType?->name;
        }

        $data['freelancer_id'] = Auth::id();
        $data['status'] = 'active';

        if ($id) {
            $gig = Gig::findOrFail($id);
        }

        // Handle Media Removal
        if ($id && $request->has('remove_media')) {
            foreach ($request->remove_media as $type => $value) {
                if ($type === 'thumbnail' && $gig->thumbnail) {
                    Storage::disk('public')->delete($gig->thumbnail);
                    $data['thumbnail'] = null;
                } elseif ($type === 'video' && $gig->video_path) {
                    Storage::disk('public')->delete($gig->video_path);
                    $data['video_path'] = null;
                } elseif ($type === 'gallery' && is_array($value)) {
                    $currentGallery = $gig->gallery ?? [];
                    foreach ($value as $index) {
                        if (isset($currentGallery[$index])) {
                            Storage::disk('public')->delete($currentGallery[$index]);
                            unset($currentGallery[$index]);
                        }
                    }
                    $data['gallery'] = array_values($currentGallery);
                } elseif ($type === 'documents' && is_array($value)) {
                    $currentDocs = $gig->document_paths ?? [];
                    foreach ($value as $index) {
                        if (isset($currentDocs[$index])) {
                            Storage::disk('public')->delete($currentDocs[$index]);
                            unset($currentDocs[$index]);
                        }
                    }
                    $data['document_paths'] = array_values($currentDocs);
                }
            }
        }

        // Media Uploads
        if ($request->hasFile('thumbnail')) {
            if ($id && $gig->thumbnail) Storage::disk('public')->delete($gig->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('gigs/thumbnails', 'public');
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = ($id && !isset($data['gallery'])) ? ($gig->gallery ?? []) : ($data['gallery'] ?? []);
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('gigs/gallery', 'public');
            }
            $data['gallery'] = $galleryPaths;
        }

        if ($request->hasFile('video')) {
            if ($id && $gig->video_path) Storage::disk('public')->delete($gig->video_path);
            $data['video_path'] = $request->file('video')->store('gigs/videos', 'public');
        }

        if ($request->hasFile('documents')) {
            $docPaths = ($id && !isset($data['document_paths'])) ? ($gig->document_paths ?? []) : ($data['document_paths'] ?? []);
            foreach ($request->file('documents') as $doc) {
                $docPaths[] = $doc->store('gigs/documents', 'public');
            }
            $data['document_paths'] = $docPaths;
        }

        if ($id) {
            $gig->update($data);
        } else {
            $gig = Gig::create($data);
        }

        // Update Packages
        $gig->packages()->delete();
        foreach ($request->packages as $packageData) {
            $gig->packages()->create([
                'type' => $packageData['type'],
                'name' => $packageData['name'],
                'description' => $packageData['description'],
                'price' => $packageData['price'],
                'revisions' => $packageData['revisions'] ?? 0,
                'delivery_days' => $packageData['delivery_days'],
            ]);
        }

        // Update Requirements
        $questions = $request->input('requirement_questions', []);
        $gig->requirements()->delete();
        foreach (array_values($questions) as $index => $question) {
            if (!is_string($question) || trim($question) === '') {
                continue;
            }
            $gig->requirements()->create([
                'question' => trim($question),
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('candidate.services')
            ->with('success', $id ? 'Gig updated successfully' : 'Gig successfully published');
    }

    public function updateStatus(Request $request, Gig $gig)
    {
        $gig = Gig::where('freelancer_id', Auth::id())->where('id', $gig->id)->firstOrFail();
        if ($request->status === 'deleted') {
            $gig->delete();
            return back()->with('success', 'Gig deleted successfully');
        }
        $gig->update(['status' => $request->status]);
        return back()->with('success', 'Gig status updated');
    }

    public function show(Request $request, Gig $gig)
    {
        $gig->load(['packages', 'freelancer', 'requirements', 'gigCategory', 'gigSubcategory', 'gigServiceType']);
        $packages = $gig->packages()->get();

        $selectedPackage = null;
        $requestedPackageId = (int) $request->query('package_id', 0);
        $requestedPackageType = (string) $request->query('package', '');

        if ($requestedPackageId > 0) {
            $selectedPackage = $packages->firstWhere('id', $requestedPackageId);
        }
        if (!$selectedPackage && $requestedPackageType !== '') {
            $selectedPackage = $packages->firstWhere('type', strtolower($requestedPackageType));
        }
        if (!$selectedPackage) {
            $selectedPackage = $packages->first();
        }

        return view('services-detail2', compact('gig', 'packages', 'selectedPackage'));
    }

    public function subcategories(GigCategory $category)
    {
        if (!$category) {
            return response()->json([]);
        }

        return response()->json(
            $category->subcategories()
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
        );
    }

    public function services(GigSubcategory $subcategory)
    {
        if (!$subcategory) {
            return response()->json([]);
        }

        return response()->json(
            $subcategory->serviceTypes()
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
        );
    }

    public function categories()
    {
        return response()->json(
            GigCategory::orderBy('name')->get(['id', 'name', 'slug'])
        );
    }

    private function buildCategoryTree(): array
    {
        $categories = GigCategory::with('subcategories.serviceTypes')->orderBy('name')->get();
        $tree = [];
        foreach ($categories as $category) {
            $subcats = [];
            foreach ($category->subcategories as $subcategory) {
                $subcats[] = [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'slug' => $subcategory->slug,
                    'types' => $subcategory->serviceTypes->map(function ($type) {
                        return ['id' => $type->id, 'name' => $type->name, 'slug' => $type->slug];
                    })->values()->all(),
                ];
            }
            $tree[] = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'subs' => $subcats,
            ];
        }
        return $tree;
    }
}
