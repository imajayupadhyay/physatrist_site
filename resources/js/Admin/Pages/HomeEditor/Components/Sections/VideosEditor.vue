<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ImageUpload from '@/Admin/Components/Global/ImageUpload.vue';
import ItemList from '@/Admin/Components/Global/ItemList.vue';

defineProps({
    form: { type: Object, required: true },
});

const newReel = () => ({ platform: 'youtube', video: '', image: '/images/therapy-room-2.jpg', cover: '', cover_em: '', topic: '', title: '' });
</script>

<template>
    <FormSection id="heading" title="Heading & channel">
        <FormInput id="v-eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
        <FormInput id="v-heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="120" required />
        <FormTextarea id="v-lead" v-model="form.lead" label="Intro text" :rows="2" :error="form.errors.lead" :maxlength="300" />
        <div class="grid gap-4 border-t border-slate-100 pt-5 sm:grid-cols-[180px_1fr]">
            <ImageUpload id="v-channel" v-model:file="form.channel_image_file" label="Channel photo" :current-url="form.channel_image" :removable="false" aspect="aspect-square" :error="form.errors.channel_image_file" hint="Small round photo." />
            <div class="space-y-4">
                <FormInput id="v-channel-name" v-model="form.channel_name" label="Channel name" :error="form.errors.channel_name" :maxlength="60" required />
                <FormInput id="v-channel-sub" v-model="form.channel_subtitle" label="Channel subtitle" :error="form.errors.channel_subtitle" :maxlength="80" />
                <p class="text-xs text-slate-400">The YouTube and Instagram buttons use the profiles set in Footer → Contact &amp; social.</p>
            </div>
        </div>
    </FormSection>

    <FormSection id="reels" title="Videos" description="YouTube videos play right on the page; Instagram reels open on Instagram. 3 fit best.">
        <ItemList v-model="form.reels" v-slot="{ item, index: i }" :new-item="newReel" item-label="Video" add-label="Add video" title-key="title" :max="6" :errors="form.errors" error-key="reels">
            <div>
                <p class="mb-1.5 text-sm font-medium text-slate-700">Platform</p>
                <div class="grid max-w-xs grid-cols-2 gap-1 rounded-lg bg-slate-100 p-1 text-sm">
                    <button type="button" class="rounded-md py-1.5 font-medium transition" :class="item.platform === 'youtube' ? 'bg-white text-forest shadow-sm' : 'text-slate-500'" @click="item.platform = 'youtube'">YouTube</button>
                    <button type="button" class="rounded-md py-1.5 font-medium transition" :class="item.platform === 'instagram' ? 'bg-white text-forest shadow-sm' : 'text-slate-500'" @click="item.platform = 'instagram'">Instagram</button>
                </div>
            </div>
            <FormInput
                :id="`reel-${i}-video`"
                v-model="item.video"
                :label="item.platform === 'youtube' ? 'YouTube link' : 'Instagram link'"
                :placeholder="item.platform === 'youtube' ? 'https://youtu.be/…' : 'https://www.instagram.com/reel/…'"
                :error="form.errors[`reels.${i}.video`]"
                :maxlength="255"
                required
            />
            <div class="grid gap-4 sm:grid-cols-[180px_1fr]">
                <ImageUpload :id="`reel-${i}-image`" v-model:file="item.image_file" label="Cover photo" :current-url="item.image" :removable="false" aspect="aspect-[4/5]" :error="form.errors[`reels.${i}.image_file`]" hint="Shown faded in green behind the text." />
                <div class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <FormInput :id="`reel-${i}-cover`" v-model="item.cover" label="Cover text, line 1" :error="form.errors[`reels.${i}.cover`]" :maxlength="40" required />
                        <FormInput :id="`reel-${i}-cover-em`" v-model="item.cover_em" label="Cover text, line 2 (gold)" :error="form.errors[`reels.${i}.cover_em`]" :maxlength="40" />
                    </div>
                    <FormInput :id="`reel-${i}-topic`" v-model="item.topic" label="Topic label" :error="form.errors[`reels.${i}.topic`]" :maxlength="40" />
                    <FormInput :id="`reel-${i}-title`" v-model="item.title" label="Title" :error="form.errors[`reels.${i}.title`]" :maxlength="80" required />
                </div>
            </div>
        </ItemList>
    </FormSection>
</template>
