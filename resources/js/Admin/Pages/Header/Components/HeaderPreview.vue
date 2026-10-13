<script setup>
// Miniature of the live header (top-level menu, logo, CTA). Reflects saved settings.
defineProps({
    settings: { type: Object, required: true },
    menu: { type: Array, required: true },
});
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <p class="border-b border-slate-100 px-5 py-2.5 text-xs font-medium tracking-wide text-slate-400 uppercase">Preview</p>
        <div class="overflow-x-auto bg-[#F8F4EC]">
            <div class="flex min-w-[720px] items-center justify-between gap-6 px-6 py-4">
                <div class="flex shrink-0 items-center gap-2.5">
                    <img v-if="settings.logo_url" :src="settings.logo_url" :alt="settings.logo_alt" class="w-auto object-contain" :style="{ height: `${Math.min(settings.logo_height, 46)}px` }" />
                    <template v-else>
                        <svg viewBox="0 0 64 64" class="size-9 text-forest" aria-hidden="true"><path d="M14 22 A22 22 0 0 1 54 30" fill="none" stroke="#B8925A" stroke-width="2.2" stroke-linecap="round" /><path d="M30 56 C29 44 31 34 38 26" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" /><path d="M30 40 C19 41 9 34 7 26 C17 25 27 30 30 40Z" fill="currentColor" /><path d="M32 30 C29 22 33 13 42 10 C44 19 40 27 32 30Z" fill="#B8925A" /></svg>
                        <span class="leading-none">
                            <span class="block text-lg font-semibold text-forest-deep" style="font-family: 'Cormorant Garamond', Georgia, serif">{{ settings.brand_name }}</span>
                            <span v-if="settings.brand_tagline" class="mt-1 block text-[8px] tracking-[0.3em] text-slate-500 uppercase">{{ settings.brand_tagline }}</span>
                        </span>
                    </template>
                </div>
                <ul class="flex items-center gap-5 text-[13px] text-slate-700">
                    <li v-for="item in menu.filter((i) => i.is_visible)" :key="item.id" class="flex items-center gap-1 whitespace-nowrap">
                        {{ item.display_label }}
                        <svg v-if="item.children.some((c) => c.is_visible)" class="size-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6" /></svg>
                    </li>
                </ul>
                <span v-if="settings.cta_visible" class="shrink-0 rounded-full bg-forest px-4 py-2 text-xs font-medium text-white">{{ settings.cta_label }}</span>
                <span v-else class="w-16" />
            </div>
        </div>
    </div>
</template>
