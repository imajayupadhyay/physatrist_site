<script setup>
import { onBeforeUnmount, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: 'max-w-lg' },
});
const emit = defineEmits(['close']);

const onKey = (e) => e.key === 'Escape' && emit('close');

watch(
    () => props.show,
    (show) => {
        document.body.style.overflow = show ? 'hidden' : '';
        show ? window.addEventListener('keydown', onKey) : window.removeEventListener('keydown', onKey);
    },
);
onBeforeUnmount(() => {
    document.body.style.overflow = '';
    window.removeEventListener('keydown', onKey);
});
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="duration-150 ease-out" enter-from-class="opacity-0" leave-active-class="duration-100 ease-in" leave-to-class="opacity-0">
            <div v-if="show" class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto bg-slate-900/50 p-4 sm:items-center" @mousedown.self="emit('close')">
                <div class="w-full rounded-2xl bg-white shadow-xl" :class="maxWidth" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-800">{{ title }}</h2>
                        <button class="-mr-2 rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Close" @click="emit('close')">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18" /></svg>
                        </button>
                    </div>
                    <div class="px-6 py-5">
                        <slot />
                    </div>
                    <div v-if="$slots.footer" class="flex justify-end gap-3 rounded-b-2xl border-t border-slate-100 bg-slate-50 px-6 py-4">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
