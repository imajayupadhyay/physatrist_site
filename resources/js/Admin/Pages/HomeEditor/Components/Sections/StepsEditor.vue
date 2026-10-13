<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import RepeaterEditor from '@/Admin/Components/Global/RepeaterEditor.vue';

defineProps({
    form: { type: Object, required: true },
});

const fields = [
    { key: 'title', label: 'Step', maxlength: 80 },
    { key: 'text', label: 'Description', type: 'textarea', maxlength: 300 },
];
</script>

<template>
    <FormSection id="heading" title="Heading" description="The dark green “Your first steps” band.">
        <FormInput id="st-eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
        <FormInput id="st-heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="120" required />
        <FormTextarea id="st-lead" v-model="form.lead" label="Intro text" :rows="2" :error="form.errors.lead" :maxlength="300" />
    </FormSection>
    <FormSection id="steps" title="Steps" description="Numbered automatically (01, 02, 03…). 3 fit best.">
        <RepeaterEditor v-model="form.items" :fields="fields" item-label="Step" add-label="Add step" :max="6" :errors="form.errors" error-key="items" />
    </FormSection>
</template>
