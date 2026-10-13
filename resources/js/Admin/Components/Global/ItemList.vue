<script setup>
import { ref } from 'vue';

// Collapsible list of records with custom fields (slot), e.g. slides or videos.
// <ItemList v-model="form.slides" :new-item="() => ({…})" title-key="title" v-slot="{ item, index }">…fields…</ItemList>
const items = defineModel({ type: Array, default: () => [] });

const props = defineProps({
    newItem: { type: Function, required: true },
    itemLabel: { type: String, default: 'Item' },
    addLabel: { type: String, default: 'Add item' },
    titleKey: { type: String, default: '' }, // field shown in the collapsed header
    min: { type: Number, default: 0 },
    max: { type: Number, default: 20 },
    fixed: { type: Boolean, default: false }, // no add/remove/reorder (e.g. exactly 4 photos)
    errors: { type: Object, default: () => ({}) },
    errorKey: { type: String, required: true },
});

const open = ref(new Set([0]));
const toggle = (i) => (open.value.has(i) ? open.value.delete(i) : open.value.add(i));

const add = () => {
    items.value.push(props.newItem());
    open.value.add(items.value.length - 1);
};
const removeAt = (i) => {
    items.value.splice(i, 1);
    open.value = new Set([...open.value].filter((j) => j !== i).map((j) => (j > i ? j - 1 : j)));
};
const move = (i, by) => {
    const j = i + by;
    if (j < 0 || j >= items.value.length) return;
    [items.value[i], items.value[j]] = [items.value[j], items.value[i]];
    const wasI = open.value.has(i);
    const wasJ = open.value.has(j);
    open.value.delete(i);
    open.value.delete(j);
    if (wasI) open.value.add(j);
    if (wasJ) open.value.add(i);
};
const errorsIn = (i) => Object.keys(props.errors).filter((key) => key.startsWith(`${props.errorKey}.${i}.`)).length;
</script>

<template>
    <div class="space-y-3">
        <div v-for="(item, i) in items" :key="i" class="overflow-hidden rounded-xl border" :class="errorsIn(i) ? 'border-red-300' : 'border-slate-200'">
            <div class="flex items-center gap-2 bg-slate-50/80 px-3 py-2">
                <button type="button" class="flex min-w-0 flex-1 items-center gap-2 text-left" :aria-expanded="open.has(i)" @click="toggle(i)">
                    <svg class="size-4 shrink-0 text-slate-400 transition" :class="open.has(i) && 'rotate-90'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" /></svg>
                    <span class="shrink-0 text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ itemLabel }} {{ i + 1 }}</span>
                    <span v-if="titleKey && item[titleKey]" class="truncate text-sm text-slate-700">· {{ item[titleKey] }}</span>
                    <span v-if="errorsIn(i)" class="shrink-0 rounded-full bg-red-100 px-1.5 text-[11px] font-semibold text-red-700">{{ errorsIn(i) }}</span>
                </button>
                <div v-if="!fixed" class="flex shrink-0">
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-white hover:text-slate-600 disabled:opacity-30" :disabled="i === 0" aria-label="Move up" @click="move(i, -1)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6" /></svg>
                    </button>
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-white hover:text-slate-600 disabled:opacity-30" :disabled="i === items.length - 1" aria-label="Move down" @click="move(i, 1)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 disabled:opacity-30" :disabled="items.length <= min" :aria-label="`Remove ${itemLabel} ${i + 1}`" @click="removeAt(i)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /></svg>
                    </button>
                </div>
            </div>
            <div v-show="open.has(i)" class="space-y-4 border-t border-slate-100 p-4">
                <slot :item="item" :index="i" />
            </div>
        </div>

        <button
            v-if="!fixed && items.length < max"
            type="button"
            class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-dashed border-slate-300 py-3 text-sm font-medium text-forest transition hover:border-forest hover:bg-sage-soft/50"
            @click="add"
        >
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
            {{ addLabel }}
        </button>
    </div>
</template>
