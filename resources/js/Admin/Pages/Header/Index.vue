<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import HeaderPreview from './Components/HeaderPreview.vue';
import HeaderSettingsForm from './Components/HeaderSettingsForm.vue';
import MenuEditor from './Components/MenuEditor.vue';

defineProps({
    settings: { type: Object, required: true },
    menu: { type: Array, required: true },
    services: { type: Array, required: true },
});

const tabs = [
    { key: 'menu', label: 'Menu' },
    { key: 'brand', label: 'Logo & button' },
    { key: 'mega', label: 'Mega menu panel' },
];
const tab = ref('menu');
</script>

<template>
    <Head title="Header" />

    <AdminLayout title="Header">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Website header</h2>
                <p class="mt-1 text-sm text-slate-500">Logo, menu (with dropdowns and mega menus) and the call-to-action button.</p>
            </div>
            <a href="/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                View website
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" /></svg>
            </a>
        </div>

        <HeaderPreview :settings="settings" :menu="menu" class="mb-6" />

        <div class="mb-6 inline-flex rounded-lg border border-slate-200 bg-white p-1 text-sm">
            <button
                v-for="t in tabs"
                :key="t.key"
                class="rounded-md px-4 py-2 font-medium transition"
                :class="tab === t.key ? 'bg-forest text-white shadow-sm' : 'text-slate-600 hover:text-slate-800'"
                @click="tab = t.key"
            >
                {{ t.label }}
            </button>
        </div>

        <!-- v-show goes on wrapper divs: MenuEditor has several root nodes (list + modals), where v-show has no effect. -->
        <div v-show="tab === 'menu'">
            <MenuEditor :menu="menu" :services="services" />
        </div>
        <!-- One settings form for both tabs; kept mounted so unsaved edits survive tab switches. -->
        <div v-show="tab !== 'menu'">
            <HeaderSettingsForm :settings="settings" :section="tab" />
        </div>
    </AdminLayout>
</template>
