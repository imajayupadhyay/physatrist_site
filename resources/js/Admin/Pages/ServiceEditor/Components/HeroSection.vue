<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';

defineProps({
    form: { type: Object, required: true },
    service: { type: Object, default: null },
});
</script>

<template>
    <FormSection id="hero" title="Introduction" description="The top of the service page: heading, intro paragraph and image.">
        <FormInput id="hero_eyebrow" v-model="form.hero_eyebrow" label="Small label above the heading" :placeholder="form.title || 'e.g. OCD therapy'" :error="form.errors.hero_eyebrow" :maxlength="80" />
        <FormInput id="hero_title" v-model="form.hero_title" label="Page heading (H1)" :error="form.errors.hero_title" :maxlength="150" required hint="The main heading of the page. Make it clear and benefit-focused." />
        <FormTextarea id="intro" v-model="form.intro" label="Introduction" :rows="4" :error="form.errors.intro" :maxlength="800" required hint="2–3 sentences introducing the service. Also used as the meta description if none is set." />
        <ImageUpload
            id="hero_image"
            v-model:file="form.hero_image_file"
            v-model:remove="form.remove_hero_image"
            label="Page image"
            :current-url="service?.hero_image_url"
            :error="form.errors.hero_image_file"
            aspect="aspect-[4/5]"
            hint="Portrait works best (4:5). Leave empty to use the default clinic photo. JPG, PNG or WebP, max 3 MB."
        />
    </FormSection>
</template>
