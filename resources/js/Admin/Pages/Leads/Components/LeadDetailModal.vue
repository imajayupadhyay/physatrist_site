<script setup>
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import LeadStatusBadge from '@/Admin/Components/Global/LeadStatusBadge.vue';
import Modal from '@/Admin/Components/Global/Modal.vue';
import { telUrl, whatsappUrl } from '@/Admin/Utils/contact';

const props = defineProps({
    lead: { type: Object, default: null },
    statuses: { type: Object, required: true },
});
const emit = defineEmits(['close', 'delete']);

const notes = useForm({ notes: '' });
watch(
    () => props.lead?.id,
    () => {
        notes.defaults({ notes: props.lead?.notes ?? '' });
        notes.reset();
        notes.clearErrors();
    },
    { immediate: true },
);

const saveNotes = () => {
    notes.patch(`/secure-admin/leads/${props.lead.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => notes.defaults(),
    });
};

const changingStatus = ref(false);
const setStatus = (status) => {
    if (status === props.lead.status) return;
    router.patch(
        `/secure-admin/leads/${props.lead.id}`,
        { status },
        {
            preserveScroll: true,
            preserveState: true,
            onStart: () => (changingStatus.value = true),
            onFinish: () => (changingStatus.value = false),
        },
    );
};

const firstName = (name) => name.trim().split(/\s+/)[0];
</script>

<template>
    <Modal :show="!!lead" :title="lead ? `Lead #${lead.id}` : ''" max-width="max-w-2xl" @close="emit('close')">
        <template v-if="lead">
            <!-- Header -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ lead.name }}</h3>
                    <p class="text-sm text-slate-500">Received {{ lead.created_at }} · {{ lead.created_ago }}</p>
                </div>
                <LeadStatusBadge :status="lead.status" :label="statuses[lead.status]" />
            </div>

            <!-- Contact actions -->
            <div class="mt-4 flex flex-wrap gap-2">
                <a :href="telUrl(lead.phone)" class="inline-flex items-center gap-2 rounded-lg bg-forest px-3.5 py-2 text-sm font-medium text-white transition hover:bg-forest-deep">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z" /></svg>
                    Call {{ lead.phone }}
                </a>
                <a
                    :href="whatsappUrl(lead.phone, `Hello ${firstName(lead.name)}, this is Mind Essence Wellness Centre regarding your appointment request.`)"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    <svg class="size-4 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1s-.5-.1-.7.1-.8 1-.9 1.2-.3.2-.6.1a8 8 0 0 1-2.4-1.5 9 9 0 0 1-1.6-2c-.2-.3 0-.5.1-.6l.4-.5.3-.5v-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6a1.1 1.1 0 0 0-.8.4 3.4 3.4 0 0 0-1 2.5 5.9 5.9 0 0 0 1.2 3.1 13.5 13.5 0 0 0 5.2 4.6c.7.3 1.3.5 1.7.6a4.2 4.2 0 0 0 1.9.1 3.1 3.1 0 0 0 2-1.4 2.5 2.5 0 0 0 .2-1.4c-.1-.2-.3-.2-.6-.4zM12 21.8a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 1 1 12 21.8zm8.4-18.2A11.8 11.8 0 0 0 1.8 17.8L.1 24l6.3-1.7A11.8 11.8 0 0 0 12 23.8 11.8 11.8 0 0 0 20.4 3.6z" /></svg>
                    WhatsApp
                </a>
                <a v-if="lead.email" :href="`mailto:${lead.email}`" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="M22 6l-10 7L2 6" /></svg>
                    Email
                </a>
            </div>

            <!-- Details -->
            <dl class="mt-5 grid gap-x-6 gap-y-4 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-2">
                <div><dt class="text-xs font-medium tracking-wide text-slate-400 uppercase">Phone</dt><dd class="mt-0.5 text-slate-800">{{ lead.phone }}</dd></div>
                <div><dt class="text-xs font-medium tracking-wide text-slate-400 uppercase">Email</dt><dd class="mt-0.5 break-all text-slate-800">{{ lead.email || '—' }}</dd></div>
                <div><dt class="text-xs font-medium tracking-wide text-slate-400 uppercase">Area of concern</dt><dd class="mt-0.5 text-slate-800">{{ lead.concern }}</dd></div>
                <div><dt class="text-xs font-medium tracking-wide text-slate-400 uppercase">Session mode</dt><dd class="mt-0.5 text-slate-800">{{ lead.mode }}</dd></div>
                <div><dt class="text-xs font-medium tracking-wide text-slate-400 uppercase">Preferred date</dt><dd class="mt-0.5 text-slate-800">{{ lead.preferred_date || 'No preference' }}</dd></div>
                <div><dt class="text-xs font-medium tracking-wide text-slate-400 uppercase">Source</dt><dd class="mt-0.5 text-slate-800">Website · appointment form</dd></div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium tracking-wide text-slate-400 uppercase">Message</dt>
                    <dd class="mt-0.5 whitespace-pre-line text-slate-800">{{ lead.message || '—' }}</dd>
                </div>
            </dl>

            <!-- Status -->
            <div class="mt-5">
                <p class="mb-2 text-sm font-medium text-slate-700">Status</p>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="(label, key) in statuses"
                        :key="key"
                        type="button"
                        :disabled="changingStatus"
                        class="rounded-lg border px-3.5 py-2 text-sm font-medium transition disabled:opacity-60"
                        :class="lead.status === key ? 'border-forest bg-forest text-white' : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'"
                        @click="setStatus(key)"
                    >
                        {{ label }}
                    </button>
                </div>
            </div>

            <!-- Notes -->
            <form class="mt-5" @submit.prevent="saveNotes">
                <label for="lead-notes" class="mb-1.5 block text-sm font-medium text-slate-700">Internal notes</label>
                <textarea
                    id="lead-notes"
                    v-model="notes.notes"
                    rows="3"
                    maxlength="5000"
                    placeholder="e.g. Called on Monday, booked for Thursday 4 PM."
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm transition outline-none placeholder:text-slate-400 focus:border-forest focus:ring-4 focus:ring-forest/10"
                />
                <p v-if="notes.errors.notes" class="mt-1 text-sm text-red-600">{{ notes.errors.notes }}</p>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-400">Only visible to admins.</p>
                    <button type="submit" :disabled="notes.processing || !notes.isDirty" class="rounded-lg bg-forest px-4 py-2 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                        {{ notes.processing ? 'Saving…' : 'Save notes' }}
                    </button>
                </div>
            </form>
        </template>

        <template #footer>
            <button type="button" class="mr-auto rounded-lg px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50" @click="emit('delete', lead)">Delete lead</button>
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="emit('close')">Close</button>
        </template>
    </Modal>
</template>
