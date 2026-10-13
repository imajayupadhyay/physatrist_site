<script setup>
import Icon from './Icon.vue';

defineProps({
    // Light text for dark backgrounds (footer).
    onDark: { type: Boolean, default: false },
    // From the header settings: { url, alt, height, name, tagline }. Omitted = default brand.
    logo: { type: Object, default: null },
});
</script>

<template>
    <a href="/#top" class="brand" :class="{ 'on-dark': onDark }" :aria-label="`${logo?.alt || 'Mind Essence Wellness Centre'}, back to top`">
        <img v-if="logo?.url" :src="logo.url" :alt="logo.alt" class="brand-img" :style="{ height: `${logo.height || 46}px` }" />
        <template v-else>
            <Icon name="logo" :size="onDark ? 46 : undefined" />
            <span class="brand-text"><b>{{ logo?.name || 'Mind Essence' }}</b><small v-if="logo?.tagline !== ''">{{ logo?.tagline || 'WELLNESS CENTRE' }}</small></span>
        </template>
    </a>
</template>

<style scoped>
.brand { display: flex; align-items: center; gap: 12px; }
.brand svg { width: 46px; height: 46px; flex: none; color: var(--forest); }
.brand-img { width: auto; max-width: 220px; object-fit: contain; }
.brand-text { display: flex; flex-direction: column; line-height: 1; }
.brand-text b { font-family: var(--serif); font-weight: 600; font-size: 1.65rem; color: var(--forest-deep); }
.brand-text small { font-size: 0.62rem; letter-spacing: 0.32em; color: var(--muted); margin-top: 5px; font-weight: 400; text-transform: uppercase; }

.on-dark svg { color: #c9d5c4; }
.on-dark .brand-text b { color: #fff; }
.on-dark .brand-text small { color: #9fb09c; }

@media (max-width: 600px) {
    .brand svg { width: 40px; height: 40px; }
    .brand-text b { font-size: 1.45rem; }
    .brand-img { max-height: 40px; max-width: 170px; }
}
</style>
