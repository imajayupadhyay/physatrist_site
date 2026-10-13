<script setup>
const model = defineModel({ type: Boolean, default: false });

defineProps({
    label: { type: String, required: true },
    description: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <label class="flex cursor-pointer items-start justify-between gap-4" :class="disabled && 'cursor-not-allowed opacity-60'">
        <span>
            <span class="block text-sm font-medium text-slate-700">{{ label }}</span>
            <span v-if="description" class="mt-0.5 block text-xs text-slate-500">{{ description }}</span>
        </span>
        <button
            type="button"
            role="switch"
            :aria-checked="model"
            :disabled="disabled"
            class="relative mt-0.5 inline-flex h-6 w-11 shrink-0 rounded-full transition"
            :class="model ? 'bg-forest' : 'bg-slate-300'"
            @click="model = !model"
        >
            <span class="absolute top-0.5 left-0.5 size-5 rounded-full bg-white shadow transition" :class="model && 'translate-x-5'" />
        </button>
    </label>
</template>
