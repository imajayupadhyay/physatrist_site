<script setup>
import { computed, ref } from 'vue';

// Labelled text input with validation error. Password inputs get a show/hide toggle.
const model = defineModel({ type: String, default: '' });

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    error: { type: String, default: '' },
    hint: { type: String, default: '' },
    autocomplete: { type: String, default: 'off' },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    maxlength: { type: Number, default: undefined },
    // [min, max] ideal length (e.g. SEO title 50–60). Shows a counter.
    recommended: { type: Array, default: null },
    // Fixed text shown before the input, e.g. "/services/".
    prefix: { type: String, default: '' },
});

const length = computed(() => (model.value ?? '').length);
const counterClass = computed(() => {
    if (!props.recommended || !length.value) return 'text-slate-400';
    const [min, max] = props.recommended;
    return length.value >= min && length.value <= max ? 'text-emerald-600' : 'text-amber-600';
});

const reveal = ref(false);
const isPassword = computed(() => props.type === 'password');
const inputType = computed(() => (isPassword.value && reveal.value ? 'text' : props.type));
</script>

<template>
    <div>
        <label :for="id" class="mb-1.5 block text-sm font-medium text-slate-700">
            {{ label }}<span v-if="required" class="text-red-500"> *</span>
        </label>
        <div class="relative flex">
            <span v-if="prefix" class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm whitespace-nowrap text-slate-500">{{ prefix }}</span>
            <input
                :id="id"
                v-model="model"
                :type="inputType"
                :autocomplete="autocomplete"
                :placeholder="placeholder"
                :required="required"
                :maxlength="maxlength"
                class="w-full min-w-0 border bg-white px-4 py-2.5 text-sm transition outline-none placeholder:text-slate-400 focus:border-forest focus:ring-4 focus:ring-forest/10"
                :class="[error ? 'border-red-400' : 'border-slate-300', isPassword && 'pr-11', prefix ? 'rounded-r-lg' : 'rounded-lg']"
            />
            <button
                v-if="isPassword"
                type="button"
                class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-slate-600"
                :aria-label="reveal ? 'Hide password' : 'Show password'"
                @click="reveal = !reveal"
            >
                <svg v-if="!reveal" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" /><circle cx="12" cy="12" r="3" /></svg>
                <svg v-else class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a17.6 17.6 0 0 1-3.2 4.2M6.6 6.6A17.4 17.4 0 0 0 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.6" /></svg>
            </button>
        </div>
        <div v-if="error || hint || recommended || maxlength" class="mt-1.5 flex justify-between gap-3">
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
            <p v-else class="text-xs text-slate-400">{{ hint }}</p>
            <p v-if="recommended || maxlength" class="shrink-0 text-xs tabular-nums" :class="counterClass">
                {{ length }}<template v-if="recommended"> · ideal {{ recommended[0] }}–{{ recommended[1] }}</template><template v-else> / {{ maxlength }}</template>
            </p>
        </div>
    </div>
</template>
