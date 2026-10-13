<script setup>
import { Link } from '@inertiajs/vue3';

// Takes a Laravel paginator (->paginate()) passed as an Inertia prop.
defineProps({
    paginator: { type: Object, required: true },
});

const label = (text) => text.replace('&laquo; ', '‹ ').replace(' &raquo;', ' ›');
</script>

<template>
    <div v-if="paginator.total" class="flex flex-col items-center justify-between gap-3 sm:flex-row">
        <p class="text-sm text-slate-500">
            Showing <span class="font-medium text-slate-700">{{ paginator.from }}</span>–<span class="font-medium text-slate-700">{{ paginator.to }}</span>
            of <span class="font-medium text-slate-700">{{ paginator.total }}</span>
        </p>
        <nav v-if="paginator.last_page > 1" class="flex flex-wrap gap-1">
            <template v-for="(link, i) in paginator.links" :key="i">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    preserve-state
                    class="min-w-9 rounded-lg border px-3 py-1.5 text-center text-sm transition"
                    :class="link.active ? 'border-forest bg-forest text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                >
                    {{ label(link.label) }}
                </Link>
                <span v-else class="min-w-9 rounded-lg border border-slate-100 px-3 py-1.5 text-center text-sm text-slate-300">{{ label(link.label) }}</span>
            </template>
        </nav>
    </div>
</template>
