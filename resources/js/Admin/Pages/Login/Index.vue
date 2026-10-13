<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminBrand from '@/Admin/Components/Global/AdminBrand.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post('/secure-admin/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-sage-soft via-slate-50 to-gold-soft/60 px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 flex justify-center">
                <AdminBrand />
            </div>

            <div class="rounded-2xl border border-slate-200/70 bg-white p-8 shadow-xl shadow-forest/5">
                <h1 class="text-2xl font-semibold text-slate-800">Welcome back</h1>
                <p class="mt-1 text-sm text-slate-500">Sign in to manage your website.</p>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="username"
                            autofocus
                            required
                            placeholder="you@example.com"
                            class="w-full rounded-lg border px-4 py-2.5 text-sm transition outline-none placeholder:text-slate-400 focus:border-forest focus:ring-4 focus:ring-forest/10"
                            :class="form.errors.email ? 'border-red-400' : 'border-slate-300'"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                placeholder="••••••••"
                                class="w-full rounded-lg border px-4 py-2.5 pr-11 text-sm transition outline-none placeholder:text-slate-400 focus:border-forest focus:ring-4 focus:ring-forest/10"
                                :class="form.errors.password ? 'border-red-400' : 'border-slate-300'"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-slate-600"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                @click="showPassword = !showPassword"
                            >
                                <svg v-if="!showPassword" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" /><circle cx="12" cy="12" r="3" /></svg>
                                <svg v-else class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17.6 17.6 0 0 1-3.2 4.2M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.6" /></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input v-model="form.remember" type="checkbox" class="size-4 rounded border-slate-300 accent-forest" />
                        Remember me
                    </label>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-forest px-4 py-3 text-sm font-medium text-white transition hover:bg-forest-deep disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <svg v-if="form.processing" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="3" /><path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
                        {{ form.processing ? 'Signing in…' : 'Sign in' }}
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">© {{ new Date().getFullYear() }} Mind Essence Wellness Centre</p>
        </div>
    </div>
</template>
