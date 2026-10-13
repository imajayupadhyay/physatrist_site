<script setup>
import LeadStatusBadge from '@/Admin/Components/Global/LeadStatusBadge.vue';
import Pagination from '@/Admin/Components/Global/Pagination.vue';

defineProps({
    leads: { type: Object, required: true },
    statuses: { type: Object, required: true },
    filtered: { type: Boolean, default: false },
});
defineEmits(['open']);
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-medium tracking-wide text-slate-500 uppercase">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="hidden px-5 py-3 lg:table-cell">Concern</th>
                    <th class="hidden px-5 py-3 md:table-cell">Preferred</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="hidden px-5 py-3 sm:table-cell">Received</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr
                    v-for="lead in leads.data"
                    :key="lead.id"
                    class="cursor-pointer transition hover:bg-slate-50"
                    :class="lead.status === 'new' && 'bg-amber-50/30'"
                    tabindex="0"
                    @click="$emit('open', lead)"
                    @keydown.enter="$emit('open', lead)"
                >
                    <td class="px-5 py-3.5">
                        <p class="font-medium" :class="lead.status === 'new' ? 'text-slate-900' : 'text-slate-700'">{{ lead.name }}</p>
                        <p class="text-slate-500">{{ lead.phone }}</p>
                        <p class="mt-0.5 text-xs text-slate-400 lg:hidden">{{ lead.concern }}</p>
                    </td>
                    <td class="hidden px-5 py-3.5 text-slate-600 lg:table-cell">{{ lead.concern }}</td>
                    <td class="hidden px-5 py-3.5 md:table-cell">
                        <p class="whitespace-nowrap text-slate-600">{{ lead.preferred_date ?? 'Any date' }}</p>
                        <p class="text-xs text-slate-400">{{ lead.mode }}</p>
                    </td>
                    <td class="px-5 py-3.5"><LeadStatusBadge :status="lead.status" :label="statuses[lead.status]" /></td>
                    <td class="hidden px-5 py-3.5 whitespace-nowrap sm:table-cell">
                        <p class="text-slate-600">{{ lead.created_ago }}</p>
                        <p class="text-xs text-slate-400">{{ lead.created_at }}</p>
                    </td>
                </tr>
                <tr v-if="!leads.data.length">
                    <td colspan="5" class="px-5 py-16 text-center">
                        <p class="font-medium text-slate-700">{{ filtered ? 'No leads match these filters.' : 'No leads yet.' }}</p>
                        <p v-if="!filtered" class="mt-1 text-sm text-slate-500">Requests from the website's appointment form will appear here.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 p-4 sm:px-5">
        <Pagination :paginator="leads" />
    </div>
</template>
