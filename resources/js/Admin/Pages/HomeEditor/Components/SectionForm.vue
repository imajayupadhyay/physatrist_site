<script setup>
import { onBeforeUnmount, onMounted, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

// One homepage section's form: show/hide switch, the section's fields (slot), save bar, unsaved-changes guard.
const props = defineProps({
    section: { type: String, required: true },
    label: { type: String, required: true },
    initial: { type: Object, required: true },
});
const emit = defineEmits(['dirty', 'saved']);

// Where each section sits on the homepage (for "View on homepage").
const anchors = { hero: '#top', trust: '#top', about: '#about', assessment: '#assessment', steps: '#assessment', space: '#space', videos: '#videos', testimonials: '#stories', faq: '#faq', seo: '' };

// useForm only submits keys that exist when it's created, so top-level upload fields are added up front.
// (Uploads inside lists, e.g. slides[0].image_file, live in existing objects and are always sent.)
const uploadKeys = {
    about: { image_file: null },
    videos: { channel_image_file: null },
    seo: { og_image_file: null, og_image_remove: false },
};
const form = useForm({ ...JSON.parse(JSON.stringify(props.initial)), ...(uploadKeys[props.section] ?? {}) });
const hasVisibility = 'visible' in props.initial;

watch(() => form.isDirty, (value) => emit('dirty', value));

const containsFile = (value) =>
    value instanceof File || (value && typeof value === 'object' && Object.values(value).some(containsFile));

const save = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => emit('saved'),
        onError: () => document.querySelector('form .border-red-400, form .text-red-600')?.scrollIntoView({ behavior: 'smooth', block: 'center' }),
    };
    const url = `/secure-admin/home/${props.section}`;

    // Files need multipart, which PHP only parses for POST → spoof PUT.
    if (containsFile(form.data())) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(url, { ...options, forceFormData: true });
    } else {
        form.transform((data) => data).put(url, options);
    }
};

// Warn before leaving the page with unsaved changes.
const beforeUnload = (e) => {
    if (form.isDirty) {
        e.preventDefault();
        e.returnValue = '';
    }
};
let removeGuard;
onMounted(() => {
    window.addEventListener('beforeunload', beforeUnload);
    removeGuard = router.on('before', (event) => {
        if (form.isDirty && !form.processing && event.detail.visit.method === 'get' && !window.confirm('You have unsaved changes. Leave without saving?')) {
            event.preventDefault();
        }
    });
});
onBeforeUnmount(() => {
    window.removeEventListener('beforeunload', beforeUnload);
    removeGuard?.();
});
</script>

<template>
    <form class="min-w-0 space-y-6" @submit.prevent="save">
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">{{ label }}</h3>
                <a v-if="anchors[section] !== undefined" :href="`/${anchors[section]}`" target="_blank" rel="noopener" class="text-sm text-slate-500 hover:text-forest">View on homepage ↗</a>
            </div>
            <div v-if="hasVisibility" class="sm:w-72">
                <ToggleSwitch v-model="form.visible" label="Show on homepage" :description="form.visible ? 'This section is visible.' : 'Hidden. Menu links to it won’t scroll anywhere.'" />
            </div>
        </div>

        <div :class="hasVisibility && !form.visible && 'opacity-60'" class="space-y-6 transition">
            <slot :form="form" />
        </div>

        <div class="fixed inset-x-0 bottom-0 z-20 border-t border-slate-200 bg-white/95 backdrop-blur lg:left-64">
            <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <p class="text-sm" :class="form.hasErrors ? 'text-red-600' : form.isDirty ? 'text-amber-600' : 'text-slate-400'">
                    {{ form.hasErrors ? 'Please fix the highlighted fields.' : form.isDirty ? `Unsaved changes in “${label}”.` : 'All changes saved.' }}
                </p>
                <div class="flex gap-2">
                    <button v-if="form.isDirty" type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="form.reset(); form.clearErrors()">Discard</button>
                    <button type="submit" :disabled="form.processing || !form.isDirty" class="rounded-lg bg-forest px-5 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                        {{ form.processing ? 'Saving…' : 'Save section' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>
