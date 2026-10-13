<script setup>
import { Link, router } from '@inertiajs/vue3';
import { iconMarkup } from '@/Shared/serviceIcons';

defineProps({
    services: { type: Array, required: true },
});
defineEmits(['delete']);

const visit = { preserveScroll: true, preserveState: true };
const move = (service, direction) => router.post(`/secure-admin/services/${service.id}/move`, { direction }, visit);
const toggle = (service, field) => router.patch(`/secure-admin/services/${service.id}/toggle`, { field, value: !service[field] }, visit);
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-medium tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="w-20 px-4 py-3">Order</th>
                        <th class="px-4 py-3">Service</th>
                        <th class="px-4 py-3 text-center">Published</th>
                        <th class="px-4 py-3 text-center">On homepage</th>
                        <th class="hidden px-4 py-3 lg:table-cell">Updated</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="(service, i) in services" :key="service.id" class="transition hover:bg-slate-50/60">
                        <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <button class="rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-25" :disabled="i === 0" aria-label="Move up" @click="move(service, 'up')">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6" /></svg>
                                </button>
                                <button class="rounded p-0.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-25" :disabled="i === services.length - 1" aria-label="Move down" @click="move(service, 'down')">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <Link :href="`/secure-admin/services/${service.id}/edit`" class="group flex items-center gap-3">
                                <span class="grid size-10 shrink-0 place-items-center rounded-full" :class="service.is_featured ? 'bg-forest text-gold-soft' : 'bg-sage-soft text-forest'">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" v-html="iconMarkup(service.icon)" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block font-medium text-slate-800 group-hover:text-forest">{{ service.title }}</span>
                                    <span class="block truncate text-xs text-slate-400">/services/{{ service.slug }}</span>
                                    <span v-if="!service.robots_index" class="mt-1 inline-block rounded bg-amber-50 px-1.5 py-0.5 text-[11px] font-medium text-amber-700">noindex</span>
                                </span>
                            </Link>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                role="switch"
                                :aria-checked="service.is_published"
                                :aria-label="`Published: ${service.title}`"
                                class="relative inline-flex h-6 w-11 rounded-full transition"
                                :class="service.is_published ? 'bg-forest' : 'bg-slate-300'"
                                @click="toggle(service, 'is_published')"
                            >
                                <span class="absolute top-0.5 left-0.5 size-5 rounded-full bg-white shadow transition" :class="service.is_published && 'translate-x-5'" />
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                role="switch"
                                :aria-checked="service.show_on_home"
                                :aria-label="`On homepage: ${service.title}`"
                                class="relative inline-flex h-6 w-11 rounded-full transition"
                                :class="service.show_on_home ? 'bg-forest' : 'bg-slate-300'"
                                @click="toggle(service, 'show_on_home')"
                            >
                                <span class="absolute top-0.5 left-0.5 size-5 rounded-full bg-white shadow transition" :class="service.show_on_home && 'translate-x-5'" />
                            </button>
                        </td>
                        <td class="hidden px-4 py-3 whitespace-nowrap text-slate-500 lg:table-cell">{{ service.updated_at }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a :href="`/services/${service.slug}`" target="_blank" rel="noopener" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-forest" :title="service.is_published ? 'View page' : 'Preview draft'">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" /><circle cx="12" cy="12" r="3" /></svg>
                                </a>
                                <Link :href="`/secure-admin/services/${service.id}/edit`" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-forest" title="Edit">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" /></svg>
                                </Link>
                                <button class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600" title="Delete" @click="$emit('delete', service)">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!services.length">
                        <td colspan="6" class="px-5 py-14 text-center text-slate-500">No services yet. Click “Add service” to create the first one.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
