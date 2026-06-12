@php
    $initialErrors = $errors->all();
    $initialMessage = session('error') ?? session('success') ?? session('info') ?? '';
    $initialOpen = !empty($initialMessage) || !empty($initialErrors);
    $initialType = session('error') ? 'error' : (session('success') ? 'success' : (session('info') ? 'info' : 'error'));
    $flashData = [
        'open' => (bool) $initialOpen,
        'type' => $initialType,
        'message' => $initialMessage,
        'errors' => $initialErrors,
    ];
@endphp

<div id="flash-modal"
     x-data='@json($flashData)'
     x-show="open"
     x-transition
     x-cloak
     class="fixed inset-0 flex items-center justify-center z-50">
    <div class="relative w-[90%] max-w-md">
        <div class="bg-white border rounded-xl px-6 py-5 shadow-2xl"
             :class="(type === 'error' || type === 'warning') ? 'border-red-500' : (type === 'success' ? 'border-green-500' : 'border-primary')">
            <button @click="open = false"
                    class="absolute top-3 right-4 cursor-pointer text-2xl text-gray-400 hover:text-red-500">&times;</button>
            <div class="flex gap-3">
                <span class="text-xl"
                      :class="(type === 'error' || type === 'warning') ? 'text-red-500' : (type === 'success' ? 'text-green-500' : 'text-primary')">
                    <span x-show="type === 'error' || type === 'warning'" x-cloak class="block">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current" aria-hidden="true">
                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 14a1.25 1.25 0 1 1-1.25 1.25A1.25 1.25 0 0 1 12 16Zm1-3a1 1 0 0 1-2 0V7a1 1 0 0 1 2 0Z"/>
                        </svg>
                    </span>
                    <span x-show="type === 'success'" x-cloak class="block">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current" aria-hidden="true">
                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm4.7 7.7-5.5 5.5a1 1 0 0 1-1.4 0l-2.5-2.5a1 1 0 1 1 1.4-1.4l1.8 1.8 4.8-4.8a1 1 0 0 1 1.4 1.4Z"/>
                        </svg>
                    </span>
                    <span x-show="type === 'info'" x-cloak class="block">
                        <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current" aria-hidden="true">
                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 5a1.25 1.25 0 1 1-1.25 1.25A1.25 1.25 0 0 1 12 7Zm1 10a1 1 0 0 1-2 0v-5a1 1 0 0 1 2 0Z"/>
                        </svg>
                    </span>
                </span>
                <div class="flex-1">
                    <h4 class="text-button font-semibold"
                        :class="(type === 'error' || type === 'warning') ? 'text-red-600' : (type === 'success' ? 'text-green-600' : 'text-primary')"
                        x-text="(type === 'error' || type === 'warning') ? 'Something went wrong' : (type === 'success' ? 'Success' : 'Info')">
                    </h4>
                    <template x-if="message">
                        <p class="mt-2 text-gray-600 text-sm" x-text="message"></p>
                    </template>
                    <template x-if="errors && errors.length">
                        <ul class="mt-2 text-gray-600 text-sm list-disc list-inside">
                            <template x-for="(error, idx) in errors" :key="idx">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
