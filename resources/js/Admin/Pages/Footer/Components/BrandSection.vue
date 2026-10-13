<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

defineProps({
    form: { type: Object, required: true },
    logoUrl: { type: String, default: null },
});
</script>

<template>
    <FormSection id="brand" title="Brand column" description="The first column: logo, short intro, credentials and social icons.">
        <ImageUpload
            id="footer_logo"
            v-model:file="form.logo_file"
            v-model:remove="form.remove_logo"
            label="Footer logo"
            :current-url="logoUrl"
            :error="form.errors.logo_file"
            aspect="aspect-[3/1] bg-forest-deep"
            hint="The footer is dark green, so use a light/white version of your logo. PNG, JPG or WebP, max 2 MB. Remove it to use the default leaf mark."
        />
        <div class="grid gap-5 sm:grid-cols-2">
            <FormInput id="footer_logo_alt" v-model="form.logo_alt" label="Logo alt text" :error="form.errors.logo_alt" :maxlength="120" required />
            <div>
                <label for="footer_logo_height" class="mb-1.5 block text-sm font-medium text-slate-700">Logo height: {{ form.logo_height }}px</label>
                <input id="footer_logo_height" v-model.number="form.logo_height" type="range" min="24" max="80" step="2" class="mt-2 w-full accent-forest" />
                <p class="mt-1 text-xs text-slate-400">Only for an uploaded logo image.</p>
            </div>
            <FormInput id="footer_brand_name" v-model="form.brand_name" label="Brand name" :error="form.errors.brand_name" :maxlength="40" required hint="Shown next to the leaf mark when no logo is uploaded." />
            <FormInput id="footer_brand_tagline" v-model="form.brand_tagline" label="Tagline" :error="form.errors.brand_tagline" :maxlength="40" hint="Leave blank to hide." />
        </div>
        <FormTextarea id="about_text" v-model="form.about_text" label="Short intro" :rows="2" :error="form.errors.about_text" :maxlength="300" />
        <div class="grid gap-5 border-t border-slate-100 pt-5 sm:grid-cols-2">
            <FormInput id="credentials_name" v-model="form.credentials_name" label="Practitioner name" :error="form.errors.credentials_name" :maxlength="80" />
            <FormTextarea id="credentials_text" v-model="form.credentials_text" label="Credentials" :rows="2" :error="form.errors.credentials_text" :maxlength="300" hint="One line per row." />
        </div>
        <ToggleSwitch v-model="form.show_socials" label="Show social icons" description="Icons for the profiles set under “Contact & social”, plus WhatsApp." />
    </FormSection>
</template>
