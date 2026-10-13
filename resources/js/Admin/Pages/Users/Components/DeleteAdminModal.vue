<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Admin/Components/Global/Modal.vue';

const props = defineProps({
    admin: { type: Object, default: null },
});
const emit = defineEmits(['close']);

const processing = ref(false);

const confirm = () => {
    router.delete(`/secure-admin/users/${props.admin.id}`, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => (processing.value = true),
        onFinish: () => (processing.value = false),
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
    <Modal :show="!!admin" title="Delete admin" max-width="max-w-md" @close="emit('close')">
        <div class="flex gap-4">
            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-red-50 text-red-600">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" /></svg>
            </span>
            <p class="text-sm leading-relaxed text-slate-600">
                Are you sure you want to delete <span class="font-medium text-slate-800">{{ admin?.name }}</span> ({{ admin?.email }})?
                They will no longer be able to log in. This cannot be undone.
            </p>
        </div>

        <template #footer>
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="emit('close')">Cancel</button>
            <button type="button" :disabled="processing" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-700 disabled:opacity-60" @click="confirm">
                {{ processing ? 'Deleting…' : 'Delete admin' }}
            </button>
        </template>
    </Modal>
</template>
