<script setup>
import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filters: { type: Object, required: true },
    counts: { type: Object, required: true },
    statuses: { type: Object, required: true },
    concerns: { type: Array, required: true },
});

const state = reactive({ ...props.filters });

const apply = () => {
    const query = Object.fromEntries(Object.entries(state).filter(([, value]) => value));
    router.get('/secure-admin/leads', query, { preserveState: true, preserveScroll: true, replace: true });
};

let timer;
watch(
    () => state.search,
    () => {
        clearTimeout(timer);
        timer = setTimeout(apply, 300);
    },
);

const setStatus = (status) => {
    state.status = status;
    apply();
};

const reset = () => {
    Object.assign(state, { search: '', status: '', concern: '' });
    apply();
};
</script>

<template>
    <div class="border-b border-slate-100">
        <!-- Status tabs -->
        <div class="flex gap-1 overflow-x-auto px-4 pt-3 sm:px-5">
            <button
                v-for="tab in [{ key: '', label: 'All' }, ...Object.entries(statuses).map(([key, label]) => ({ key, label }))]"
                :key="tab.key"
                class="-mb-px flex items-center gap-2 border-b-2 px-3 py-2.5 text-sm font-medium whitespace-nowrap transition"
                :class="state.status === tab.key ? 'border-forest text-forest' : 'border-transparent text-slate-500 hover:text-slate-700'"
                @click="setStatus(tab.key)"
            >
                {{ tab.label }}
                <span class="rounded-full px-2 py-0.5 text-[11px]" :class="state.status === tab.key ? 'bg-forest text-white' : 'bg-slate-100 text-slate-500'">
                    {{ counts[tab.key || 'all'] }}
                </span>
            </button>
        </div>

        <!-- Search + concern -->
        <div class="flex flex-col gap-3 border-t border-slate-100 p-4 sm:flex-row sm:items-center sm:px-5">
            <div class="relative w-full sm:max-w-xs">
                <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
                <input
                    v-model="state.search"
                    type="search"
                    placeholder="Search name, phone or email"
                    class="w-full rounded-lg border border-slate-300 py-2 pr-3 pl-9 text-sm transition outline-none placeholder:text-slate-400 focus:border-forest focus:ring-4 focus:ring-forest/10"
                />
            </div>
            <select
                v-model="state.concern"
                class="w-full rounded-lg border border-slate-300 bg-white py-2 pr-8 pl-3 text-sm text-slate-700 outline-none focus:border-forest focus:ring-4 focus:ring-forest/10 sm:w-64"
                @change="apply"
            >
                <option value="">All concerns</option>
                <option v-for="concern in concerns" :key="concern" :value="concern">{{ concern }}</option>
            </select>
            <button v-if="state.search || state.status || state.concern" class="text-sm font-medium text-slate-500 hover:text-forest sm:ml-auto" @click="reset">Clear filters</button>
        </div>
    </div>
</template>
