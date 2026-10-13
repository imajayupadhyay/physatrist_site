<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import FormInput from '@/Admin/Components/Global/FormInput.vue';

const admin = usePage().props.auth.admin;

const form = useForm({
    name: admin.name,
    email: admin.email,
});

const submit = () => {
    form.put('/secure-admin/account', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.defaults(),
    });
};
</script>

<template>
    <section class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Personal details</h3>
            <p class="mt-0.5 text-sm text-slate-500">Update your name and login email.</p>
        </div>

        <form class="space-y-4 px-6 py-5" @submit.prevent="submit">
            <FormInput id="account-name" v-model="form.name" label="Full name" :error="form.errors.name" autocomplete="name" required />
            <FormInput id="account-email" v-model="form.email" label="Email" type="email" :error="form.errors.email" autocomplete="email" required />

            <div class="flex items-center justify-between gap-4 pt-2">
                <span class="text-sm text-slate-500">Role: <span class="font-medium text-forest capitalize">{{ admin.role }}</span></span>
                <button type="submit" :disabled="form.processing || !form.isDirty" class="rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                    {{ form.processing ? 'Saving…' : 'Save details' }}
                </button>
            </div>
        </form>
    </section>
</template>
