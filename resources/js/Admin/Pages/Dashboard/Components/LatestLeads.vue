<script setup>
import { Link } from '@inertiajs/vue3';
import LeadStatusBadge from '@/Admin/Components/Global/LeadStatusBadge.vue';

defineProps({
    leads: { type: Array, required: true },
    statuses: { type: Object, required: true },
});
</script>

<template>
    <section class="rounded-2xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-800">Latest appointment requests</h3>
            <Link href="/secure-admin/leads" class="text-sm font-medium text-forest hover:text-gold">View all →</Link>
        </div>

        <ul v-if="leads.length" class="divide-y divide-slate-100">
            <li v-for="lead in leads" :key="lead.id" class="flex items-center gap-4 px-5 py-3.5">
                <div class="min-w-0 flex-1">
                    <p class="truncate font-medium text-slate-800">{{ lead.name }} <span class="font-normal text-slate-400">· {{ lead.phone }}</span></p>
                    <p class="truncate text-sm text-slate-500">{{ lead.concern }} · {{ lead.mode }}</p>
                </div>
                <span class="hidden text-sm whitespace-nowrap text-slate-400 sm:block">{{ lead.created_ago }}</span>
                <LeadStatusBadge :status="lead.status" :label="statuses[lead.status]" />
            </li>
        </ul>
        <p v-else class="px-5 py-10 text-center text-sm text-slate-500">No appointment requests yet. They'll show up here as soon as someone submits the website form.</p>
    </section>
</template>
