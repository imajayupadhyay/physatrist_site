<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AdminBrand from './AdminBrand.vue';

defineProps({
    open: { type: Boolean, default: false },
});
defineEmits(['close']);

// Add new admin sections here. `icon` is the inner markup of a 24×24 stroked SVG.
const items = [
    {
        label: 'Dashboard',
        href: '/secure-admin/dashboard',
        icon: '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
    },
    {
        label: 'Home page',
        href: '/secure-admin/home',
        icon: '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
    },
    {
        label: 'Header',
        href: '/secure-admin/header',
        icon: '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M7 6h.01M10 6h4"/>',
    },
    {
        label: 'Footer',
        href: '/secure-admin/footer',
        icon: '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 15h18M7 18h.01M10 18h4"/>',
    },
    {
        label: 'Services',
        href: '/secure-admin/services',
        icon: '<path d="M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1z"/><path d="M4 9h16M9 9v11"/>',
    },
    {
        label: 'Leads',
        href: '/secure-admin/leads',
        badge: 'newLeadsCount', // shared prop name holding the count
        icon: '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.8 4H7.2a2 2 0 0 0-1.7 1.1z"/>',
    },
    {
        label: 'Users',
        href: '/secure-admin/users',
        icon: '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18.5 13.9c1.8.9 3 2.8 3 5.1"/>',
    },
];

const page = usePage();
const isActive = (href) => page.url.startsWith(href);
</script>

<template>
    <!-- Mobile backdrop -->
    <div v-if="open" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" @click="$emit('close')" />

    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-forest-deep transition-transform duration-200 lg:translate-x-0"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 items-center border-b border-white/10 px-5">
            <AdminBrand dark />
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
            <p class="px-3 pb-2 text-[11px] font-medium tracking-[0.18em] text-white/40 uppercase">Menu</p>
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                :class="isActive(item.href) ? 'bg-white/10 text-white' : 'text-white/65 hover:bg-white/5 hover:text-white'"
                @click="$emit('close')"
            >
                <svg
                    class="size-5 shrink-0"
                    :class="isActive(item.href) ? 'text-gold' : ''"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    v-html="item.icon"
                />
                {{ item.label }}
                <span
                    v-if="item.badge && page.props[item.badge]"
                    class="ml-auto rounded-full bg-gold px-2 py-0.5 text-[11px] font-semibold text-white"
                    :title="`${page.props[item.badge]} new`"
                >
                    {{ page.props[item.badge] }}
                </span>
            </Link>
        </nav>

        <div class="border-t border-white/10 p-3">
            <a href="/" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/65 transition hover:bg-white/5 hover:text-white">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" /></svg>
                View website
            </a>
        </div>
    </aside>
</template>
