<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Admin/Layouts/AdminLayout.vue';
import AccountDetailsForm from './Components/AccountDetailsForm.vue';
import AdminsPanel from './Components/AdminsPanel.vue';
import ChangePasswordForm from './Components/ChangePasswordForm.vue';

defineProps({
    admins: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const tabs = [
    { key: 'admins', label: 'All admins' },
    { key: 'account', label: 'My account' },
];

// ?tab=account opens "My account" directly.
const tab = ref(new URLSearchParams(window.location.search).get('tab') === 'account' ? 'account' : 'admins');
</script>

<template>
    <Head title="Users" />

    <AdminLayout title="Users">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Users</h2>
                <p class="mt-1 text-sm text-slate-500">Manage admin accounts and your own login details.</p>
            </div>

            <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1 text-sm">
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
        </div>

        <AdminsPanel v-if="tab === 'admins'" :admins="admins" :filters="filters" @edit-self="tab = 'account'" />

        <div v-else class="grid gap-6 xl:grid-cols-2">
            <AccountDetailsForm />
            <ChangePasswordForm />
        </div>
    </AdminLayout>
</template>
