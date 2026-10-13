<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import DeleteLeadModal from './Components/DeleteLeadModal.vue';
import LeadDetailModal from './Components/LeadDetailModal.vue';
import LeadFilters from './Components/LeadFilters.vue';
import LeadsTable from './Components/LeadsTable.vue';

const props = defineProps({
    leads: { type: Object, required: true },
    filters: { type: Object, required: true },
    counts: { type: Object, required: true },
    statuses: { type: Object, required: true },
    concerns: { type: Array, required: true },
});

// Keep the open lead in sync with fresh props after an update.
const openId = ref(null);
const openLead = computed(() => props.leads.data.find((lead) => lead.id === openId.value) ?? null);

const deleting = ref(null);
const askDelete = (lead) => {
    openId.value = null;
    deleting.value = lead;
};
</script>

<template>
    <Head title="Leads" />

    <AdminLayout title="Leads">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-800">Leads</h2>
            <p class="mt-1 text-sm text-slate-500">Appointment requests submitted through the website's “Book an appointment” form.</p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <LeadFilters :filters="filters" :counts="counts" :statuses="statuses" :concerns="concerns" />
            <LeadsTable :leads="leads" :statuses="statuses" :filtered="!!(filters.search || filters.status || filters.concern)" @open="openId = $event.id" />
        </div>

        <LeadDetailModal :lead="openLead" :statuses="statuses" @close="openId = null" @delete="askDelete" />
        <DeleteLeadModal :lead="deleting" @close="deleting = null" />
    </AdminLayout>
</template>
