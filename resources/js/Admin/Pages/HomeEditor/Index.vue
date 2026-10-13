<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import SectionForm from './Components/SectionForm.vue';
import AboutEditor from './Components/Sections/AboutEditor.vue';
import AssessmentEditor from './Components/Sections/AssessmentEditor.vue';
import FaqEditor from './Components/Sections/FaqEditor.vue';
import HeroEditor from './Components/Sections/HeroEditor.vue';
import SeoEditor from './Components/Sections/SeoEditor.vue';
import SpaceEditor from './Components/Sections/SpaceEditor.vue';
import StepsEditor from './Components/Sections/StepsEditor.vue';
import TestimonialsEditor from './Components/Sections/TestimonialsEditor.vue';
import TrustEditor from './Components/Sections/TrustEditor.vue';
import VideosEditor from './Components/Sections/VideosEditor.vue';

// Homepage editor: pick a section on the left, edit and save it on the right (each section saves on its own).
const props = defineProps({
    sections: { type: Object, required: true }, // key => label
    content: { type: Object, required: true }, // key => saved content
    current: { type: String, required: true },
});

const editors = {
    hero: HeroEditor,
    trust: TrustEditor,
    about: AboutEditor,
    assessment: AssessmentEditor,
    steps: StepsEditor,
    space: SpaceEditor,
    videos: VideosEditor,
    testimonials: TestimonialsEditor,
    faq: FaqEditor,
    seo: SeoEditor,
};

const current = ref(props.current);
const dirty = ref(false);
const version = ref(0); // bumped after a save → the form reloads from the fresh content

const select = (key) => {
    if (key === current.value) return;
    if (dirty.value && !window.confirm('You have unsaved changes in this section. Switch without saving?')) return;
    current.value = key;
    dirty.value = false;
    const url = new URL(window.location.href);
    url.searchParams.set('section', key);
    window.history.replaceState(window.history.state, '', url);
};

// Shown in the menu but managed elsewhere.
const elsewhere = [
    { label: 'Services section', href: '/secure-admin/services/homepage', note: 'Services' },
    { label: 'Booking form', href: '/secure-admin/footer', note: 'Contact details' },
];
</script>

<template>
    <Head title="Home page" />

    <AdminLayout title="Home page">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Home page</h2>
                <p class="mt-1 text-sm text-slate-500">Edit the homepage one section at a time. Each section is saved separately.</p>
            </div>
            <a href="/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                View homepage
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" /></svg>
            </a>
        </div>

        <div class="grid gap-6 pb-24 xl:grid-cols-[240px_1fr]">
            <!-- Section menu -->
            <nav class="xl:sticky xl:top-24 xl:self-start" aria-label="Homepage sections">
                <ul class="flex gap-1 overflow-x-auto pb-1 xl:flex-col xl:overflow-visible xl:rounded-2xl xl:border xl:border-slate-200 xl:bg-white xl:p-2">
                    <li v-for="(label, key) in sections" :key="key" class="shrink-0">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-left text-sm font-medium whitespace-nowrap transition"
                            :class="current === key ? 'bg-forest text-white' : 'text-slate-600 hover:bg-slate-100'"
                            @click="select(key)"
                        >
                            {{ label }}
                            <span v-if="content[key].visible === false" class="rounded-full px-1.5 py-0.5 text-[10px] font-semibold uppercase" :class="current === key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'">Hidden</span>
                            <span v-else-if="current === key && dirty" class="size-2 rounded-full bg-gold" title="Unsaved changes" />
                        </button>
                    </li>
                    <li class="hidden px-3 pt-3 pb-1 text-[11px] font-medium tracking-[0.18em] text-slate-400 uppercase xl:block">Managed elsewhere</li>
                    <li v-for="item in elsewhere" :key="item.href" class="shrink-0">
                        <Link :href="item.href" class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-sm whitespace-nowrap text-slate-500 transition hover:bg-slate-100 hover:text-forest">
                            {{ item.label }}
                            <span class="text-xs text-slate-400">{{ item.note }} →</span>
                        </Link>
                    </li>
                </ul>
            </nav>

            <SectionForm
                :key="`${current}-${version}`"
                v-slot="{ form }"
                :section="current"
                :label="sections[current]"
                :initial="content[current]"
                @dirty="dirty = $event"
                @saved="version++; dirty = false"
            >
                <component :is="editors[current]" :form="form" />
            </SectionForm>
        </div>
    </AdminLayout>
</template>
