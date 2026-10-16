<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

// Top bar ("topbar" tab), logo + CTA button ("brand" tab) and mega-menu promo ("mega" tab): one form, one save.
const props = defineProps({
    settings: { type: Object, required: true },
    contact: { type: Object, required: true },
    section: { type: String, required: true },
});

const s = props.settings;
const form = useForm({
    topbar_visible: s.topbar_visible ?? true,
    topbar_show_hours: s.topbar_show_hours ?? true,
    topbar_text: s.topbar_text ?? '',
    topbar_show_phone: s.topbar_show_phone ?? true,
    topbar_show_email: s.topbar_show_email ?? true,
    logo_file: null,
    remove_logo: false,
    logo_alt: s.logo_alt ?? '',
    logo_height: s.logo_height ?? 46,
    brand_name: s.brand_name ?? '',
    brand_tagline: s.brand_tagline ?? '',
    cta_visible: s.cta_visible ?? true,
    cta_label: s.cta_label ?? '',
    cta_url: s.cta_url ?? '',
    cta_new_tab: s.cta_new_tab ?? false,
    mega_promo_visible: s.mega_promo_visible ?? true,
    mega_promo_title: s.mega_promo_title ?? '',
    mega_promo_text: s.mega_promo_text ?? '',
    mega_promo_cta_label: s.mega_promo_cta_label ?? '',
    mega_promo_cta_url: s.mega_promo_cta_url ?? '',
});

const save = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.logo_file = null;
            form.remove_logo = false;
            form.defaults();
        },
    };

    // Files need multipart, which PHP only parses for POST → spoof PUT.
    if (form.logo_file) {
        form.transform((data) => ({ ...data, _method: 'put' })).post('/secure-admin/header', { ...options, forceFormData: true });
    } else {
        form.transform((data) => data).put('/secure-admin/header', options);
    }
};
</script>

<template>
    <form class="space-y-6 pb-24" @submit.prevent="save">
        <FormSection v-if="section === 'topbar'" id="topbar" title="Top bar" description="The dark strip above the menu, on every page.">
            <ToggleSwitch v-model="form.topbar_visible" label="Show the top bar" />
            <template v-if="form.topbar_visible">
                <div class="space-y-4 border-t border-slate-100 pt-5">
                    <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">Left side</p>
                    <ToggleSwitch v-model="form.topbar_show_hours" label="Opening hours" :description="contact.hours || 'No hours set yet.'" />
                    <FormInput
                        id="topbar_text"
                        v-model="form.topbar_text"
                        label="Message"
                        placeholder="In-clinic & online sessions"
                        :error="form.errors.topbar_text"
                        :maxlength="80"
                        hint="Short text after the hours. Leave blank to hide. The left side is hidden on phones when a phone number or email is shown."
                    />
                </div>
                <div class="space-y-4 border-t border-slate-100 pt-5">
                    <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">Right side</p>
                    <ToggleSwitch v-model="form.topbar_show_phone" label="Phone number" :description="contact.phones[0]?.display || 'No phone number set yet.'" />
                    <ToggleSwitch v-model="form.topbar_show_email" label="Email address" :description="contact.email || 'No email set yet.'" />
                </div>
                <p class="rounded-lg bg-slate-50 px-4 py-3 text-xs text-slate-500">
                    Hours, phone and email are the site-wide contact details. Change them in
                    <Link href="/secure-admin/footer?tab=contact" class="font-medium text-forest underline underline-offset-2">Footer → Contact &amp; social</Link>.
                </p>
            </template>
        </FormSection>

        <template v-else-if="section === 'brand'">
            <FormSection id="logo" title="Logo" description="Upload your own logo, or keep the leaf mark with the brand name.">
                <ImageUpload
                    id="logo"
                    v-model:file="form.logo_file"
                    v-model:remove="form.remove_logo"
                    label="Logo image"
                    :current-url="settings.logo_url"
                    :error="form.errors.logo_file"
                    aspect="aspect-[3/1]"
                    hint="PNG with a transparent background works best. PNG, JPG or WebP, max 2 MB. Remove it to go back to the default leaf mark."
                />
                <div class="grid gap-5 sm:grid-cols-2">
                    <FormInput id="logo_alt" v-model="form.logo_alt" label="Logo alt text" :error="form.errors.logo_alt" :maxlength="120" required hint="Describes the logo for screen readers and Google." />
                    <div>
                        <label for="logo_height" class="mb-1.5 block text-sm font-medium text-slate-700">Logo height: {{ form.logo_height }}px</label>
                        <input id="logo_height" v-model.number="form.logo_height" type="range" min="24" max="80" step="2" class="mt-2 w-full accent-forest" />
                        <p class="mt-1 text-xs text-slate-400">Only for an uploaded logo image.</p>
                    </div>
                </div>
                <div class="grid gap-5 border-t border-slate-100 pt-5 sm:grid-cols-2">
                    <FormInput id="brand_name" v-model="form.brand_name" label="Brand name" :error="form.errors.brand_name" :maxlength="40" required hint="Shown next to the leaf mark when no logo image is uploaded." />
                    <FormInput id="brand_tagline" v-model="form.brand_tagline" label="Tagline" :error="form.errors.brand_tagline" :maxlength="40" hint="Small text under the name. Leave blank to hide." />
                </div>
            </FormSection>

            <FormSection id="cta" title="Call-to-action button" description="The button on the right of the menu (desktop).">
                <ToggleSwitch v-model="form.cta_visible" label="Show the button" />
                <div v-if="form.cta_visible" class="grid gap-5 sm:grid-cols-2">
                    <FormInput id="cta_label" v-model="form.cta_label" label="Button text" :error="form.errors.cta_label" :maxlength="30" required />
                    <FormInput id="cta_url" v-model="form.cta_url" label="Button link" placeholder="/#appointment" :error="form.errors.cta_url" :maxlength="255" required hint="e.g. /#appointment, /services/ocd-therapy, https://…, tel:+91…" />
                </div>
                <ToggleSwitch v-if="form.cta_visible" v-model="form.cta_new_tab" label="Open in a new tab" />
            </FormSection>
        </template>

        <FormSection v-else id="mega" title="Mega menu panel" description="The dark box on the right side of every mega menu.">
            <ToggleSwitch v-model="form.mega_promo_visible" label="Show the panel" />
            <template v-if="form.mega_promo_visible">
                <FormInput id="mega_promo_title" v-model="form.mega_promo_title" label="Heading" :error="form.errors.mega_promo_title" :maxlength="60" required />
                <FormTextarea id="mega_promo_text" v-model="form.mega_promo_text" label="Text" :rows="2" :error="form.errors.mega_promo_text" :maxlength="200" />
                <div class="grid gap-5 sm:grid-cols-2">
                    <FormInput id="mega_promo_cta_label" v-model="form.mega_promo_cta_label" label="Button text" :error="form.errors.mega_promo_cta_label" :maxlength="30" required />
                    <FormInput id="mega_promo_cta_url" v-model="form.mega_promo_cta_url" label="Button link" :error="form.errors.mega_promo_cta_url" :maxlength="255" required />
                </div>
                <p class="text-xs text-slate-400">The clinic’s phone number is shown under the button automatically.</p>
            </template>
        </FormSection>

        <div class="fixed inset-x-0 bottom-0 z-20 border-t border-slate-200 bg-white/95 backdrop-blur lg:left-64">
            <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <p class="text-sm" :class="form.hasErrors ? 'text-red-600' : form.isDirty ? 'text-amber-600' : 'text-slate-400'">
                    {{ form.hasErrors ? 'Please fix the highlighted fields (check every tab).' : form.isDirty ? 'You have unsaved changes.' : 'All changes saved.' }}
                </p>
                <button type="submit" :disabled="form.processing || !form.isDirty" class="rounded-lg bg-forest px-5 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-50">
                    {{ form.processing ? 'Saving…' : 'Save settings' }}
                </button>
            </div>
        </div>
    </form>
</template>
