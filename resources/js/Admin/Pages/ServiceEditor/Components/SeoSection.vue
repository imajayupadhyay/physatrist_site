<script setup>
import { computed } from 'vue';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

const props = defineProps({
    form: { type: Object, required: true },
    service: { type: Object, default: null },
    siteUrl: { type: String, required: true },
});

// What search engines will actually get (same fallbacks as App\Support\Seo).
const pageUrl = computed(() => `${props.siteUrl}/services/${props.form.slug || 'your-service'}`);
const effectiveTitle = computed(() => props.form.seo_title || `${props.form.title || 'Service name'} · Mind Essence Wellness Centre`);
const effectiveDescription = computed(() => props.form.meta_description || props.form.intro || 'Add a meta description…');
const truncate = (text, max) => (text.length > max ? `${text.slice(0, max - 1)}…` : text);

const ogImage = computed(() => {
    if (props.form.og_image_file) return URL.createObjectURL(props.form.og_image_file);
    if (!props.form.remove_og_image && props.service?.og_image_url) return props.service.og_image_url;
    if (props.service?.hero_image_url) return props.service.hero_image_url;
    return '/images/therapy-room-2.jpg';
});
</script>

<template>
    <FormSection id="seo" title="SEO & social sharing" description="How this page appears in Google and when shared on WhatsApp, Facebook or LinkedIn.">
        <!-- Google preview -->
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="mb-2 text-xs font-medium tracking-wide text-slate-400 uppercase">Google preview</p>
            <p class="truncate text-xs text-slate-500">{{ pageUrl.replace(/^https?:\/\//, '') }}</p>
            <p class="mt-0.5 truncate text-lg leading-snug text-[#1a0dab]">{{ truncate(effectiveTitle, 62) }}</p>
            <p class="mt-0.5 line-clamp-2 text-sm text-slate-600">{{ truncate(effectiveDescription, 160) }}</p>
            <p v-if="!form.robots_index" class="mt-2 text-xs font-medium text-amber-700">Hidden from search engines (noindex).</p>
        </div>

        <FormInput id="seo_title" v-model="form.seo_title" label="SEO title" :placeholder="`${form.title || 'Service name'} · Mind Essence Wellness Centre`" :error="form.errors.seo_title" :maxlength="255" :recommended="[50, 60]" hint="Shown in the browser tab and search results. Leave blank for the default." />
        <FormTextarea id="meta_description" v-model="form.meta_description" label="Meta description" :rows="3" :error="form.errors.meta_description" :maxlength="320" :recommended="[120, 160]" hint="Leave blank to use the introduction." />
        <FormInput id="meta_keywords" v-model="form.meta_keywords" label="Meta keywords" placeholder="e.g. OCD therapy, ERP, intrusive thoughts" :error="form.errors.meta_keywords" :maxlength="255" hint="Optional, comma-separated. Google ignores this tag, but some search engines use it." />
        <FormInput id="canonical_url" v-model="form.canonical_url" type="url" label="Canonical URL" :placeholder="pageUrl" :error="form.errors.canonical_url" :maxlength="255" hint="Leave blank (recommended) to use this page’s own URL. Only set it if another page should rank instead." />

        <div class="grid gap-5 border-t border-slate-100 pt-5 sm:grid-cols-2">
            <ToggleSwitch v-model="form.robots_index" label="Allow search engines to index" description="Off = noindex: the page won’t appear in search results or the sitemap." />
            <ToggleSwitch v-model="form.robots_follow" label="Allow search engines to follow links" description="Off = nofollow." />
        </div>

        <!-- Open Graph -->
        <div class="space-y-5 border-t border-slate-100 pt-5">
            <div>
                <p class="text-sm font-semibold text-slate-800">Social sharing (Open Graph)</p>
                <p class="text-xs text-slate-500">Leave blank to reuse the SEO title, description and page image.</p>
            </div>
            <div class="grid gap-6 lg:grid-cols-[1fr_280px]">
                <div class="space-y-5">
                    <FormInput id="og_title" v-model="form.og_title" label="Share title" :placeholder="effectiveTitle" :error="form.errors.og_title" :maxlength="255" :recommended="[40, 70]" />
                    <FormTextarea id="og_description" v-model="form.og_description" label="Share description" :rows="2" :error="form.errors.og_description" :maxlength="320" :recommended="[80, 200]" />
                    <ImageUpload
                        id="og_image"
                        v-model:file="form.og_image_file"
                        v-model:remove="form.remove_og_image"
                        label="Share image"
                        :current-url="service?.og_image_url"
                        :error="form.errors.og_image_file"
                        hint="1200 × 630 px recommended. Falls back to the page image."
                    />
                </div>
                <div>
                    <p class="mb-1.5 text-sm font-medium text-slate-700">Share preview</p>
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                        <img :src="ogImage" alt="" class="aspect-[1.91/1] w-full object-cover" />
                        <div class="space-y-0.5 bg-slate-50 p-3">
                            <p class="text-[11px] text-slate-400 uppercase">{{ siteUrl.replace(/^https?:\/\//, '') }}</p>
                            <p class="line-clamp-2 text-sm font-semibold text-slate-800">{{ form.og_title || effectiveTitle }}</p>
                            <p class="line-clamp-2 text-xs text-slate-500">{{ form.og_description || effectiveDescription }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FormSection>
</template>
