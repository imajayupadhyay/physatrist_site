<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';
import ListEditor from '@/Admin/Components/Global/ListEditor.vue';

defineProps({
    form: { type: Object, required: true },
});
</script>

<template>
    <FormSection id="photo" title="Photo & badge">
        <ImageUpload id="about-image" v-model:file="form.image_file" label="Photo" :current-url="form.image" :removable="false" :error="form.errors.image_file" aspect="aspect-[4/5]" hint="Portrait (4:5), shown with an arched top." />
        <FormInput id="about-image-alt" v-model="form.image_alt" label="Photo description (alt text)" :error="form.errors.image_alt" :maxlength="150" required />
        <div class="grid gap-4 sm:grid-cols-2">
            <FormInput id="about-badge-title" v-model="form.badge_title" label="Badge title" :error="form.errors.badge_title" :maxlength="40" hint="Leave empty to hide the badge." />
            <FormInput id="about-badge-text" v-model="form.badge_text" label="Badge text" :error="form.errors.badge_text" :maxlength="100" />
        </div>
    </FormSection>

    <FormSection id="text" title="Text">
        <FormInput id="about-eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
        <FormInput id="about-heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="100" required />
        <ListEditor v-model="form.roles" label="Titles (gold-lined, uppercase)" placeholder="e.g. RCI Registered Clinical Psychologist" add-label="Add title" :max="4" :errors="form.errors" error-key="roles" />
        <FormTextarea id="about-body" v-model="form.body" label="Introduction" :rows="7" :error="form.errors.body" :maxlength="3000" hint="Leave a blank line between paragraphs." />
        <FormTextarea id="about-quote" v-model="form.quote" label="Quote" :rows="2" :error="form.errors.quote" :maxlength="300" hint="Shown in italics. Leave empty to hide." />
        <ListEditor v-model="form.credentials" label="Credentials (with tick icons)" add-label="Add credential" :max="10" :errors="form.errors" error-key="credentials" />
    </FormSection>

    <FormSection id="buttons" title="Buttons" description="Leave a button’s text empty to hide it.">
        <div class="grid gap-4 sm:grid-cols-2">
            <FormInput id="about-p-label" v-model="form.primary_label" label="Green button text" :error="form.errors.primary_label" :maxlength="40" />
            <FormInput id="about-p-url" v-model="form.primary_url" label="Green button link" placeholder="#appointment" :error="form.errors.primary_url" :maxlength="255" />
            <FormInput id="about-s-label" v-model="form.secondary_label" label="Outline button text" :error="form.errors.secondary_label" :maxlength="40" />
            <FormInput id="about-s-url" v-model="form.secondary_url" label="Outline button link" placeholder="Leave empty for WhatsApp" :error="form.errors.secondary_url" :maxlength="255" hint="Empty = opens the clinic’s WhatsApp." />
        </div>
    </FormSection>
</template>
