<script setup>
import { computed, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Pagination from '@/Admin/Components/Global/Pagination.vue';
import { initials } from '@/Admin/Utils/initials';
import AdminFormModal from './AdminFormModal.vue';
import DeleteAdminModal from './DeleteAdminModal.vue';

const props = defineProps({
    admins: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});
const emit = defineEmits(['edit-self']);

const me = computed(() => usePage().props.auth.admin);

// Search (debounced, keeps the URL in sync).
const search = ref(props.filters.search ?? '');
let searchTimer;
watch(search, (value) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get('/secure-admin/users', value ? { search: value } : {}, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});

// Create / edit modal: `editing` is null when creating.
const formOpen = ref(false);
const editing = ref(null);
const openCreate = () => {
    editing.value = null;
    formOpen.value = true;
};
const openEdit = (admin) => {
    if (admin.id === me.value.id) {
        emit('edit-self');
        return;
    }
    editing.value = admin;
    formOpen.value = true;
};

const deleting = ref(null);
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div class="relative w-full sm:max-w-xs">
                <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by name or email"
                    class="w-full rounded-lg border border-slate-300 py-2 pr-3 pl-9 text-sm transition outline-none placeholder:text-slate-400 focus:border-forest focus:ring-4 focus:ring-forest/10"
                />
            </div>
            <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep" @click="openCreate">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
                Add admin
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-medium tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="hidden px-5 py-3 md:table-cell">Created</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="admin in admins.data" :key="admin.id" class="transition hover:bg-slate-50/60">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-sage-soft text-xs font-semibold text-forest">{{ initials(admin.name) }}</span>
                                <div class="min-w-0">
                                    <p class="flex items-center gap-2 font-medium text-slate-800">
                                        <span class="truncate">{{ admin.name }}</span>
                                        <span v-if="admin.id === me.id" class="rounded-full bg-gold-soft px-2 py-0.5 text-[11px] font-medium text-forest-deep">You</span>
                                    </p>
                                    <p class="truncate text-slate-500">{{ admin.email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="rounded-full bg-forest/10 px-2.5 py-1 text-xs font-medium text-forest capitalize">{{ admin.role }}</span>
                        </td>
                        <td class="hidden px-5 py-3.5 whitespace-nowrap text-slate-500 md:table-cell">{{ admin.created_at }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex justify-end gap-1">
                                <button
                                    class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-forest"
                                    :title="admin.id === me.id ? 'Edit my account' : 'Edit'"
                                    :aria-label="`Edit ${admin.name}`"
                                    @click="openEdit(admin)"
                                >
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" /></svg>
                                </button>
                                <button
                                    v-if="admin.id !== me.id"
                                    class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                    title="Delete"
                                    :aria-label="`Delete ${admin.name}`"
                                    @click="deleting = admin"
                                >
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /></svg>
                                </button>
                                <span v-else class="w-8" />
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!admins.data.length">
                        <td colspan="4" class="px-5 py-12 text-center text-slate-500">
                            {{ search ? `No admins match “${search}”.` : 'No admins yet.' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4 sm:px-5">
            <Pagination :paginator="admins" />
        </div>
    </div>

    <AdminFormModal :show="formOpen" :admin="editing" @close="formOpen = false" />
    <DeleteAdminModal :admin="deleting" @close="deleting = null" />
</template>
