<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { initials as toInitials } from '@/Admin/Utils/initials';

defineProps({
    title: { type: String, default: '' },
});
defineEmits(['toggle-sidebar']);

const admin = computed(() => usePage().props.auth.admin);
const initials = computed(() => toInitials(admin.value?.name));

const menuOpen = ref(false);
</script>

<template>
    <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6">
        <button class="-ml-1 rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Open menu" @click="$emit('toggle-sidebar')">
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h10" /></svg>
        </button>

        <h1 class="text-lg font-semibold text-slate-800">{{ title }}</h1>

        <div class="relative ml-auto">
            <button class="flex items-center gap-3 rounded-full py-1 pr-2 pl-1 transition hover:bg-slate-100" :aria-expanded="menuOpen" @click="menuOpen = !menuOpen">
                <span class="grid size-9 place-items-center rounded-full bg-forest text-sm font-semibold text-white">{{ initials }}</span>
                <span class="hidden text-left leading-tight sm:block">
                    <span class="block text-sm font-medium text-slate-800">{{ admin?.name }}</span>
                    <span class="block text-xs text-slate-400">{{ admin?.email }}</span>
                </span>
                <svg class="size-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
            </button>

            <div v-if="menuOpen" class="fixed inset-0 z-10" @click="menuOpen = false" />
            <div v-if="menuOpen" class="absolute right-0 z-20 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                <div class="border-b border-slate-100 px-4 py-3 sm:hidden">
                    <p class="text-sm font-medium text-slate-800">{{ admin?.name }}</p>
                    <p class="text-xs text-slate-400">{{ admin?.email }}</p>
                </div>
                <Link
                    href="/secure-admin/logout"
                    method="post"
                    as="button"
                    class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-slate-600 hover:bg-slate-50 hover:text-red-600"
                >
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" /></svg>
                    Log out
                </Link>
            </div>
        </div>
    </header>
</template>
