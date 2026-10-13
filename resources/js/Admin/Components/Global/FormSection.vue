<script setup>
// Card for one section of a long form. `id` is the anchor used by the section menu.
defineProps({
    id: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, default: '' },
});
</script>

<template>
    <section :id="id" class="scroll-mt-24 rounded-2xl border border-slate-200 bg-white">
        <header class="border-b border-slate-100 px-5 py-4 sm:px-6">
            <h3 class="font-semibold text-slate-800">{{ title }}</h3>
            <p v-if="description" class="mt-0.5 text-sm text-slate-500">{{ description }}</p>
        </header>
        <div class="space-y-5 px-5 py-5 sm:px-6">
            <slot />
        </div>
    </section>
</template>
