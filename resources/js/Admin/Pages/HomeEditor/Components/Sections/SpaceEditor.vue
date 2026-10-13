<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';
import ItemList from '@/Admin/Components/Global/ItemList.vue';

defineProps({
    form: { type: Object, required: true },
});

const positions = ['Large, left (portrait)', 'Wide, top right (landscape)', 'Small, bottom middle', 'Small, bottom right'];
const aspects = ['aspect-[3/4]', 'aspect-[2/1]', 'aspect-[4/3]', 'aspect-[4/3]'];
</script>

<template>
    <FormSection id="heading" title="Heading">
        <FormInput id="sp-eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
        <FormInput id="sp-heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="120" required />
        <FormTextarea id="sp-lead" v-model="form.lead" label="Intro text" :rows="2" :error="form.errors.lead" :maxlength="300" />
    </FormSection>

    <FormSection id="photos" title="Photos" description="A fixed gallery of 4 photos. Each position has its own shape.">
        <!-- Visual map of the layout -->
        <div class="grid h-28 max-w-sm grid-cols-[1.2fr_1fr_1fr] grid-rows-2 gap-1.5 text-[11px] font-semibold text-forest">
            <span class="row-span-2 grid place-items-center rounded-md bg-sage-soft">1</span>
            <span class="col-span-2 grid place-items-center rounded-md bg-sage-soft">2</span>
            <span class="grid place-items-center rounded-md bg-sage-soft">3</span>
            <span class="grid place-items-center rounded-md bg-sage-soft">4</span>
        </div>
        <ItemList v-model="form.photos" v-slot="{ item, index: i }" :new-item="() => ({})" item-label="Photo" title-key="caption" fixed :errors="form.errors" error-key="photos">
            <p class="text-xs font-medium text-slate-500">Position: {{ positions[i] }}</p>
            <ImageUpload :id="`photo-${i}`" v-model:file="item.image_file" label="Photo" :current-url="item.image" :removable="false" :aspect="aspects[i]" :error="form.errors[`photos.${i}.image_file`]" />
            <div class="grid gap-4 sm:grid-cols-2">
                <FormInput :id="`photo-${i}-alt`" v-model="item.alt" label="Description (alt text)" :error="form.errors[`photos.${i}.alt`]" :maxlength="150" required />
                <FormInput :id="`photo-${i}-caption`" v-model="item.caption" label="Caption" :error="form.errors[`photos.${i}.caption`]" :maxlength="60" hint="Leave empty to hide." />
            </div>
        </ItemList>
    </FormSection>
</template>
