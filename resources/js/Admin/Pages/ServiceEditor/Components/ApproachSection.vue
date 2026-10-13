<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import RepeaterEditor from '@/Admin/Components/Global/RepeaterEditor.vue';

defineProps({
    form: { type: Object, required: true },
});

const fields = [
    { key: 'title', label: 'Step title', maxlength: 100, placeholder: 'e.g. Understanding you' },
    { key: 'text', label: 'Description', type: 'textarea', maxlength: 400 },
];
</script>

<template>
    <FormSection id="approach" title="How we help" description="Numbered steps describing what to expect. Leave empty to hide this section.">
        <FormInput id="approach_heading" v-model="form.approach_heading" label="Section heading" placeholder="What to expect" :error="form.errors.approach_heading" :maxlength="150" />
        <RepeaterEditor v-model="form.approach" :fields="fields" item-label="Step" add-label="Add step" :max="8" :errors="form.errors" error-key="approach" />
    </FormSection>
</template>
