<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import Modal from '@/Admin/Components/Global/Modal.vue';

// Create (admin = null) or edit another admin.
const props = defineProps({
    show: { type: Boolean, default: false },
    admin: { type: Object, default: null },
});
const emit = defineEmits(['close']);

const isEdit = computed(() => !!props.admin);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

watch(
    () => props.show,
    (show) => {
        if (!show) return;
        form.clearErrors();
        form.name = props.admin?.name ?? '';
        form.email = props.admin?.email ?? '';
        form.password = '';
        form.password_confirmation = '';
    },
);

const submit = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => emit('close'),
    };

    isEdit.value ? form.put(`/secure-admin/users/${props.admin.id}`, options) : form.post('/secure-admin/users', options);
};
</script>

<template>
    <Modal :show="show" :title="isEdit ? 'Edit admin' : 'Add admin'" @close="emit('close')">
        <form id="admin-form" class="space-y-4" @submit.prevent="submit">
            <FormInput id="admin-name" v-model="form.name" label="Full name" :error="form.errors.name" autocomplete="off" required />
            <FormInput id="admin-email" v-model="form.email" label="Email" type="email" :error="form.errors.email" autocomplete="off" required />

            <div class="rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-500">
                Role: <span class="font-medium text-forest">Admin</span>. Every account created here gets the admin role.
            </div>

            <FormInput
                id="admin-password"
                v-model="form.password"
                :label="isEdit ? 'New password' : 'Password'"
                type="password"
                :error="form.errors.password"
                :hint="isEdit ? 'Leave blank to keep the current password.' : 'At least 8 characters.'"
                autocomplete="new-password"
                :required="!isEdit"
            />
            <FormInput
                id="admin-password-confirmation"
                v-model="form.password_confirmation"
                label="Confirm password"
                type="password"
                :error="form.errors.password_confirmation"
                autocomplete="new-password"
                :required="!isEdit"
            />
        </form>

        <template #footer>
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="emit('close')">Cancel</button>
            <button
                type="submit"
                form="admin-form"
                :disabled="form.processing"
                class="rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-60"
            >
                {{ form.processing ? 'Saving…' : isEdit ? 'Save changes' : 'Create admin' }}
            </button>
        </template>
    </Modal>
</template>
