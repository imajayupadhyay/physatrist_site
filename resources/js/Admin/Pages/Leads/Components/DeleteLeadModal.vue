<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Admin/Components/Global/Modal.vue';

const props = defineProps({
    lead: { type: Object, default: null },
});
const emit = defineEmits(['close']);

const processing = ref(false);

const confirm = () => {
    router.delete(`/secure-admin/leads/${props.lead.id}`, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => (processing.value = true),
        onFinish: () => (processing.value = false),
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
    <Modal :show="!!lead" title="Delete lead" max-width="max-w-md" @close="emit('close')">
        <p class="text-sm leading-relaxed text-slate-600">
            Delete the request from <span class="font-medium text-slate-800">{{ lead?.name }}</span> ({{ lead?.phone }})? This cannot be undone.
        </p>

        <template #footer>
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="emit('close')">Cancel</button>
            <button type="button" :disabled="processing" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-700 disabled:opacity-60" @click="confirm">
                {{ processing ? 'Deleting…' : 'Delete lead' }}
            </button>
        </template>
    </Modal>
</template>
