<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import BottomSection from './Components/BottomSection.vue';
import BrandSection from './Components/BrandSection.vue';
import ColumnsEditor from './Components/ColumnsEditor.vue';
import ContactSection from './Components/ContactSection.vue';
import SocialSection from './Components/SocialSection.vue';

// Footer content + site-wide contact details. One form, split into tabs.
const props = defineProps({
    footer: { type: Object, required: true },
    contact: { type: Object, required: true },
    publishedServices: { type: Number, required: true },
});

const f = props.footer;
const c = props.contact;
const form = useForm({
    logo_file: null,
    remove_logo: false,
    logo_alt: f.logo_alt ?? '',
    logo_height: f.logo_height ?? 46,
    brand_name: f.brand_name ?? '',
    brand_tagline: f.brand_tagline ?? '',
    about_text: f.about_text ?? '',
    credentials_name: f.credentials_name ?? '',
    credentials_text: f.credentials_text ?? '',
    show_socials: f.show_socials ?? true,
    columns: (f.columns ?? []).map((column) => ({ ...column, limit: column.limit ?? 6, links: (column.links ?? []).map((link) => ({ ...link })) })),
    contact_heading: f.contact_heading ?? '',
    show_website: f.show_website ?? true,
    show_hours: f.show_hours ?? true,
    helpline_visible: f.helpline_visible ?? true,
    helpline_text: f.helpline_text ?? '',
    copyright_text: f.copyright_text ?? '',
    bottom_text: f.bottom_text ?? '',
    contact: {
        email: c.email ?? '',
        phones: [...(c.phones ?? [])],
        whatsapp_number: c.whatsapp_number ?? '',
        address: c.address ?? '',
        maps_url: c.maps_url ?? '',
        website_url: c.website_url ?? '',
        hours: c.hours ?? '',
        hours_short: c.hours_short ?? '',
        instagram: c.instagram ?? '',
        youtube: c.youtube ?? '',
        facebook: c.facebook ?? '',
        linkedin: c.linkedin ?? '',
    },
});

const tabs = [
    { key: 'content', label: 'Content & columns', prefixes: ['logo', 'brand', 'about', 'credentials', 'columns', 'show_socials'] },
    { key: 'contact', label: 'Contact & social', prefixes: ['contact.', 'contact_heading', 'show_website', 'show_hours'] },
    { key: 'bottom', label: 'Helpline & bottom bar', prefixes: ['helpline', 'copyright', 'bottom'] },
];
const tab = ref('content');
const errorCount = (t) => Object.keys(form.errors).filter((key) => t.prefixes.some((p) => key.startsWith(p))).length;

const save = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.logo_file = null;
            form.remove_logo = false;
            form.defaults();
        },
        onError: () => {
            const first = tabs.find((t) => errorCount(t));
            if (first) tab.value = first.key;
        },
    };

    // Files need multipart, which PHP only parses for POST → spoof PUT.
    if (form.logo_file) {
        form.transform((data) => ({ ...data, _method: 'put' })).post('/secure-admin/footer', { ...options, forceFormData: true });
    } else {
        form.transform((data) => data).put('/secure-admin/footer', options);
    }
};
</script>

<template>
    <Head title="Footer" />

    <AdminLayout title="Footer">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Website footer</h2>
                <p class="mt-1 text-sm text-slate-500">Footer content, link columns and the clinic’s contact details.</p>
            </div>
            <a href="/#contact" target="_blank" rel="noopener" class="inline-flex items-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                View website
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" /></svg>
            </a>
        </div>

        <div class="mb-6 inline-flex flex-wrap rounded-lg border border-slate-200 bg-white p-1 text-sm">
            <button
                v-for="t in tabs"
                :key="t.key"
                type="button"
                class="flex items-center gap-2 rounded-md px-4 py-2 font-medium transition"
                :class="tab === t.key ? 'bg-forest text-white shadow-sm' : 'text-slate-600 hover:text-slate-800'"
                @click="tab = t.key"
            >
                {{ t.label }}
                <span v-if="errorCount(t)" class="rounded-full bg-red-100 px-1.5 text-[11px] font-semibold text-red-700">{{ errorCount(t) }}</span>
            </button>
        </div>

        <form class="pb-24" @submit.prevent="save">
            <!-- Plain divs carry v-show (it doesn't work on multi-root components). -->
            <div v-show="tab === 'content'" class="space-y-6">
                <BrandSection :form="form" :logo-url="footer.logo_url" />
                <ColumnsEditor :form="form" :published-services="publishedServices" />
            </div>
            <div v-show="tab === 'contact'" class="space-y-6">
                <ContactSection :form="form" />
                <SocialSection :form="form" />
            </div>
            <div v-show="tab === 'bottom'" class="space-y-6">
                <BottomSection :form="form" />
            </div>

            <div class="fixed inset-x-0 bottom-0 z-20 border-t border-slate-200 bg-white/95 backdrop-blur lg:left-64">
                <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                    <p class="text-sm" :class="form.hasErrors ? 'text-red-600' : form.isDirty ? 'text-amber-600' : 'text-slate-400'">
                        {{ form.hasErrors ? 'Please fix the highlighted fields (see the red badges on the tabs).' : form.isDirty ? 'You have unsaved changes.' : 'All changes saved.' }}
                    </p>
                    <div class="flex gap-2">
                        <button v-if="form.isDirty" type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="form.reset(); form.clearErrors()">Discard</button>
                        <button type="submit" :disabled="form.processing || !form.isDirty" class="rounded-lg bg-forest px-5 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                            {{ form.processing ? 'Saving…' : 'Save footer' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
