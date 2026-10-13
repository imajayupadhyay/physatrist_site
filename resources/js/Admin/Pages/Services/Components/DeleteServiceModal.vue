<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Admin/Components/Global/Modal.vue';

const props = defineProps({
    service: { type: Object, default: null },
});
const emit = defineEmits(['close']);

const processing = ref(false);

const confirm = () => {
    router.delete(`/secure-admin/services/${props.service.id}`, {
        preserveScroll: true,
        onStart: () => (processing.value = true),
        onFinish: () => (processing.value = false),
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
    <Modal :show="!!service" title="Delete service" max-width="max-w-md" @close="emit('close')">
        <div class="space-y-3 text-sm leading-relaxed text-slate-600">
            <p>Delete <span class="font-medium text-slate-800">{{ service?.title }}</span>? This removes its homepage card and its page at <span class="font-medium text-slate-800">/services/{{ service?.slug }}</span>.</p>
            <p class="rounded-lg bg-amber-50 px-3 py-2 text-amber-800">Links to this page will stop working. To hide it temporarily instead, switch off “Published”.</p>
        </div>

        <template #footer>
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="emit('close')">Cancel</button>
            <button type="button" :disabled="processing" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-700 disabled:opacity-60" @click="confirm">
                {{ processing ? 'Deleting…' : 'Delete service' }}
            </button>
        </template>
    </Modal>
</template>
