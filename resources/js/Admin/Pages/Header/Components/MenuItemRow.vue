<script setup>
import { iconMarkup } from '@/Shared/serviceIcons';

defineProps({
    item: { type: Object, required: true },
    nested: { type: Boolean, default: false },
    first: { type: Boolean, default: false },
    last: { type: Boolean, default: false },
});
defineEmits(['move', 'toggle', 'edit', 'delete', 'add-child', 'add-services']);
</script>

<template>
    <div class="flex items-center gap-3 py-2.5 pr-3 sm:pr-5" :class="nested ? 'pl-10 sm:pl-14' : 'pl-3 sm:pl-5'">
        <div class="flex shrink-0 flex-col">
            <button class="rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-25" :disabled="first" aria-label="Move up" @click="$emit('move', 'up')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6" /></svg>
            </button>
            <button class="rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-25" :disabled="last" aria-label="Move down" @click="$emit('move', 'down')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
            </button>
        </div>

        <span v-if="item.type === 'service'" class="grid size-8 shrink-0 place-items-center rounded-full bg-sage-soft text-forest">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" v-html="iconMarkup(item.icon)" />
        </span>
        <span v-else class="grid size-8 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-500">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7" /></svg>
        </span>

        <button class="min-w-0 flex-1 text-left" @click="$emit('edit')">
            <span class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <span class="font-medium" :class="item.is_visible ? 'text-slate-800' : 'text-slate-400 line-through'">{{ item.display_label }}</span>
                <span v-if="!nested && item.children?.length" class="rounded-full bg-gold-soft px-2 py-0.5 text-[11px] font-medium text-forest-deep">
                    {{ item.dropdown_style === 'mega' ? 'Mega menu' : 'Dropdown' }} · {{ item.children.length }}
                </span>
                <span v-if="item.type === 'service'" class="rounded-full bg-sage-soft px-2 py-0.5 text-[11px] font-medium text-forest">Service page</span>
                <span v-if="item.open_in_new_tab" class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">New tab</span>
                <span v-if="!item.is_visible" class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">Hidden</span>
                <span v-if="item.service_unpublished" class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700">Service unpublished · not shown</span>
            </span>
            <span class="block truncate text-xs text-slate-400">{{ item.href || 'No link (opens its dropdown)' }}</span>
        </button>

        <div class="flex shrink-0 items-center gap-0.5">
            <template v-if="!nested">
                <button class="rounded-lg px-2 py-1.5 text-xs font-medium whitespace-nowrap text-forest transition hover:bg-sage-soft" title="Add a sub-item" @click="$emit('add-child')">+ Sub-item</button>
                <button class="rounded-lg px-2 py-1.5 text-xs font-medium whitespace-nowrap text-forest transition hover:bg-sage-soft" title="Add service pages under this item" @click="$emit('add-services')">+ Services</button>
            </template>
            <button class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100" :title="item.is_visible ? 'Hide' : 'Show'" @click="$emit('toggle')">
                <svg v-if="item.is_visible" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" /><circle cx="12" cy="12" r="3" /></svg>
                <svg v-else class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17.6 17.6 0 0 1-3.2 4.2M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.6" /></svg>
            </button>
            <button class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-forest" title="Edit" @click="$emit('edit')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" /></svg>
            </button>
            <button class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600" title="Delete" @click="$emit('delete')">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /></svg>
            </button>
        </div>
    </div>
</template>
