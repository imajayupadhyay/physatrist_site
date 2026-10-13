<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ListEditor from '@/Admin/Components/Global/ListEditor.vue';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import { iconMarkup } from '@/Shared/serviceIcons';

// Homepage "Areas of practice": the text around the service cards.
const props = defineProps({
    section: { type: Object, required: true },
    cards: { type: Array, required: true },
});

const form = useForm({
    eyebrow: props.section.eyebrow ?? '',
    heading: props.section.heading ?? '',
    lead: props.section.lead ?? '',
    card_link_label: props.section.card_link_label ?? 'Learn more',
    also_label: props.section.also_label ?? '',
    also_items: [...(props.section.also_items ?? [])],
    notes: [...(props.section.notes ?? [])],
});

const save = () => form.put('/secure-admin/services/homepage', { preserveScroll: true, preserveState: true, onSuccess: () => form.defaults() });
</script>

<template>
    <Head title="Homepage services section" />

    <AdminLayout title="Homepage services section">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <Link href="/secure-admin/services" class="text-sm font-medium text-slate-500 hover:text-forest">← All services</Link>
                <h2 class="mt-1 text-xl font-semibold text-slate-800">Homepage: “Areas of practice”</h2>
                <p class="mt-1 text-sm text-slate-500">The heading, intro and lists around the service cards on the homepage.</p>
            </div>
            <a href="/#services" target="_blank" rel="noopener" class="inline-flex items-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                View on homepage
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" /></svg>
            </a>
        </div>

        <form class="grid gap-6 pb-24 xl:grid-cols-[1fr_320px]" @submit.prevent="save">
            <div class="min-w-0 space-y-6">
                <FormSection id="heading" title="Section heading">
                    <FormInput id="eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
                    <FormInput id="heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="150" required />
                    <FormTextarea id="lead" v-model="form.lead" label="Intro text" :rows="3" :error="form.errors.lead" :maxlength="600" />
                    <FormInput id="card_link_label" v-model="form.card_link_label" label="Card link text" :error="form.errors.card_link_label" :maxlength="40" required hint="Shown at the bottom of every card, followed by →." />
                </FormSection>

                <FormSection id="below" title="Below the cards" description="Leave a list empty to hide it.">
                    <FormInput id="also_label" v-model="form.also_label" label="Label for the extra list" :error="form.errors.also_label" :maxlength="80" />
                    <ListEditor v-model="form.also_items" label="Also within our care (tags)" placeholder="e.g. Anger Management" add-label="Add tag" :errors="form.errors" error-key="also_items" />
                    <ListEditor v-model="form.notes" label="Notes (with tick icons)" placeholder="e.g. Sessions in Hindi & English" add-label="Add note" :max="6" :errors="form.errors" error-key="notes" />
                </FormSection>
            </div>

            <aside class="xl:sticky xl:top-24 xl:self-start">
                <div class="rounded-2xl border border-slate-200 bg-white">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h3 class="font-semibold text-slate-800">Cards on the homepage</h3>
                        <p class="mt-0.5 text-sm text-slate-500">In display order.</p>
                    </div>
                    <ol class="divide-y divide-slate-100">
                        <li v-for="card in cards" :key="card.id" class="flex items-center gap-3 px-5 py-2.5 text-sm">
                            <span class="grid size-8 shrink-0 place-items-center rounded-full" :class="card.is_featured ? 'bg-forest text-gold-soft' : 'bg-sage-soft text-forest'">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" v-html="iconMarkup(card.icon)" />
                            </span>
                            <Link :href="`/secure-admin/services/${card.id}/edit`" class="truncate text-slate-700 hover:text-forest">{{ card.title }}</Link>
                        </li>
                        <li v-if="!cards.length" class="px-5 py-6 text-center text-sm text-slate-500">No services are shown on the homepage.</li>
                    </ol>
                    <div class="border-t border-slate-100 px-5 py-3">
                        <Link href="/secure-admin/services" class="text-sm font-medium text-forest hover:text-gold">Reorder or show/hide cards →</Link>
                    </div>
                </div>
            </aside>

            <div class="fixed inset-x-0 bottom-0 z-20 border-t border-slate-200 bg-white/95 backdrop-blur lg:left-64">
                <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                    <p class="text-sm" :class="form.isDirty ? 'text-amber-600' : 'text-slate-400'">{{ form.isDirty ? 'You have unsaved changes.' : 'All changes saved.' }}</p>
                    <button type="submit" :disabled="form.processing || !form.isDirty" class="rounded-lg bg-forest px-5 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                        {{ form.processing ? 'Saving…' : 'Save changes' }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
