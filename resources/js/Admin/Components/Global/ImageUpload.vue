<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';

// File input with preview. v-model:file = new File | null, v-model:remove = remove the current image.
const file = defineModel('file', { default: null });
const remove = defineModel('remove', { type: Boolean, default: false });

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    currentUrl: { type: String, default: '' },
    hint: { type: String, default: 'JPG, PNG or WebP, max 3 MB.' },
    error: { type: String, default: '' },
    aspect: { type: String, default: 'aspect-video' },
    // false = the image can be replaced but not removed (it's required).
    removable: { type: Boolean, default: true },
});

const input = ref(null);
const previewUrl = ref('');

watch(file, (value) => {
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = value ? URL.createObjectURL(value) : '';
    if (value) remove.value = false;
});
onBeforeUnmount(() => previewUrl.value && URL.revokeObjectURL(previewUrl.value));

const shown = computed(() => previewUrl.value || (!remove.value && props.currentUrl) || '');

const pick = (e) => (file.value = e.target.files?.[0] ?? null);
const clear = () => {
    file.value = null;
    if (input.value) input.value.value = '';
    if (props.currentUrl && props.removable) remove.value = true;
};
</script>

<template>
    <div>
        <p class="mb-1.5 text-sm font-medium text-slate-700">{{ label }}</p>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="w-full shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 sm:w-48" :class="aspect">
                <img v-if="shown" :src="shown" alt="" class="size-full object-cover" />
                <div v-else class="grid size-full place-items-center text-slate-300">
                    <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="9" cy="9" r="2" /><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21" /></svg>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex flex-wrap gap-2">
                    <label :for="id" class="cursor-pointer rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        {{ shown ? 'Replace image' : 'Upload image' }}
                    </label>
                    <button v-if="shown && (removable || file)" type="button" class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50" @click="clear">{{ removable ? 'Remove' : 'Undo' }}</button>
                </div>
                <input :id="id" ref="input" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="pick" />
                <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
                <p v-else class="text-xs text-slate-400">{{ hint }}</p>
            </div>
        </div>
    </div>
</template>
