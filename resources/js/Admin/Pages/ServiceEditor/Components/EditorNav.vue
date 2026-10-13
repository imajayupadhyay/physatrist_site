<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    sections: { type: Array, required: true },
    errorCount: { type: Function, required: true },
});

// Highlight the section currently in view.
const active = ref(props.sections[0].id);
let observer;
onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => entries.forEach((entry) => entry.isIntersecting && (active.value = entry.target.id)),
        { rootMargin: '-30% 0px -60% 0px' },
    );
    props.sections.forEach((section) => {
        const el = document.getElementById(section.id);
        if (el) observer.observe(el);
    });
});
onBeforeUnmount(() => observer?.disconnect());

const go = (id) => document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
</script>

<template>
    <nav class="xl:sticky xl:top-24 xl:self-start" aria-label="Sections">
        <p class="mb-2 hidden px-3 text-[11px] font-medium tracking-[0.18em] text-slate-400 uppercase xl:block">Sections</p>
        <ul class="flex gap-1 overflow-x-auto pb-1 xl:flex-col xl:overflow-visible xl:pb-0">
            <li v-for="section in sections" :key="section.id" class="shrink-0">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium whitespace-nowrap transition"
                    :class="active === section.id ? 'bg-white text-forest shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:bg-white/70 hover:text-slate-700'"
                    @click="go(section.id)"
                >
                    {{ section.label }}
                    <span v-if="errorCount(section)" class="rounded-full bg-red-100 px-1.5 text-[11px] font-semibold text-red-700">{{ errorCount(section) }}</span>
                </button>
            </li>
        </ul>
    </nav>
</template>
