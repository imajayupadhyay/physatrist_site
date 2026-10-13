<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
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
    <FormSection id="heading" title="Heading">
        <FormInput id="f-eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
        <FormInput id="f-heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="120" required />
        <FormTextarea id="f-lead" v-model="form.lead" label="Intro text" :rows="2" :error="form.errors.lead" :maxlength="300" />
    </FormSection>
    <FormSection id="items" title="Questions & answers" description="The first question is shown open.">
        <p class="rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-800">
            To add a link inside an answer, write <code class="rounded bg-white px-1">[link text](#appointment)</code> or <code class="rounded bg-white px-1">[link text](https://…)</code>.
        </p>
        <RepeaterEditor v-model="form.items" :fields="fields" item-label="Question" add-label="Add question" :max="30" :errors="form.errors" error-key="items" />
    </FormSection>
</template>
