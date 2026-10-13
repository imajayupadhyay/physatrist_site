<script setup>
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import RepeaterEditor from '@/Admin/Components/Global/RepeaterEditor.vue';

const props = defineProps({
    form: { type: Object, required: true },
    publishedServices: { type: Number, required: true },
});

const linkFields = [
    { key: 'label', label: 'Link text', maxlength: 60 },
    { key: 'url', label: 'Link', maxlength: 255, placeholder: '/#about or https://…' },
    { key: 'new_tab', label: 'Open in a new tab', type: 'checkbox' },
];

const add = () => props.form.columns.push({ heading: '', type: 'links', limit: 6, links: [{ label: '', url: '', new_tab: false }] });
const removeAt = (i) => props.form.columns.splice(i, 1);
const move = (i, by) => {
    const cols = props.form.columns;
    const j = i + by;
    if (j < 0 || j >= cols.length) return;
    [cols[i], cols[j]] = [cols[j], cols[i]];
};
const err = (key) => props.form.errors[key];
</script>

<template>
    <FormSection id="columns" title="Link columns" description="Up to 3 columns between the brand column and the contact column, in this order.">
        <div v-for="(column, i) in form.columns" :key="i" class="rounded-xl border border-slate-200">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/70 px-4 py-2.5">
                <span class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Column {{ i + 1 }}</span>
                <div class="flex">
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-white hover:text-slate-600 disabled:opacity-30" :disabled="i === 0" aria-label="Move left" @click="move(i, -1)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" /></svg>
                    </button>
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-white hover:text-slate-600 disabled:opacity-30" :disabled="i === form.columns.length - 1" aria-label="Move right" @click="move(i, 1)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" /></svg>
                    </button>
                    <button type="button" class="rounded p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" :aria-label="`Remove column ${i + 1}`" @click="removeAt(i)">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-4 p-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label :for="`col-heading-${i}`" class="mb-1.5 block text-sm font-medium text-slate-700">Heading <span class="text-red-500">*</span></label>
                        <input
                            :id="`col-heading-${i}`"
                            v-model="column.heading"
                            maxlength="40"
                            class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10"
                            :class="err(`columns.${i}.heading`) ? 'border-red-400' : 'border-slate-300'"
                        />
                        <p v-if="err(`columns.${i}.heading`)" class="mt-1 text-sm text-red-600">{{ err(`columns.${i}.heading`) }}</p>
                    </div>
                    <div>
                        <p class="mb-1.5 text-sm font-medium text-slate-700">Content</p>
                        <div class="grid grid-cols-2 gap-1 rounded-lg bg-slate-100 p-1 text-sm">
                            <button type="button" class="rounded-md py-1.5 font-medium transition" :class="column.type === 'links' ? 'bg-white text-forest shadow-sm' : 'text-slate-500'" @click="column.type = 'links'">Custom links</button>
                            <button type="button" class="rounded-md py-1.5 font-medium transition" :class="column.type === 'services' ? 'bg-white text-forest shadow-sm' : 'text-slate-500'" @click="column.type = 'services'">Services list</button>
                        </div>
                    </div>
                </div>

                <div v-if="column.type === 'services'" class="flex flex-wrap items-center gap-3 rounded-lg bg-sage-soft/50 px-4 py-3 text-sm text-slate-600">
                    Shows the first
                    <input v-model.number="column.limit" type="number" min="1" max="12" class="w-16 rounded-md border border-slate-300 bg-white px-2 py-1 text-center" />
                    published services (of {{ publishedServices }}), in the order set under Services. Updates automatically.
                </div>
                <RepeaterEditor
                    v-else
                    v-model="column.links"
                    :fields="linkFields"
                    item-label="Link"
                    add-label="Add link"
                    :max="12"
                    :errors="form.errors"
                    :error-key="`columns.${i}.links`"
                    compact
                />
            </div>
        </div>

        <button
            v-if="form.columns.length < 3"
            type="button"
            class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-dashed border-slate-300 py-3 text-sm font-medium text-forest transition hover:border-forest hover:bg-sage-soft/50"
            @click="add"
        >
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
            Add column
        </button>
    </FormSection>
</template>
