<script setup>
defineProps({
    form: { type: Object, required: true },
    isEdit: { type: Boolean, default: false },
});
</script>

<template>
    <div class="fixed inset-x-0 bottom-0 z-20 border-t border-slate-200 bg-white/95 backdrop-blur lg:left-64">
        <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <p class="text-sm" :class="form.hasErrors ? 'text-red-600' : form.isDirty ? 'text-amber-600' : 'text-slate-400'">
                <template v-if="form.hasErrors">Please fix the highlighted fields.</template>
                <template v-else-if="form.isDirty">You have unsaved changes.</template>
                <template v-else>All changes saved.</template>
            </p>
            <div class="flex gap-2">
                <button
                    v-if="isEdit && form.isDirty"
                    type="button"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    @click="form.reset(); form.clearErrors()"
                >
                    Discard
                </button>
                <button type="submit" :disabled="form.processing || (isEdit && !form.isDirty)" class="rounded-lg bg-forest px-5 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                    {{ form.processing ? 'Saving…' : isEdit ? 'Save changes' : 'Create service' }}
                </button>
            </div>
        </div>
    </div>
</template>
