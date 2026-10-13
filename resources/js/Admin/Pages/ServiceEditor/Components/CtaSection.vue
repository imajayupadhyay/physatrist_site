<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';

defineProps({
    form: { type: Object, required: true },
});
</script>

<template>
    <FormSection id="cta" title="Call to action" description="The dark green band with “Book an Appointment” and WhatsApp buttons.">
        <FormInput id="cta_heading" v-model="form.cta_heading" label="Heading" placeholder="Take the first step today" :error="form.errors.cta_heading" :maxlength="150" />
        <FormTextarea id="cta_text" v-model="form.cta_text" label="Text" :rows="2" :error="form.errors.cta_text" :maxlength="400" />
        <p class="text-xs text-slate-400">The booking form is added automatically below the FAQs, with this service pre-selected.</p>
    </FormSection>
</template>
