<script setup>
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import RepeaterEditor from '@/Admin/Components/Global/RepeaterEditor.vue';

defineProps({
    form: { type: Object, required: true },
});

const fields = [
    { key: 'value', label: 'Big text', maxlength: 20, placeholder: 'e.g. 6+' },
    { key: 'label', label: 'Small text', maxlength: 60, placeholder: 'e.g. Years of clinical practice' },
];
</script>

<template>
    <FormSection id="items" title="Highlights" description="The white strip of key numbers under the banner. 4 items fit best (max 6).">
        <RepeaterEditor v-model="form.items" :fields="fields" item-label="Item" add-label="Add item" :max="6" :errors="form.errors" error-key="items" compact />
    </FormSection>
</template>
