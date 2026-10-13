<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Admin/Components/Global/Modal.vue';
import { iconMarkup } from '@/Shared/serviceIcons';

// Bulk-add service pages under a top-level item.
const props = defineProps({
    parent: { type: Object, default: null },
    services: { type: Array, required: true },
});
const emit = defineEmits(['close']);

const already = computed(() => new Set((props.parent?.children ?? []).filter((c) => c.type === 'service').map((c) => c.service_id)));
const available = computed(() => props.services.filter((s) => !already.value.has(s.id)));

const selected = ref([]);
watch(
    () => props.parent,
    () => (selected.value = available.value.filter((s) => s.is_published).map((s) => s.id)),
);

const allSelected = computed(() => available.value.length > 0 && selected.value.length === available.value.length);
const toggleAll = () => (selected.value = allSelected.value ? [] : available.value.map((s) => s.id));

const processing = ref(false);
const submit = () => {
    router.post(
        `/secure-admin/header/menu/${props.parent.id}/services`,
        { service_ids: selected.value },
        {
            preserveScroll: true,
            preserveState: true,
            onStart: () => (processing.value = true),
            onFinish: () => (processing.value = false),
            onSuccess: () => emit('close'),
        },
    );
};
</script>

<template>
    <Modal :show="!!parent" :title="`Add service pages to “${parent?.display_label}”`" @close="emit('close')">
        <div v-if="available.length">
            <label class="mb-2 flex items-center gap-2 border-b border-slate-100 pb-3 text-sm font-medium text-slate-700">
                <input type="checkbox" class="size-4 accent-forest" :checked="allSelected" @change="toggleAll" />
                Select all ({{ available.length }})
            </label>
            <ul class="max-h-80 space-y-1 overflow-y-auto">
                <li v-for="service in available" :key="service.id">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-slate-50">
                        <input v-model="selected" type="checkbox" :value="service.id" class="size-4 accent-forest" />
                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-sage-soft text-forest">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" v-html="iconMarkup(service.icon)" />
                        </span>
                        <span class="text-sm text-slate-700">{{ service.title }}</span>
                        <span v-if="!service.is_published" class="ml-auto rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">Unpublished</span>
                    </label>
                </li>
            </ul>
        </div>
        <p v-else class="py-6 text-center text-sm text-slate-500">All services are already in this dropdown.</p>

        <template #footer>
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="emit('close')">Cancel</button>
            <button type="button" :disabled="processing || !selected.length" class="rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50" @click="submit">
                {{ processing ? 'Adding…' : `Add ${selected.length || ''} ${selected.length === 1 ? 'page' : 'pages'}` }}
            </button>
        </template>
    </Modal>
</template>
