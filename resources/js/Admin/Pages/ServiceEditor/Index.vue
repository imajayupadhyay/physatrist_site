<script setup>
import { computed, onBeforeUnmount, onMounted } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import ApproachSection from './Components/ApproachSection.vue';
import BasicsSection from './Components/BasicsSection.vue';
import CardSection from './Components/CardSection.vue';
import CtaSection from './Components/CtaSection.vue';
import EditorNav from './Components/EditorNav.vue';
import FaqsSection from './Components/FaqsSection.vue';
import HeroSection from './Components/HeroSection.vue';
import OverviewSection from './Components/OverviewSection.vue';
import SaveBar from './Components/SaveBar.vue';
import SeoSection from './Components/SeoSection.vue';

// Create (service = null) and edit share this page. One form, split into sections.
const props = defineProps({
    service: { type: Object, default: null },
    icons: { type: Array, required: true },
    siteUrl: { type: String, required: true },
});

const isEdit = computed(() => !!props.service);
const s = props.service ?? {};

const form = useForm({
    // Basics
    title: s.title ?? '',
    slug: s.slug ?? '',
    concern_label: s.concern_label ?? '',
    is_published: s.is_published ?? true,
    // Homepage card
    show_on_home: s.show_on_home ?? true,
    is_featured: s.is_featured ?? false,
    icon: s.icon ?? 'leaf',
    audience: s.audience ?? '',
    summary: s.summary ?? '',
    // Hero
    hero_eyebrow: s.hero_eyebrow ?? '',
    hero_title: s.hero_title ?? '',
    intro: s.intro ?? '',
    hero_image_file: null,
    remove_hero_image: false,
    // Overview
    overview_heading: s.overview_heading ?? '',
    overview_body: s.overview_body ?? '',
    issues_heading: s.issues_heading ?? '',
    issues: [...(s.issues ?? [])],
    // Approach
    approach_heading: s.approach_heading ?? '',
    approach: (s.approach ?? []).map((step) => ({ ...step })),
    // CTA
    cta_heading: s.cta_heading ?? '',
    cta_text: s.cta_text ?? '',
    // FAQs
    faqs: (s.faqs ?? []).map((faq) => ({ ...faq })),
    // SEO
    seo_title: s.seo_title ?? '',
    meta_description: s.meta_description ?? '',
    meta_keywords: s.meta_keywords ?? '',
    canonical_url: s.canonical_url ?? '',
    og_title: s.og_title ?? '',
    og_description: s.og_description ?? '',
    og_image_file: null,
    remove_og_image: false,
    robots_index: s.robots_index ?? true,
    robots_follow: s.robots_follow ?? true,
});

// Section menu + which form fields belong to each (for error badges).
const sections = [
    { id: 'basics', label: 'Basics', fields: ['title', 'slug', 'concern_label', 'is_published'] },
    { id: 'card', label: 'Homepage card', fields: ['show_on_home', 'is_featured', 'icon', 'audience', 'summary'] },
    { id: 'hero', label: 'Introduction', fields: ['hero_eyebrow', 'hero_title', 'intro', 'hero_image_file'] },
    { id: 'overview', label: 'About & issues', fields: ['overview_heading', 'overview_body', 'issues_heading', 'issues'] },
    { id: 'approach', label: 'How we help', fields: ['approach_heading', 'approach'] },
    { id: 'cta', label: 'Call to action', fields: ['cta_heading', 'cta_text'] },
    { id: 'faqs', label: 'FAQs', fields: ['faqs'] },
    { id: 'seo', label: 'SEO & sharing', fields: ['seo_title', 'meta_description', 'meta_keywords', 'canonical_url', 'og_title', 'og_description', 'og_image_file', 'robots_index', 'robots_follow'] },
];
const errorCount = (section) =>
    Object.keys(form.errors).filter((key) => section.fields.some((field) => key === field || key.startsWith(`${field}.`))).length;

const save = () => {
    const done = () => {
        form.hero_image_file = null;
        form.og_image_file = null;
        form.remove_hero_image = false;
        form.remove_og_image = false;
        form.defaults();
    };
    const scrollToError = () => {
        const first = sections.find((section) => errorCount(section));
        if (first) document.getElementById(first.id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    if (!isEdit.value) {
        form.transform((data) => data).post('/secure-admin/services', { onError: scrollToError });
        return;
    }

    const url = `/secure-admin/services/${props.service.id}`;
    const options = { preserveScroll: true, preserveState: true, onSuccess: done, onError: scrollToError };

    // Files need multipart, which PHP only parses for POST → spoof PUT.
    if (form.hero_image_file || form.og_image_file) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(url, { ...options, forceFormData: true });
    } else {
        form.transform((data) => data).put(url, options);
    }
};

// Warn before leaving with unsaved changes.
const beforeUnload = (e) => {
    if (form.isDirty) {
        e.preventDefault();
        e.returnValue = '';
    }
};
let removeRouterGuard;
onMounted(() => {
    window.addEventListener('beforeunload', beforeUnload);
    removeRouterGuard = router.on('before', (event) => {
        const visit = event.detail.visit;
        if (form.isDirty && !form.processing && visit.method === 'get' && !window.confirm('You have unsaved changes. Leave without saving?')) {
            event.preventDefault();
        }
    });
});
onBeforeUnmount(() => {
    window.removeEventListener('beforeunload', beforeUnload);
    removeRouterGuard?.();
});
</script>

<template>
    <Head :title="isEdit ? `Edit: ${service.title}` : 'New service'" />

    <AdminLayout :title="isEdit ? 'Edit service' : 'New service'">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <Link href="/secure-admin/services" class="text-sm font-medium text-slate-500 hover:text-forest">← All services</Link>
                <h2 class="mt-1 truncate text-xl font-semibold text-slate-800">{{ isEdit ? service.title : 'New service' }}</h2>
                <p v-if="isEdit" class="text-sm text-slate-500">Last saved {{ service.updated_at }}</p>
            </div>
            <a
                v-if="isEdit"
                :href="service.url"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                {{ service.is_published ? 'View page' : 'Preview draft' }}
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" /></svg>
            </a>
        </div>

        <div class="grid gap-6 pb-24 xl:grid-cols-[200px_1fr]">
            <EditorNav :sections="sections" :error-count="errorCount" />

            <form class="min-w-0 space-y-6" @submit.prevent="save">
                <BasicsSection :form="form" :service="service" :site-url="siteUrl" />
                <CardSection :form="form" :icons="icons" />
                <HeroSection :form="form" :service="service" />
                <OverviewSection :form="form" />
                <ApproachSection :form="form" />
                <CtaSection :form="form" />
                <FaqsSection :form="form" />
                <SeoSection :form="form" :service="service" :site-url="siteUrl" />

                <SaveBar :form="form" :is-edit="isEdit" />
            </form>
        </div>
    </AdminLayout>
</template>
