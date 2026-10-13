<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import DeleteServiceModal from './Components/DeleteServiceModal.vue';
import ServicesTable from './Components/ServicesTable.vue';

defineProps({
    services: { type: Array, required: true },
});

const deleting = ref(null);
</script>

<template>
    <Head title="Services" />

    <AdminLayout title="Services">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Services</h2>
                <p class="mt-1 text-sm text-slate-500">Each service has a homepage card and its own page at <span class="font-medium text-slate-600">/services/…</span></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Link href="/secure-admin/services/homepage" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" /></svg>
                    Homepage section
                </Link>
                <Link href="/secure-admin/services/create" class="inline-flex items-center gap-2 rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
                    Add service
                </Link>
            </div>
        </div>

        <ServicesTable :services="services" @delete="deleting = $event" />

        <p class="mt-4 text-xs text-slate-400">The order here is the order of the cards on the homepage, the footer links and the appointment form’s “Area of concern” list.</p>

        <DeleteServiceModal :service="deleting" @close="deleting = null" />
    </AdminLayout>
</template>
