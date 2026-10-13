<script setup>
import { useForm } from '@inertiajs/vue3';
import FormInput from '@/Admin/Components/Global/FormInput.vue';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put('/secure-admin/account/password', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.reset(),
        onError: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <section class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Change password</h3>
            <p class="mt-0.5 text-sm text-slate-500">Use at least 8 characters. You'll stay logged in.</p>
        </div>

        <form class="space-y-4 px-6 py-5" @submit.prevent="submit">
            <FormInput id="current-password" v-model="form.current_password" label="Current password" type="password" :error="form.errors.current_password" autocomplete="current-password" required />
            <FormInput id="new-password" v-model="form.password" label="New password" type="password" :error="form.errors.password" autocomplete="new-password" required />
            <FormInput
                id="new-password-confirmation"
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                :error="form.errors.password_confirmation"
                autocomplete="new-password"
                required
            />

            <div class="flex justify-end pt-2">
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                    {{ form.processing ? 'Updating…' : 'Update password' }}
                </button>
            </div>
        </form>
    </section>
</template>
