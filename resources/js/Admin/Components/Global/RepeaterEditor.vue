<script setup>
// Editable list of small records, e.g. FAQs ({ question, answer }) or steps ({ title, text }).
// fields: [{ key, label, type: 'text' | 'textarea' | 'checkbox', placeholder?, maxlength? }]
const items = defineModel({ type: Array, default: () => [] });

const props = defineProps({
    fields: { type: Array, required: true },
    itemLabel: { type: String, default: 'Item' },
    addLabel: { type: String, default: 'Add item' },
    max: { type: Number, default: 20 },
    errors: { type: Object, default: () => ({}) },
    errorKey: { type: String, required: true },
    // Lay text fields out side by side (checkboxes span the full width).
    compact: { type: Boolean, default: false },
});

const add = () => items.value.push(Object.fromEntries(props.fields.map((f) => [f.key, f.type === 'checkbox' ? false : ''])));
const removeAt = (i) => items.value.splice(i, 1);
const move = (i, by) => {
    const j = i + by;
    if (j < 0 || j >= items.value.length) return;
    [items.value[i], items.value[j]] = [items.value[j], items.value[i]];
};
const err = (i, key) => props.errors[`${props.errorKey}.${i}.${key}`];
</script>

<template>
    <div class="space-y-3">
        <div v-for="(item, i) in items" :key="i" class="rounded-xl border border-slate-200 bg-slate-50/60" :class="compact ? 'px-4 pt-2 pb-3' : 'p-4'">
            <div class="flex items-center justify-between" :class="compact ? 'mb-1' : 'mb-3'">
                <span class="text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ itemLabel }} {{ i + 1 }}</span>
                <div class="flex">
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-white hover:text-slate-600 disabled:opacity-30" :disabled="i === 0" aria-label="Move up" @click="move(i, -1)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6" /></svg>
                    </button>
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-white hover:text-slate-600 disabled:opacity-30" :disabled="i === items.length - 1" aria-label="Move down" @click="move(i, 1)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" :aria-label="`Remove ${itemLabel} ${i + 1}`" @click="removeAt(i)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /></svg>
                    </button>
                </div>
            </div>
            <div :class="compact ? 'grid gap-3 sm:grid-cols-2' : 'space-y-3'">
                <div v-for="field in fields" :key="field.key" :class="compact && field.type === 'checkbox' && 'sm:col-span-2'">
                    <label v-if="field.type === 'checkbox'" class="flex items-center gap-2 text-sm text-slate-600">
                        <input v-model="item[field.key]" type="checkbox" class="size-4 accent-forest" />
                        {{ field.label }}
                    </label>
                    <label v-else class="mb-1 block text-xs font-medium text-slate-600">{{ field.label }}</label>
                    <textarea
                        v-if="field.type === 'textarea'"
                        v-model="item[field.key]"
                        rows="3"
                        :maxlength="field.maxlength"
                        :placeholder="field.placeholder"
                        class="w-full rounded-lg border bg-white px-3.5 py-2 text-sm leading-relaxed transition outline-none focus:border-forest focus:ring-4 focus:ring-forest/10"
                        :class="err(i, field.key) ? 'border-red-400' : 'border-slate-300'"
                    />
                    <input
                        v-else-if="field.type !== 'checkbox'"
                        v-model="item[field.key]"
                        :maxlength="field.maxlength"
                        :placeholder="field.placeholder"
                        class="w-full rounded-lg border bg-white px-3.5 py-2 text-sm transition outline-none focus:border-forest focus:ring-4 focus:ring-forest/10"
                        :class="err(i, field.key) ? 'border-red-400' : 'border-slate-300'"
                    />
                    <p v-if="err(i, field.key)" class="mt-1 text-sm text-red-600">{{ err(i, field.key) }}</p>
                </div>
            </div>
        </div>

        <button
            v-if="items.length < max"
            type="button"
            class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-dashed border-slate-300 py-3 text-sm font-medium text-forest transition hover:border-forest hover:bg-sage-soft/50"
            @click="add"
        >
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
            {{ addLabel }}
        </button>
    </div>
</template>
