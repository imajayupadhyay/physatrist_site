<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const admin = computed(() => usePage().props.auth.admin);

const hour = new Date().getHours();
const greeting = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';
const today = new Date().toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
</script>

<template>
    <section class="relative overflow-hidden rounded-2xl bg-forest p-6 text-white shadow-sm sm:p-8">
        <!-- decorative rings -->
        <div class="pointer-events-none absolute -top-16 -right-16 size-56 rounded-full border-[28px] border-white/5" />
        <div class="pointer-events-none absolute -right-4 -bottom-24 size-48 rounded-full bg-gold/15" />

        <div class="relative">
            <p class="text-sm font-medium text-gold-soft">{{ today }}</p>
            <h2 class="mt-2 text-2xl font-semibold sm:text-3xl">{{ greeting }}, {{ admin?.name }} 👋</h2>
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-white/75 sm:text-base">
                Welcome to the Mind Essence admin panel. This is where you'll manage your website's content and appointments.
            </p>
            <a href="/" target="_blank" rel="noopener" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-forest-deep transition hover:bg-gold-soft">
                View website
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
            </a>
        </div>
    </section>
</template>
