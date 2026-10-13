<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, required: true },
});
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-3">
        <Link href="/secure-admin/leads?status=new" class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-gold/50 hover:shadow-sm">
            <p class="text-sm font-medium text-slate-500">New leads</p>
            <p class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-semibold text-slate-800">{{ stats.new }}</span>
                <span v-if="stats.new" class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">awaiting contact</span>
            </p>
        </Link>
        <Link href="/secure-admin/leads" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-gold/50 hover:shadow-sm">
            <p class="text-sm font-medium text-slate-500">This week</p>
            <p class="mt-2 text-3xl font-semibold text-slate-800">{{ stats.thisWeek }}</p>
        </Link>
        <Link href="/secure-admin/leads" class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-gold/50 hover:shadow-sm">
            <p class="text-sm font-medium text-slate-500">All-time requests</p>
            <p class="mt-2 text-3xl font-semibold text-slate-800">{{ stats.total }}</p>
        </Link>
    </div>
</template>
