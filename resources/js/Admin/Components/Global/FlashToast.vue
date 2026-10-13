<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Shows session flash messages: back()->with('success' | 'error', '...').
const page = usePage();
const message = ref(null);
let timer;

watch(
    () => page.props.flash,
    (flash) => {
        const text = flash?.success || flash?.error;
        if (!text) return;

        message.value = { text, type: flash.success ? 'success' : 'error' };
        clearTimeout(timer);
        timer = setTimeout(() => (message.value = null), 4000);
    },
    { immediate: true },
);
</script>

<template>
    <Transition enter-active-class="duration-200 ease-out" enter-from-class="translate-y-2 opacity-0" leave-active-class="duration-150 ease-in" leave-to-class="opacity-0">
        <div
            v-if="message"
            class="fixed right-4 bottom-20 z-[60] flex max-w-sm items-center gap-3 rounded-xl border bg-white px-4 py-3 text-sm shadow-lg"
            :class="message.type === 'success' ? 'border-emerald-200' : 'border-red-200'"
            role="status"
        >
            <span class="grid size-7 shrink-0 place-items-center rounded-full" :class="message.type === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                <svg v-if="message.type === 'success'" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5" /></svg>
                <svg v-else class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 8v5M12 16.5v.5" /></svg>
            </span>
            <span class="text-slate-700">{{ message.text }}</span>
            <button class="ml-1 text-slate-400 hover:text-slate-600" aria-label="Dismiss" @click="message = null">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        </div>
    </Transition>
</template>
