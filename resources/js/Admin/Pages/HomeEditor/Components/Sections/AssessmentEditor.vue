<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ListEditor from '@/Admin/Components/Global/ListEditor.vue';
import RepeaterEditor from '@/Admin/Components/Global/RepeaterEditor.vue';

defineProps({
    form: { type: Object, required: true },
});

const typeFields = [
    { key: 'title', label: 'Name', maxlength: 80 },
    { key: 'text', label: 'Description', type: 'textarea', maxlength: 300 },
    { key: 'for', label: 'Who it’s for', maxlength: 80, placeholder: 'e.g. Children · Adolescents' },
];
const stepFields = [
    { key: 'title', label: 'Step', maxlength: 80 },
    { key: 'text', label: 'Description', type: 'textarea', maxlength: 300 },
];
</script>

<template>
    <FormSection id="heading" title="Heading">
        <FormInput id="as-eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
        <FormInput id="as-heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="120" required />
        <FormTextarea id="as-lead" v-model="form.lead" label="Intro text" :rows="3" :error="form.errors.lead" :maxlength="600" />
    </FormSection>

    <FormSection id="types" title="Assessment types" description="The numbered cards (numbers are added automatically).">
        <RepeaterEditor v-model="form.types" :fields="typeFields" item-label="Assessment" add-label="Add assessment" :max="12" :errors="form.errors" error-key="types" />
    </FormSection>

    <FormSection id="process" title="How it works" description="The steps on the left.">
        <FormInput id="as-process-heading" v-model="form.process_heading" label="Heading" :error="form.errors.process_heading" :maxlength="120" />
        <RepeaterEditor v-model="form.process" :fields="stepFields" item-label="Step" add-label="Add step" :max="8" :errors="form.errors" error-key="process" />
    </FormSection>

    <FormSection id="who" title="Who is it for?" description="The dark green box on the right.">
        <FormInput id="as-who-heading" v-model="form.who_heading" label="Heading" :error="form.errors.who_heading" :maxlength="120" />
        <ListEditor v-model="form.who_items" label="Points" add-label="Add point" :max="10" :errors="form.errors" error-key="who_items" />
        <FormTextarea id="as-note" v-model="form.note" label="Note" :rows="2" :error="form.errors.note" :maxlength="300" />
        <div class="grid gap-4 sm:grid-cols-2">
            <FormInput id="as-primary" v-model="form.primary_label" label="Booking button text" :error="form.errors.primary_label" :maxlength="40" required hint="Opens the booking form with “Psychological Assessment” selected." />
            <FormInput id="as-wa-label" v-model="form.whatsapp_label" label="WhatsApp button text" :error="form.errors.whatsapp_label" :maxlength="40" hint="Leave empty to hide." />
        </div>
        <FormTextarea id="as-wa-msg" v-model="form.whatsapp_message" label="WhatsApp message (pre-filled)" :rows="2" :error="form.errors.whatsapp_message" :maxlength="300" />
    </FormSection>
</template>
