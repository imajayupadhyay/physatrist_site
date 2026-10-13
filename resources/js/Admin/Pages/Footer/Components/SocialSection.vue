<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';

defineProps({
    form: { type: Object, required: true },
});

const profiles = [
    { key: 'instagram', label: 'Instagram', placeholder: 'https://www.instagram.com/yourprofile' },
    { key: 'youtube', label: 'YouTube', placeholder: 'https://www.youtube.com/@yourchannel' },
    { key: 'facebook', label: 'Facebook', placeholder: 'https://www.facebook.com/yourpage' },
    { key: 'linkedin', label: 'LinkedIn', placeholder: 'https://www.linkedin.com/in/yourprofile' },
];
</script>

<template>
    <FormSection id="social" title="Social profiles" description="Leave a field empty to hide that icon. Instagram and YouTube are also used in the homepage “Videos” section.">
        <div class="grid gap-5 sm:grid-cols-2">
            <FormInput
                v-for="profile in profiles"
                :id="`social_${profile.key}`"
                :key="profile.key"
                v-model="form.contact[profile.key]"
                type="url"
                :label="profile.label"
                :placeholder="profile.placeholder"
                :error="form.errors[`contact.${profile.key}`]"
                :maxlength="255"
            />
        </div>
    </FormSection>
</template>
