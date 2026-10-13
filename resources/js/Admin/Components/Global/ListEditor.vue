<script setup>
// Editable list of short texts (add / remove / reorder).
const items = defineModel({ type: Array, default: () => [] });

defineProps({
    label: { type: String, required: true },
    placeholder: { type: String, default: '' },
    addLabel: { type: String, default: 'Add item' },
    max: { type: Number, default: 20 },
    errors: { type: Object, default: () => ({}) },
    errorKey: { type: String, default: '' },
});

const add = () => items.value.push('');
const removeAt = (i) => items.value.splice(i, 1);
const move = (i, by) => {
    const j = i + by;
    if (j < 0 || j >= items.value.length) return;
    [items.value[i], items.value[j]] = [items.value[j], items.value[i]];
};
</script>

<template>
    <div>
        <p class="mb-1.5 text-sm font-medium text-slate-700">{{ label }}</p>
        <ul class="space-y-2">
            <li v-for="(item, i) in items" :key="i">
                <div class="flex items-center gap-2">
                    <input
                        v-model="items[i]"
                        :placeholder="placeholder"
                        class="w-full min-w-0 rounded-lg border bg-white px-3.5 py-2 text-sm transition outline-none focus:border-forest focus:ring-4 focus:ring-forest/10"
                        :class="errors[`${errorKey}.${i}`] ? 'border-red-400' : 'border-slate-300'"
                    />
                    <div class="flex shrink-0">
                        <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 disabled:opacity-30" :disabled="i === 0" aria-label="Move up" @click="move(i, -1)">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6" /></svg>
                        </button>
                        <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 disabled:opacity-30" :disabled="i === items.length - 1" aria-label="Move down" @click="move(i, 1)">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
                        </button>
                        <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" aria-label="Remove" @click="removeAt(i)">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18" /></svg>
                        </button>
                    </div>
                </div>
                <p v-if="errors[`${errorKey}.${i}`]" class="mt-1 text-sm text-red-600">{{ errors[`${errorKey}.${i}`] }}</p>
            </li>
        </ul>
        <button
            v-if="items.length < max"
            type="button"
            class="mt-2 inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-forest transition hover:bg-sage-soft"
            @click="add"
        >
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
            {{ addLabel }}
        </button>
    </div>
</template>
