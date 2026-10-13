<script setup>
import { computed, ref, watch } from 'vue';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';
import { slugify } from '@/Admin/Utils/slugify';

const props = defineProps({
    form: { type: Object, required: true },
    service: { type: Object, default: null },
    siteUrl: { type: String, required: true },
});

// New service: the slug follows the title until it's edited by hand.
const slugTouched = ref(!!props.service);
watch(
    () => props.form.title,
    (title) => {
        if (!slugTouched.value) props.form.slug = slugify(title);
    },
);
const onSlugInput = () => {
    slugTouched.value = true;
    props.form.slug = slugify(props.form.slug.replace(/\s+/g, '-')) || props.form.slug.toLowerCase();
};

const slugChanged = computed(() => props.service && props.form.slug && props.form.slug !== props.service.slug);
</script>

<template>
    <FormSection id="basics" title="Basics" description="Name, web address and visibility.">
        <FormInput id="title" v-model="form.title" label="Service name" :error="form.errors.title" :maxlength="120" required hint="Shown on the homepage card, page breadcrumbs and in the admin." />

        <div>
            <FormInput
                id="slug"
                v-model="form.slug"
                label="URL (slug)"
                :prefix="`${siteUrl.replace(/^https?:\/\//, '')}/services/`"
                :error="form.errors.slug"
                :maxlength="100"
                required
                hint="Lowercase letters, numbers and hyphens."
                @change="onSlugInput"
            />
            <p v-if="slugChanged" class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-800">
                The URL will change from <b>/services/{{ service.slug }}</b>. The old URL will permanently redirect (301) to the new one, so existing links and search rankings are kept.
            </p>
            <p v-if="service?.old_slugs?.length" class="mt-2 text-xs text-slate-500">
                Old URLs redirecting here: <span v-for="(old, i) in service.old_slugs" :key="old"><code class="rounded bg-slate-100 px-1">/services/{{ old }}</code><template v-if="i < service.old_slugs.length - 1">, </template></span>
            </p>
        </div>

        <FormInput
            id="concern_label"
            v-model="form.concern_label"
            label="Name in the appointment form"
            :error="form.errors.concern_label"
            :maxlength="100"
            :placeholder="form.title || 'Same as service name'"
            hint="Shown in the form’s “Area of concern” list. Leave blank to use the service name."
        />

        <ToggleSwitch v-model="form.is_published" label="Published" description="When off, the page and homepage card are hidden. Admins can still preview it." />
    </FormSection>
</template>
