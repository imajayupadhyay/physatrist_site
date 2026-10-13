<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';
import ItemList from '@/Admin/Components/Global/ItemList.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

defineProps({
    form: { type: Object, required: true },
});

const newSlide = () => ({
    image: '/images/therapy-room-1.jpg',
    alt: '',
    eyebrow: '',
    title: '',
    title_em: '',
    text: '',
    primary_label: 'Book an Appointment',
    primary_url: '#appointment',
    secondary_label: '',
    secondary_url: '',
});
</script>

<template>
    <FormSection id="slides" title="Slides" description="The large rotating banner at the top of the homepage (1–6 slides).">
        <div class="flex items-center gap-3 text-sm text-slate-600">
            <label for="hero_interval">Change slide every</label>
            <input id="hero_interval" v-model.number="form.interval" type="number" min="3" max="20" step="0.5" class="w-20 rounded-lg border border-slate-300 px-2 py-1.5 text-center" />
            seconds
            <span v-if="form.errors.interval" class="text-red-600">{{ form.errors.interval }}</span>
        </div>

        <ItemList v-model="form.slides" v-slot="{ item, index: i }" :new-item="newSlide" item-label="Slide" add-label="Add slide" title-key="title" :min="1" :max="6" :errors="form.errors" error-key="slides">
            <ImageUpload :id="`slide-${i}-image`" v-model:file="item.image_file" label="Background image" :current-url="item.image" :removable="false" :error="form.errors[`slides.${i}.image_file`]" hint="Wide landscape photo, at least 1920 × 1080. JPG, PNG or WebP, max 4 MB." />
            <FormInput :id="`slide-${i}-alt`" v-model="item.alt" label="Image description (alt text)" :error="form.errors[`slides.${i}.alt`]" :maxlength="150" required />
            <FormInput :id="`slide-${i}-eyebrow`" v-model="item.eyebrow" label="Small label" :error="form.errors[`slides.${i}.eyebrow`]" :maxlength="80" />
            <div class="grid gap-4 sm:grid-cols-2">
                <FormInput :id="`slide-${i}-title`" v-model="item.title" label="Heading, line 1" :error="form.errors[`slides.${i}.title`]" :maxlength="80" required />
                <FormInput :id="`slide-${i}-title-em`" v-model="item.title_em" label="Heading, line 2 (gold italic)" :error="form.errors[`slides.${i}.title_em`]" :maxlength="80" />
            </div>
            <FormTextarea :id="`slide-${i}-text`" v-model="item.text" label="Text" :rows="2" :error="form.errors[`slides.${i}.text`]" :maxlength="300" />
            <div class="grid gap-4 sm:grid-cols-2">
                <FormInput :id="`slide-${i}-p-label`" v-model="item.primary_label" label="Gold button text" :error="form.errors[`slides.${i}.primary_label`]" :maxlength="40" />
                <FormInput :id="`slide-${i}-p-url`" v-model="item.primary_url" label="Gold button link" placeholder="#appointment" :error="form.errors[`slides.${i}.primary_url`]" :maxlength="255" />
                <FormInput :id="`slide-${i}-s-label`" v-model="item.secondary_label" label="Outline button text" :error="form.errors[`slides.${i}.secondary_label`]" :maxlength="40" />
                <FormInput :id="`slide-${i}-s-url`" v-model="item.secondary_url" label="Outline button link" placeholder="#services" :error="form.errors[`slides.${i}.secondary_url`]" :maxlength="255" />
            </div>
            <p class="text-xs text-slate-400">Leave a button’s text empty to hide it. Links: #appointment, #services, /services/ocd-therapy, https://…</p>
        </ItemList>
    </FormSection>

    <FormSection id="card" title="Psychologist card" description="The white card floating on the right of the banner (a strip under it on mobile).">
        <ToggleSwitch v-model="form.card.visible" label="Show the card" />
        <ImageUpload id="card-photo" v-model:file="form.card.photo_file" label="Photo" :current-url="form.card.photo" :removable="false" :error="form.errors['card.photo_file']" aspect="aspect-square" hint="Square portrait works best." />
        <div class="grid gap-4 sm:grid-cols-2">
            <FormInput id="card-label" v-model="form.card.label" label="Small label" :error="form.errors['card.label']" :maxlength="40" />
            <FormInput id="card-name" v-model="form.card.name" label="Name" :error="form.errors['card.name']" :maxlength="80" required />
        </div>
        <FormTextarea id="card-details" v-model="form.card.details" label="Details" :rows="2" :error="form.errors['card.details']" :maxlength="200" hint="One line per row." />
        <FormInput id="card-badge" v-model="form.card.badge" label="Badge" placeholder="RCI Reg. No. A66045" :error="form.errors['card.badge']" :maxlength="60" />
        <div class="grid gap-4 sm:grid-cols-2">
            <FormInput id="card-link-label" v-model="form.card.link_label" label="Link text" :error="form.errors['card.link_label']" :maxlength="40" />
            <FormInput id="card-link-url" v-model="form.card.link_url" label="Link" placeholder="#about" :error="form.errors['card.link_url']" :maxlength="255" />
        </div>
    </FormSection>
</template>
