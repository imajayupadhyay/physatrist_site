<script setup>
import { computed } from 'vue';

const model = defineModel({ type: String, default: '' });

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
    rows: { type: Number, default: 4 },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    maxlength: { type: Number, default: undefined },
    // [min, max] ideal length. Shows a counter.
    recommended: { type: Array, default: null },
});

const length = computed(() => (model.value ?? '').length);
const counterClass = computed(() => {
    if (!props.recommended || !length.value) return 'text-slate-400';
    const [min, max] = props.recommended;
    return length.value >= min && length.value <= max ? 'text-emerald-600' : 'text-amber-600';
});
</script>

<template>
    <div>
        <label :for="id" class="mb-1.5 block text-sm font-medium text-slate-700">
            {{ label }}<span v-if="required" class="text-red-500"> *</span>
        </label>
        <textarea
            :id="id"
            v-model="model"
            :rows="rows"
            :placeholder="placeholder"
            :maxlength="maxlength"
            class="w-full rounded-lg border bg-white px-4 py-2.5 text-sm leading-relaxed transition outline-none placeholder:text-slate-400 focus:border-forest focus:ring-4 focus:ring-forest/10"
            :class="error ? 'border-red-400' : 'border-slate-300'"
        />
        <div v-if="error || hint || recommended || maxlength" class="mt-1 flex justify-between gap-3">
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
            <p v-else class="text-xs text-slate-400">{{ hint }}</p>
            <p v-if="recommended || maxlength" class="shrink-0 text-xs tabular-nums" :class="counterClass">
                {{ length }}<template v-if="recommended"> · ideal {{ recommended[0] }}–{{ recommended[1] }}</template><template v-else> / {{ maxlength }}</template>
            </p>
        </div>
    </div>
</template>
