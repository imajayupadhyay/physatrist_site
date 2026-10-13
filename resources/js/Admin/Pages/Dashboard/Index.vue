<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import LatestLeads from './Components/LatestLeads.vue';
import LeadStats from './Components/LeadStats.vue';
import WelcomeCard from './Components/WelcomeCard.vue';

defineProps({
    leadStats: { type: Object, required: true },
    latestLeads: { type: Array, required: true },
    statuses: { type: Object, required: true },
});
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout title="Dashboard">
        <div class="space-y-6">
            <WelcomeCard />
            <LeadStats :stats="leadStats" />
            <LatestLeads :leads="latestLeads" :statuses="statuses" />
        </div>
    </AdminLayout>
</template>
