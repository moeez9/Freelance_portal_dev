@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex sm:pt-20 pt-16">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.candidate-menu', ['active' => isset($gig) ? 'services' : 'services_create'])
    </div>

    <div class="content_dashboard scrollbar_custom max-h-full w-full h-fit bg-surface">
<div class="container h-full lg:py-15 sm:py-12 py-8" x-data="gigForm()" x-init="init()" x-cloak>
    <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden mb-4" data-type="menu_dashboard">
        <span class="ph ph-squares-four text-xl"></span>
        <strong class="text-button">Menu</strong>
    </button>
    <!-- Error Handling -->
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.flashModal) {
                    window.flashModal({
                        type: 'error',
                        message: 'Something went wrong',
                        errors: @json($errors->all())
                    });
                }
            });
        </script>
    @endif
<!-- Stepper -->
    <div class="bg-white border-b border-gray-200 mb-10">
        <div class="max-w-5xl mx-auto px-4">
            <nav class="flex justify-between">
                <template x-for="s in steps" :key="s.id">
                    <div class="flex-1">
                        <div :class="step === s.id ? 'border-[#04b2b2]' : 'border-transparent'"
                             class="py-4 border-b-4 flex items-center justify-center transition-all duration-200">
                            <div :class="step >= s.id ? 'bg-[#04b2b2] text-white' : 'bg-gray-200 text-gray-500'"
                                 class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-bold mr-2"
                                 x-text="s.id"></div>
                            <span :class="step >= s.id ? 'text-gray-900 font-bold' : 'text-gray-400 font-bold'"
                                  class="text-xs uppercase tracking-wider hidden md:block" x-text="s.name"></span>
                        </div>
                    </div>
                </template>
            </nav>
        </div>
    </div>

    <form action="{{ isset($gig) ? route('gigs.update', $gig->slug) : route('gigs.store') }}" method="POST" enctype="multipart/form-data" class="max-w-5xl mx-auto" @submit="prepareForSubmit()">
        @csrf
        @if(isset($gig)) @method('PUT') @endif

        <!-- Step 1: Overview -->
        <div x-show="step === 1" class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-bold mb-8 text-gray-800">Gig Overview</h2>
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1"><label class="text-base font-bold text-gray-700">Gig Title</label></div>
                    <div class="md:col-span-2">
                        <div class="relative">
                            <span class="absolute left-4 top-4 text-xl font-bold text-gray-400"></span>
                            <textarea name="title" x-model="formData.title" maxlength="80" rows="3" required
                                      class="w-full pr-4 py-4 border border-gray-300 rounded focus:ring-0 focus:border-[#04b2b2] text-xl font-bold text-gray-800 resize-none" placeholder="Enter your gig title here"></textarea>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1"><label class="text-base font-bold text-gray-700">Category</label></div>
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <select name="gig_category_id" x-model="formData.gig_category_id" @change="updateSubCategories(true)" required
                                class="w-full h-12 px-4 border border-gray-300 rounded text-sm font-bold text-gray-600">
                            <option value="">SELECT A CATEGORY</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="String(cat.id)" x-text="cat.name"></option>
                            </template>
                        </select>
                        <select name="gig_subcategory_id" x-model="formData.gig_subcategory_id" @change="updateServiceTypes(true)" required
                                class="w-full h-12 px-4 border border-gray-300 rounded text-sm font-bold text-gray-600">
                            <option value="">SELECT A SUB-CATEGORY</option>
                            <template x-for="sub in currentSubCategories" :key="sub.id">
                                <option :value="String(sub.id)" x-text="sub.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1"><label class="text-base font-bold text-gray-700">Service Type</label></div>
                    <div class="md:col-span-2">
                        <select name="gig_service_type_id" x-model="formData.gig_service_type_id" required
                                class="w-full h-12 px-4 border border-gray-300 rounded text-sm font-bold text-gray-600">
                            <option value="">SELECT A SERVICE TYPE</option>
                            <template x-for="type in currentServiceTypes" :key="type.id">
                                <option :value="String(type.id)" x-text="type.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1"><label class="text-base font-bold text-gray-700">Search Tags</label></div>
                    <div class="md:col-span-2">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <template x-for="(tag, index) in formData.tags" :key="index">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                                    <span x-text="tag"></span>
                                    <button type="button" @click="removeTag(index)" class="text-gray-400 hover:text-red-500">&times;</button>
                                    <input type="hidden" name="search_tags[]" :value="tag">
                                </span>
                            </template>
                        </div>
                        <div class="flex gap-2">
                            <input type="text"
                                   x-model="newTag"
                                   @keydown.enter.prevent.stop="addTagFromInput()"
                                   placeholder="Enter search terms (max 5)"
                                   class="w-full h-12 px-4 border border-gray-300 rounded text-sm"
                                   :disabled="(Array.isArray(formData.tags) ? formData.tags.length : 0) >= 5">
                            <button type="button"
                                    @click="addTagFromInput()"
                                    class="h-12 px-4 rounded bg-[#04b2b2] text-white font-semibold disabled:opacity-50"
                                    :disabled="(Array.isArray(formData.tags) ? formData.tags.length : 0) >= 5">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Pricing -->
        <div x-show="step === 2" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-8 border-b border-gray-200"><h2 class="text-2xl font-bold text-gray-800">Scope & Pricing</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="p-4 border-b border-r border-gray-200 w-1/4"></th>
                            <template x-for="pkg in ['Basic', 'Standard', 'Premium']">
                                <th class="p-4 border-b border-r border-gray-200 text-center font-bold text-gray-700 uppercase" x-text="pkg"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-4 bg-gray-50 border-b border-r border-gray-200 font-bold text-gray-600">Package Name</td>
                            <template x-for="i in [0,1,2]">
                                <td class="p-0 border-b border-r border-gray-200">
                                    <input type="hidden" :name="'packages['+i+'][type]'" :value="i === 0 ? 'basic' : (i === 1 ? 'standard' : 'premium')">
                                    <textarea :name="'packages['+i+'][name]'" x-model="formData.packages[i].name" required class="w-full p-4 border-none focus:ring-0 resize-none text-sm" rows="2"></textarea>
                                </td>
                            </template>
                        </tr>
                        <tr>
                            <td class="p-4 bg-gray-50 border-b border-r border-gray-200 font-bold text-gray-600">Description</td>
                            <template x-for="i in [0,1,2]">
                                <td class="p-0 border-b border-r border-gray-200">
                                    <textarea :name="'packages['+i+'][description]'" x-model="formData.packages[i].description" required class="w-full p-4 border-none focus:ring-0 resize-none text-sm" rows="4"></textarea>
                                </td>
                            </template>
                        </tr>
                        <tr>
                            <td class="p-4 bg-gray-50 border-b border-r border-gray-200 font-bold text-gray-600">Delivery Days</td>
                            <template x-for="i in [0,1,2]">
                                <td class="p-4 border-b border-r border-gray-200">
                                    <select :name="'packages['+i+'][delivery_days]'" x-model="formData.packages[i].delivery_days" required class="w-full border-gray-300 rounded text-sm">
                                        <option value="1">1 DAY</option><option value="3">3 DAYS</option><option value="7">7 DAYS</option>
                                    </select>
                                </td>
                            </template>
                        </tr>
                        <tr>
                            <td class="p-4 bg-gray-50 border-b border-r border-gray-200 font-bold text-gray-600">Revisions</td>
                            <template x-for="i in [0,1,2]">
                                <td class="p-4 border-b border-r border-gray-200">
                                    <input type="number" :name="'packages['+i+'][revisions]'" x-model="formData.packages[i].revisions" required class="w-full border-gray-300 rounded text-sm" min="0">
                                </td>
                            </template>
                        </tr>
                        <tr>
                            <td class="p-4 bg-gray-50 border-b border-r border-gray-200 font-bold text-gray-600">Price ($)</td>
                            <template x-for="i in [0,1,2]">
                                <td class="p-4 border-b border-r border-gray-200">
                                    <input type="number" :name="'packages['+i+'][price]'" x-model="formData.packages[i].price" required class="w-full border-gray-300 rounded text-sm" min="5">
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 3: Description -->
        <div x-show="step === 3" class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-bold mb-8 text-gray-800">Description</h2>
            <textarea name="description" x-model="formData.description" rows="12" required class="w-full p-4 border border-gray-300 rounded text-gray-700"></textarea>
        </div>

        <!-- Step 4: Requirements -->
        <div x-show="step === 4" class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Requirements</h2>
            <div class="space-y-6">
                <template x-for="(q, index) in formData.questions" :key="index">
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 relative">
                        <button type="button" @click="removeQuestion(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">×</button>
                        <p class="text-gray-800" x-text="q"></p>
                        <input type="hidden" name="requirement_questions[]" :value="q">
                    </div>
                </template>
                <div class="border-2 border-dashed border-gray-200 p-6 rounded-lg">
                    <textarea x-model="newQuestion" rows="3" class="w-full p-3 border border-gray-300 rounded text-sm" placeholder="Add a question..."></textarea>
                    <button type="button" @click="addQuestion()" class="mt-3 text-[#04b2b2] font-bold">+ Add New Question</button>
                </div>
            </div>
        </div>

        <!-- Step 5: Gallery -->
        <div x-show="step === 5" class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-bold mb-8 text-gray-800">Gallery</h2>
            <div class="space-y-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 uppercase">Thumbnail</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg h-[140px] w-full max-w-[220px] relative flex items-center justify-center overflow-hidden">
                            <input type="file" name="thumbnail" class="absolute inset-0 opacity-0 cursor-pointer" @change="previewImage($event, 'thumb')">
                            <template x-if="!previews.thumb">
                                <span class="ph ph-image text-4xl text-gray-300"></span>
                            </template>
                            <template x-if="previews.thumb">
                                <div class="relative w-full h-full">
                                    <img :src="previews.thumb" class="w-full h-full object-cover">
                                    <button type="button" @click.stop="openPreviewViewer('thumb')" class="absolute top-2 right-10 bg-black/70 text-white rounded px-2 py-1 text-[10px] uppercase tracking-wide">Full</button>
                                    <button type="button" @click="removeMedia('thumbnail')" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 text-xs">×</button>
                                    <template x-if="mediaRemoved.thumbnail">
                                        <input type="hidden" name="remove_media[thumbnail]" value="1">
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    <template x-for="i in [0,1,2]">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase">Gallery Image</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg h-[140px] w-full max-w-[220px] relative flex items-center justify-center overflow-hidden">
                                <input type="file" name="gallery[]" class="absolute inset-0 opacity-0 cursor-pointer" @change="previewImage($event, 'gal'+i)">
                                <template x-if="!previews['gal'+i]">
                                    <span class="ph ph-plus text-2xl text-gray-300"></span>
                                </template>
                                <template x-if="previews['gal'+i]">
                                    <div class="relative w-full h-full">
                                        <img :src="previews['gal'+i]" class="w-full h-full object-cover">
                                        <button type="button" @click.stop="openPreviewViewer('gal'+i)" class="absolute top-2 right-10 bg-black/70 text-white rounded px-2 py-1 text-[10px] uppercase tracking-wide">Full</button>
                                        <button type="button" @click="removeMedia('gallery', i)" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 text-xs">×</button>
                                        <template x-if="mediaRemoved.gallery[i]">
                                            <input type="hidden" :name="'remove_media[gallery]['+i+']'" :value="i">
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                <div>
                    <label class="font-bold text-gray-800 block mb-2">Video (1 Only)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center relative h-32 flex flex-col items-center justify-center">
                        <input type="file" name="video" accept="video/*" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleVideo($event)">
                        <div class="flex items-center gap-4">
                            <span x-text="videoName || 'Upload Video'" class="text-gray-500 font-bold"></span>
                            <button type="button" x-show="videoName" @click="removeMedia('video')" class="text-red-500 font-bold">Remove</button>
                            <template x-if="mediaRemoved.video">
                                <input type="hidden" name="remove_media[video]" value="1">
                            </template>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="font-bold text-gray-800 block mb-2">Documents (Up to 2)</label>
                    <div class="grid grid-cols-2 gap-6">
                        <template x-for="i in [0,1]">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg h-24 relative flex items-center justify-center">
                                <input type="file" name="documents[]" accept=".pdf,.doc,.docx" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleDoc($event, i)">
                                <div class="flex flex-col items-center px-2">
                                    <span x-text="docNames[i] || 'Add PDF/DOC'" class="text-xs font-bold text-gray-400 truncate max-w-full"></span>
                                    <button type="button" x-show="docNames[i]" @click="removeMedia('documents', i)" class="text-red-500 text-[10px] font-bold">Remove</button>
                                    <template x-if="mediaRemoved.documents[i]">
                                        <input type="hidden" :name="'remove_media[documents]['+i+']'" :value="i">
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6: Publish -->
        <div x-show="step === 6" class="bg-white p-12 rounded-lg shadow-sm border border-gray-200 text-center">
            <h2 class="text-3xl font-bold mb-4 text-gray-800">Ready to Publish?</h2>
            <div class="flex justify-center gap-4">
                <button type="button" @click="step--" class="bg-gray-200 text-gray-700 px-12 py-4 rounded font-bold text-xl hover:bg-gray-300">Back</button>
                <button type="submit" class="bg-[#04b2b2] text-white px-12 py-4 rounded font-bold text-xl hover:bg-[#039494] shadow-lg">
                    {{ isset($gig) ? 'Update Gig' : 'Publish Gig' }}
                </button>
            </div>
        </div>

        <!-- Footer Navigation -->
        <div class="mt-8 flex justify-between items-center px-4" x-show="step < 6">
            <button type="button" @click="step > 1 ? step-- : null" class="text-gray-500 font-bold uppercase text-sm" :class="step === 1 ? 'invisible' : ''">Back</button>
            <button type="button" @click="nextStep()" class="bg-[#04b2b2] text-white px-10 py-3 rounded font-bold shadow-md">Save & Continue</button>
        </div>

        <div x-show="previewViewer.open" x-cloak class="fixed inset-0 z-[120] bg-black/85 flex items-center justify-center p-4" @click.self="closePreviewViewer()">
            <button type="button" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white text-black flex items-center justify-center" @click="closePreviewViewer()">
                <span class="ph ph-x text-xl"></span>
            </button>
            <button type="button" class="absolute left-4 md:left-8 w-11 h-11 rounded-full bg-white text-black flex items-center justify-center" @click="prevPreviewViewer()" x-show="previewGalleryItems.length > 1">
                <span class="ph ph-caret-left text-2xl"></span>
            </button>
            <img :src="previewViewer.currentUrl" alt="Preview" class="max-w-[95vw] max-h-[85vh] rounded-lg shadow-2xl object-contain">
            <button type="button" class="absolute right-4 md:right-8 w-11 h-11 rounded-full bg-white text-black flex items-center justify-center" @click="nextPreviewViewer()" x-show="previewGalleryItems.length > 1">
                <span class="ph ph-caret-right text-2xl"></span>
            </button>
        </div>
    </form>
</div>
    </div>
</div>

<script>
    window.gigCategoryTree = @json($categoryTree ?? []);
    window.gigCategories = @json($gigCategories ?? []);
</script>

<script>
function gigForm() {
    return {
        step: 1,
        steps: [{ id: 1, name: 'Overview' }, { id: 2, name: 'Pricing' }, { id: 3, name: 'Description' }, { id: 4, name: 'Requirements' }, { id: 5, name: 'Gallery' }, { id: 6, name: 'Publish' }],
        categories: [],
        currentSubCategories: [], currentServiceTypes: [],
        categoryTree: [],
        formData: {
            title: '', tags: [], description: '',
            gig_category_id: '', gig_subcategory_id: '', gig_service_type_id: '',
            packages: [
                { type: 'basic', name: '', description: '', delivery_days: 1, revisions: 1, price: 5 },
                { type: 'standard', name: '', description: '', delivery_days: 3, revisions: 2, price: 50 },
                { type: 'premium', name: '', description: '', delivery_days: 7, revisions: 3, price: 100 }
            ],
            questions: []
        },
        newTag: '', newQuestion: '', videoName: '', docNames: { 0: '', 1: '' }, previews: { thumb: null, gal0: null, gal1: null, gal2: null },
        mediaRemoved: { thumbnail: false, video: false, gallery: [false, false, false], documents: [false, false] },
        previewViewer: { open: false, currentKey: 'thumb', currentUrl: '' },

        async init() {
            this.categoryTree = Array.isArray(window.gigCategoryTree) ? window.gigCategoryTree : [];
            if (this.categoryTree.length) {
                this.categories = this.categoryTree.map(c => ({ id: c.id, name: c.name, slug: c.slug, subs: c.subs || [] }));
            } else if (Array.isArray(window.gigCategories) && window.gigCategories.length) {
                this.categories = window.gigCategories;
            } else {
                await this.loadCategories();
            }
            @if(isset($gig))
                const gig = @json($gig);
                this.formData.title = gig.title;
                await this.hydrateTaxonomySelection(gig);
                this.formData.tags = this.normalizeTags(gig.search_tags);
                this.formData.description = gig.description;
                this.formData.questions = (gig.requirements || []).map(r => r.question);
                if (!this.formData.questions.length && gig.requirement_questions) {
                    this.formData.questions = gig.requirement_questions;
                }

                gig.packages.forEach((p, i) => {
                    if(this.formData.packages[i]) {
                        this.formData.packages[i].name = p.name;
                        this.formData.packages[i].description = p.description;
                        this.formData.packages[i].delivery_days = p.delivery_days || p.delivery_time;
                        this.formData.packages[i].revisions = p.revisions ?? 0;
                        this.formData.packages[i].price = p.price;
                    }
                });
                if(gig.thumbnail) this.previews.thumb = '/storage/' + gig.thumbnail;
                if(gig.gallery && gig.gallery[0]) this.previews.gal0 = '/storage/' + gig.gallery[0];
                if(gig.gallery && gig.gallery[1]) this.previews.gal1 = '/storage/' + gig.gallery[1];
                if(gig.gallery && gig.gallery[2]) this.previews.gal2 = '/storage/' + gig.gallery[2];
                if(gig.video_path) this.videoName = 'Existing Video';
                if(gig.document_paths && gig.document_paths[0]) this.docNames[0] = 'Document 1';
                if(gig.document_paths && gig.document_paths[1]) this.docNames[1] = 'Document 2';
            @endif
        },
        async loadCategories() {
            const response = await fetch('/categories');
            if (!response.ok) return;
            this.categories = await response.json();
        },
        async updateSubCategories(reset) {
            const categoryId = this.formData.gig_category_id;
            this.currentSubCategories = [];
            if (reset) {
                this.formData.gig_subcategory_id = '';
                this.currentServiceTypes = [];
                this.formData.gig_service_type_id = '';
            }
            if (!categoryId) return;
            const selectedCategory = this.categories.find(c => String(c.id) === String(categoryId));
            const categoryRef = selectedCategory?.slug || categoryId;
            if (this.categoryTree.length) {
                const category = this.categoryTree.find(c => String(c.id) === String(categoryId));
                const subs = (category?.subs || []).map(s => ({ id: s.id, name: s.name, slug: s.slug, types: s.types || [] }));
                this.currentSubCategories = subs;
                if (subs.length) return;
            }
            const response = await fetch(`/subcategories/${categoryRef}`);
            if (!response.ok) return;
            this.currentSubCategories = await response.json();
        },
        async updateServiceTypes(reset) {
            const subcategoryId = this.formData.gig_subcategory_id;
            this.currentServiceTypes = [];
            if (reset) {
                this.formData.gig_service_type_id = '';
            }
            if (!subcategoryId) return;
            const selectedSubcategory = this.currentSubCategories.find(s => String(s.id) === String(subcategoryId));
            const subcategoryRef = selectedSubcategory?.slug || subcategoryId;
            if (this.currentSubCategories.length && this.currentSubCategories[0].types) {
                const sub = this.currentSubCategories.find(s => String(s.id) === String(subcategoryId));
                const types = sub?.types || [];
                this.currentServiceTypes = types;
                if (types.length) return;
            }
            const response = await fetch(`/services/${subcategoryRef}`);
            if (!response.ok) return;
            this.currentServiceTypes = await response.json();
        },
        async hydrateTaxonomySelection(gig) {
            const categoryId = this.resolveCategoryId(gig) || this.findCategoryIdBySubcategoryId(
                gig.gig_subcategory_id ?? gig.gig_subcategory?.id ?? null
            );
            const subcategoryId = this.resolveSubcategoryId(gig);
            const serviceTypeId = this.resolveServiceTypeId(gig);

            this.formData.gig_category_id = categoryId ? String(categoryId) : '';
            await this.$nextTick();
            const categorySelect = document.querySelector('select[name="gig_category_id"]');
            if (categorySelect && this.formData.gig_category_id) {
                categorySelect.value = String(this.formData.gig_category_id);
            }
            await this.updateSubCategories(false);

            if (!this.formData.gig_category_id && subcategoryId) {
                this.formData.gig_category_id = this.findCategoryIdBySubcategoryId(subcategoryId);
                await this.$nextTick();
                await this.updateSubCategories(false);
            }

            this.formData.gig_subcategory_id = subcategoryId ? String(subcategoryId) : '';
            await this.$nextTick();
            const subcategorySelect = document.querySelector('select[name="gig_subcategory_id"]');
            if (subcategorySelect && this.formData.gig_subcategory_id) {
                subcategorySelect.value = String(this.formData.gig_subcategory_id);
            }
            await this.updateServiceTypes(false);

            this.formData.gig_service_type_id = serviceTypeId ? String(serviceTypeId) : '';
            await this.$nextTick();
            const serviceTypeSelect = document.querySelector('select[name="gig_service_type_id"]');
            if (serviceTypeSelect && this.formData.gig_service_type_id) {
                serviceTypeSelect.value = String(this.formData.gig_service_type_id);
            }
        },
        addTagFromInput() {
            if (!Array.isArray(this.formData.tags)) {
                this.formData.tags = this.normalizeTags(this.formData.tags);
            }
            const val = (this.newTag || '').trim();
            if (val && this.formData.tags.length < 5 && !this.formData.tags.includes(val)) {
                this.formData.tags.push(val);
            } else if (val && val.includes(',')) {
                const items = val.split(',').map(v => v.trim()).filter(Boolean);
                for (const item of items) {
                    if (this.formData.tags.length >= 5) break;
                    if (!this.formData.tags.includes(item)) this.formData.tags.push(item);
                }
            }
            this.newTag = '';
        },
        normalizeTags(value) {
            if (Array.isArray(value)) return value.filter(tag => typeof tag === 'string' && tag.trim() !== '');
            if (typeof value === 'string') {
                try {
                    const parsed = JSON.parse(value);
                    if (Array.isArray(parsed)) {
                        return parsed.filter(tag => typeof tag === 'string' && tag.trim() !== '');
                    }
                } catch (_) {}
                return value.split(',').map(v => v.trim()).filter(Boolean);
            }
            return [];
        },
        resolveCategoryId(gig) {
            const direct = gig.gig_category_id ?? gig.gig_category?.id ?? null;
            if (direct !== null && direct !== undefined && direct !== '') return String(direct);
            const fromSub = gig.gig_subcategory?.gig_category_id ?? null;
            if (fromSub !== null && fromSub !== undefined && fromSub !== '') return String(fromSub);
            if (!gig.category) return '';
            const found = this.categories.find(c => c.name === gig.category);
            return found ? String(found.id) : '';
        },
        findCategoryIdBySubcategoryId(subcategoryId) {
            if (!subcategoryId || !Array.isArray(this.categoryTree) || !this.categoryTree.length) return '';
            const subId = String(subcategoryId);
            for (const category of this.categoryTree) {
                const found = (category.subs || []).find(s => String(s.id) === subId);
                if (found) return String(category.id);
            }
            return '';
        },
        resolveSubcategoryId(gig) {
            const direct = gig.gig_subcategory_id ?? gig.gig_subcategory?.id ?? null;
            if (direct !== null && direct !== undefined && direct !== '') return String(direct);
            if (!gig.sub_category || !this.formData.gig_category_id) return '';
            const category = this.categoryTree.find(c => String(c.id) === String(this.formData.gig_category_id));
            const found = (category?.subs || []).find(s => s.name === gig.sub_category);
            return found ? String(found.id) : '';
        },
        resolveServiceTypeId(gig) {
            const direct = gig.gig_service_type_id ?? gig.gig_service_type?.id ?? null;
            if (direct !== null && direct !== undefined && direct !== '') return String(direct);
            if (!gig.service_type || !this.formData.gig_subcategory_id) return '';
            const subcategory = this.currentSubCategories.find(s => String(s.id) === String(this.formData.gig_subcategory_id));
            const found = (subcategory?.types || []).find(t => t.name === gig.service_type);
            return found ? String(found.id) : '';
        },
        removeTag(index) { this.formData.tags.splice(index, 1); },
        addQuestion() {
            if (this.newQuestion.trim()) {
                this.formData.questions.push(this.newQuestion.trim()); this.newQuestion = '';
            }
        },
        removeQuestion(index) { this.formData.questions.splice(index, 1); },
        nextStep() {
            this.prepareForSubmit();
            if (this.step === 1 && (!this.formData.title || !this.formData.gig_category_id || !this.formData.gig_subcategory_id || !this.formData.gig_service_type_id)) {
                if (window.flashModal) {
                    window.flashModal({ type: 'error', message: 'Title, Category, Subcategory, and Service Type are required.' });
                }
                return;
            }
            if (this.step < 6) this.step++;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        prepareForSubmit() {
            this.addTagFromInput();
            this.addQuestion();
        },
        previewImage(event, key) {
            const file = event.target.files[0];
            if (file) {
                this.previews[key] = URL.createObjectURL(file);
                if(key === 'thumb') this.mediaRemoved.thumbnail = false;
                if(key.startsWith('gal')) this.mediaRemoved.gallery[key.slice(-1)] = false;
            }
        },
        handleVideo(event) {
            const file = event.target.files[0];
            if (file) { this.videoName = file.name; this.mediaRemoved.video = false; }
        },
        handleDoc(event, i) {
            const file = event.target.files[0];
            if (file) { this.docNames[i] = file.name; this.mediaRemoved.documents[i] = false; }
        },
        get previewGalleryItems() {
            const items = [];
            ['thumb', 'gal0', 'gal1', 'gal2'].forEach((key) => {
                if (this.previews[key]) {
                    items.push({ key, url: this.previews[key] });
                }
            });
            return items;
        },
        openPreviewViewer(key) {
            const items = this.previewGalleryItems;
            const activeItem = items.find((item) => item.key === key);
            if (!activeItem) return;
            this.previewViewer.currentKey = activeItem.key;
            this.previewViewer.currentUrl = activeItem.url;
            this.previewViewer.open = true;
        },
        closePreviewViewer() {
            this.previewViewer.open = false;
            this.previewViewer.currentKey = 'thumb';
            this.previewViewer.currentUrl = '';
        },
        prevPreviewViewer() {
            const items = this.previewGalleryItems;
            if (!items.length) return;
            const currentIndex = items.findIndex((item) => item.key === this.previewViewer.currentKey);
            const prevIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
            this.previewViewer.currentKey = items[prevIndex].key;
            this.previewViewer.currentUrl = items[prevIndex].url;
        },
        nextPreviewViewer() {
            const items = this.previewGalleryItems;
            if (!items.length) return;
            const currentIndex = items.findIndex((item) => item.key === this.previewViewer.currentKey);
            const nextIndex = currentIndex >= items.length - 1 ? 0 : currentIndex + 1;
            this.previewViewer.currentKey = items[nextIndex].key;
            this.previewViewer.currentUrl = items[nextIndex].url;
        },
        removeMedia(type, index = null) {
            if(type === 'thumbnail') { this.previews.thumb = null; this.mediaRemoved.thumbnail = true; }
            if(type === 'video') { this.videoName = ''; this.mediaRemoved.video = true; }
            if(type === 'gallery') { this.previews['gal'+index] = null; this.mediaRemoved.gallery[index] = true; }
            if(type === 'documents') { this.docNames[index] = ''; this.mediaRemoved.documents[index] = true; }
            if (!this.previewGalleryItems.length) {
                this.closePreviewViewer();
            } else if (this.previewViewer.open) {
                this.nextPreviewViewer();
            }
        }
    }
}
</script>

<div class="modal">
    <div class="modal_item menu_dashboard -modal overflow-hidden relative flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white" data-type="menu_dashboard">
        @include('partials.dashboard.candidate-menu', ['active' => isset($gig) ? 'services' : 'services_create'])
    </div>
</div>
@endsection
