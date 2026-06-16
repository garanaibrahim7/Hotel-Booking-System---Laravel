<div 
    x-data="{ show: false, message: '', type: 'success' }"
    x-init="
        @if(session('success'))
            message = '{{ session('success') }}'; type = 'success'; show = true;
        @elseif(session('error') || $errors->any())
            message = '{{ session('error') ?? $errors->first() }}'; type = 'error'; show = true;
        @endif
        if(show) setTimeout(() => show = false, 4000);
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-[-20px]"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    style="position: fixed; top: 30px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 320px;"
>
    <div 
        :class="type === 'success' ? 'border-[#bca47f]' : 'border-red-600'"
        style="background: #1a1a1a; border-width: 1px; border-style: solid; padding: 12px 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: space-between;"
    >
        <div style="display: flex; align-items: center; gap: 12px;">
            <template x-if="type === 'success'">
                <span style="color: #bca47f; font-size: 18px;">✓</span>
            </template>
            <template x-if="type === 'error'">
                <span style="color: #ef4444; font-size: 18px;">✕</span>
            </template>

            <span x-text="message" style="color: #eeeeee; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 500; letter-spacing: 0.5px;"></span>
        </div>

        <button @click="show = false" style="background: none; border: none; color: #666; cursor: pointer; font-size: 20px; margin-left: 15px; line-height: 1;">&times;</button>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>