<script setup>
import { computed } from 'vue';
import Icon from '@/Components/Global/Icon.vue';
import { whatsappUrl } from '@/Config/site';

const props = defineProps({
    content: { type: Object, required: true },
});

// Blank lines in the admin text become paragraphs.
const paragraphs = computed(() => (props.content.body || '').split(/\n\s*\n/).map((p) => p.trim()).filter(Boolean));
const secondaryHref = computed(() => props.content.secondary_url || whatsappUrl());
const secondaryExternal = computed(() => /^https?:/.test(secondaryHref.value));
</script>

<template>
    <section id="about" class="about">
        <div class="wrap grid">
            <div v-reveal class="about-visual">
                <div class="main-img"><img :src="content.image" :alt="content.image_alt" /></div>
                <div v-if="content.badge_title" class="about-badge">
                    <div class="monogram"><Icon name="i-shield" :size="24" /></div>
                    <div><b>{{ content.badge_title }}</b><small v-if="content.badge_text">{{ content.badge_text }}</small></div>
                </div>
            </div>
            <div v-reveal class="about-copy">
                <span v-if="content.eyebrow" class="eyebrow">{{ content.eyebrow }}</span>
                <h2>{{ content.heading }}</h2>
                <div v-if="content.roles.length" class="role"><span v-for="role in content.roles" :key="role">{{ role }}</span></div>
                <p v-for="(paragraph, i) in paragraphs" :key="i" class="lead">{{ paragraph }}</p>
                <blockquote v-if="content.quote">“{{ content.quote }}”</blockquote>
                <div v-if="content.credentials.length" class="creds">
                    <div v-for="cred in content.credentials" :key="cred"><Icon name="i-check" :size="18" />{{ cred }}</div>
                </div>
                <div class="about-actions">
                    <a v-if="content.primary_label" :href="content.primary_url" class="btn btn-primary">{{ content.primary_label }} <Icon name="i-arrow" :size="16" /></a>
                    <a v-if="content.secondary_label" :href="secondaryHref" :target="secondaryExternal ? '_blank' : null" :rel="secondaryExternal ? 'noopener' : null" class="btn btn-outline">{{ content.secondary_label }}</a>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.about .grid { display: grid; grid-template-columns: 0.95fr 1.05fr; gap: 80px; align-items: center; }
.about-visual { position: relative; max-width: 480px; width: 100%; justify-self: center; }
.about-visual .main-img { border-radius: 240px 240px var(--radius) var(--radius); overflow: hidden; aspect-ratio: 4/5; box-shadow: var(--shadow); background: #e8e6e2; }
.about-visual .main-img img { width: 100%; height: 100%; object-fit: cover; object-position: center 10%; }
.about-badge { position: absolute; right: -24px; bottom: 44px; background: var(--paper); padding: 18px 22px; border-radius: var(--radius); box-shadow: var(--shadow); display: flex; gap: 14px; align-items: center; max-width: 270px; }
.monogram { width: 52px; height: 52px; border-radius: 50%; background: var(--forest); color: var(--gold-soft); display: grid; place-items: center; flex: none; }
.about-badge b { font-family: var(--serif); font-size: 1.2rem; font-weight: 600; display: block; line-height: 1.2; color: var(--forest-deep); }
.about-badge small { font-size: 0.8rem; color: var(--muted); line-height: 1.4; display: block; }
.about-copy h2 { margin: 16px 0 10px; }
.role { display: flex; flex-direction: column; gap: 4px; font-size: 0.82rem; letter-spacing: 0.14em; text-transform: uppercase; color: var(--forest); font-weight: 500; margin-bottom: 26px; padding-left: 16px; border-left: 2px solid var(--gold); }
.about-copy .lead + .lead { margin-top: 14px; }
.about-copy blockquote { font-family: var(--serif); font-style: italic; font-size: 1.5rem; line-height: 1.4; color: var(--forest); border-left: 2px solid var(--gold); padding-left: 22px; margin: 30px 0; }
.creds { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 24px; margin: 0 0 36px; }
.creds div { display: flex; gap: 12px; align-items: flex-start; font-size: 0.95rem; line-height: 1.5; }
.creds svg { flex: none; color: var(--gold); margin-top: 3px; }
.about-actions { display: flex; gap: 14px; flex-wrap: wrap; }

@media (max-width: 1100px) {
    .about .grid { gap: 56px; }
}
@media (max-width: 900px) {
    .about .grid { grid-template-columns: 1fr; }
    .about-visual { max-width: 420px; }
    .about-badge { right: -12px; }
}
@media (max-width: 600px) {
    .about-badge { right: 12px; left: 12px; bottom: 20px; max-width: none; }
    .about-visual .main-img { border-radius: 200px 200px var(--radius) var(--radius); }
    .about-copy blockquote { font-size: 1.3rem; }
    .creds { grid-template-columns: 1fr; }
    .about-actions .btn { flex: 1; }
}
</style>
