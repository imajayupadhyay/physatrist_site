<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ListEditor from '@/Admin/Components/Global/ListEditor.vue';

defineProps({
    form: { type: Object, required: true },
});
</script>

<template>
    <FormSection id="overview" title="About & issues addressed" description="What the service is, and the concerns it helps with.">
        <FormInput id="overview_heading" v-model="form.overview_heading" label="Section heading" placeholder="e.g. How therapy can help" :error="form.errors.overview_heading" :maxlength="150" />
        <FormTextarea id="overview_body" v-model="form.overview_body" label="Text" :rows="7" :error="form.errors.overview_body" :maxlength="6000" hint="Leave a blank line between paragraphs." />
        <div class="border-t border-slate-100 pt-5">
            <FormInput id="issues_heading" v-model="form.issues_heading" label="List heading" placeholder="e.g. Concerns we often help with" :error="form.errors.issues_heading" :maxlength="150" />
        </div>
        <ListEditor v-model="form.issues" label="Issues addressed" placeholder="e.g. Panic attacks" add-label="Add issue" :errors="form.errors" error-key="issues" />
    </FormSection>
</template>
