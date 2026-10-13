<script setup>
import { computed } from 'vue';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';

const props = defineProps({
    form: { type: Object, required: true },
});

const host = window.location.host;
const truncate = (text, max) => (text.length > max ? `${text.slice(0, max - 1)}…` : text);
const shareImage = computed(() => {
    if (props.form.og_image_file) return URL.createObjectURL(props.form.og_image_file);
    if (!props.form.og_image_remove && props.form.og_image) return props.form.og_image;
    return '/images/therapy-room-2.jpg';
});
</script>

<template>
    <FormSection id="search" title="Search engines" description="How the homepage appears in Google.">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="mb-2 text-xs font-medium tracking-wide text-slate-400 uppercase">Google preview</p>
            <p class="truncate text-xs text-slate-500">{{ host }}</p>
            <p class="mt-0.5 truncate text-lg leading-snug text-[#1a0dab]">{{ truncate(form.title || 'Page title', 62) }}</p>
            <p class="mt-0.5 line-clamp-2 text-sm text-slate-600">{{ truncate(form.description || 'Add a description…', 160) }}</p>
        </div>
        <FormInput id="seo-title" v-model="form.title" label="SEO title" :error="form.errors.title" :maxlength="255" :recommended="[50, 60]" required />
        <FormTextarea id="seo-description" v-model="form.description" label="Meta description" :rows="3" :error="form.errors.description" :maxlength="320" :recommended="[120, 160]" required />
        <FormInput id="seo-keywords" v-model="form.keywords" label="Meta keywords" placeholder="e.g. clinical psychologist Delhi, therapy, counselling" :error="form.errors.keywords" :maxlength="255" hint="Optional, comma-separated." />
    </FormSection>

    <FormSection id="share" title="Social sharing" description="When the homepage link is shared on WhatsApp, Facebook or LinkedIn. Leave blank to reuse the SEO title and description.">
        <div class="grid gap-6 lg:grid-cols-[1fr_280px]">
            <div class="space-y-5">
                <FormInput id="og-title" v-model="form.og_title" label="Share title" :placeholder="form.title" :error="form.errors.og_title" :maxlength="255" :recommended="[40, 70]" />
                <FormTextarea id="og-description" v-model="form.og_description" label="Share description" :rows="2" :error="form.errors.og_description" :maxlength="320" />
                <ImageUpload id="og-image" v-model:file="form.og_image_file" v-model:remove="form.og_image_remove" label="Share image" :current-url="form.og_image" :error="form.errors.og_image_file" hint="1200 × 630 px recommended. Remove it to use the default clinic photo." />
            </div>
            <div>
                <p class="mb-1.5 text-sm font-medium text-slate-700">Share preview</p>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <img :src="shareImage" alt="" class="aspect-[1.91/1] w-full object-cover" />
                    <div class="space-y-0.5 bg-slate-50 p-3">
                        <p class="text-[11px] text-slate-400 uppercase">{{ host }}</p>
                        <p class="line-clamp-2 text-sm font-semibold text-slate-800">{{ form.og_title || form.title }}</p>
                        <p class="line-clamp-2 text-xs text-slate-500">{{ form.og_description || form.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </FormSection>
</template>
