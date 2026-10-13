<script setup>
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import RepeaterEditor from '@/Admin/Components/Global/RepeaterEditor.vue';

defineProps({
    form: { type: Object, required: true },
});

const fields = [
    { key: 'question', label: 'Question', maxlength: 200 },
    { key: 'answer', label: 'Answer', type: 'textarea', maxlength: 2000 },
];
</script>

<template>
    <FormSection id="faqs" title="FAQs" description="3–5 relevant questions work best. They also appear in Google as FAQ rich results. Leave empty to hide.">
        <RepeaterEditor v-model="form.faqs" :fields="fields" item-label="FAQ" add-label="Add FAQ" :max="15" :errors="form.errors" error-key="faqs" />
    </FormSection>
</template>
