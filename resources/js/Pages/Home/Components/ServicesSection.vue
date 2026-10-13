<script setup>
import Icon from '@/Components/Global/Icon.vue';
import { iconMarkup } from '@/Shared/serviceIcons';

// Managed in the admin: Services (cards, order, visibility) + Services → Homepage section (text).
defineProps({
    section: { type: Object, required: true },
    services: { type: Array, required: true },
});
</script>

<template>
    <section id="services" class="services">
        <div class="wrap">
            <div v-reveal class="head center">
                <span v-if="section.eyebrow" class="eyebrow">{{ section.eyebrow }}</span>
                <h2>{{ section.heading }}</h2>
                <p v-if="section.lead" class="lead">{{ section.lead }}</p>
            </div>
            <div class="svc-grid">
                <article v-for="svc in services" :key="svc.id" v-reveal class="svc" :class="{ feature: svc.is_featured }">
                    <div class="ico">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="iconMarkup(svc.icon)" />
                    </div>
                    <h3>{{ svc.title }}</h3>
                    <span v-if="svc.audience" class="for">{{ svc.audience }}</span>
                    <p>{{ svc.summary }}</p>
                    <a :href="`/services/${svc.slug}`" class="more">{{ section.card_link_label || 'Learn more' }} →</a>
                </article>
            </div>

            <div v-if="section.also_items?.length || section.notes?.length" v-reveal class="svc-note">
                <p v-if="section.also_items?.length">{{ section.also_label }}</p>
                <div v-if="section.also_items?.length" class="chips">
                    <span v-for="item in section.also_items" :key="item">{{ item }}</span>
                </div>
                <div v-if="section.notes?.length" class="svc-mode">
                    <span v-for="note in section.notes" :key="note"><Icon name="i-check" :size="16" />{{ note }}</span>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.services { background: var(--paper); }
.svc-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
.svc { background: var(--cream); border: 1px solid var(--line); border-radius: var(--radius); padding: 32px 26px 28px; transition: all 0.4s ease; display: flex; flex-direction: column; gap: 12px; position: relative; overflow: hidden; }
.svc::before { content: ''; position: absolute; inset: auto 0 0 0; height: 3px; background: var(--gold); transform: scaleX(0); transform-origin: left; transition: transform 0.4s; }
.svc:hover { transform: translateY(-6px); box-shadow: var(--shadow); background: #fff; }
.svc:hover::before { transform: scaleX(1); }
.svc .ico { width: 54px; height: 54px; border-radius: 50%; background: var(--sage-soft); display: grid; place-items: center; color: var(--forest); margin-bottom: 6px; }
.svc h3 { font-size: 1.42rem; }
.svc .for { font-size: 0.72rem; letter-spacing: 0.12em; white-space: nowrap; text-transform: uppercase; color: var(--gold); font-weight: 500; margin-top: -4px; }
.svc p { font-size: 0.93rem; color: var(--muted); line-height: 1.6; }
.svc .more { margin-top: auto; padding-top: 8px; font-size: 0.85rem; font-weight: 500; letter-spacing: 0.06em; color: var(--forest); display: inline-flex; gap: 8px; align-items: center; }
.svc .more:hover { color: var(--gold); }
.svc.feature { background: var(--forest); border-color: var(--forest); }
.svc.feature h3 { color: #fff; }
.svc.feature p { color: #c8d2c6; }
.svc.feature .ico { background: rgba(255, 255, 255, 0.1); color: var(--gold-soft); }
.svc.feature .for { color: var(--gold-soft); }
.svc.feature .more { color: var(--gold-soft); }
.svc.feature:hover { background: var(--forest-deep); }
.svc-note { margin-top: 56px; padding-top: 44px; border-top: 1px solid var(--line); display: grid; grid-template-columns: auto 1fr; gap: 20px 40px; align-items: center; }
.svc-note p { font-size: 0.8rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--muted); font-weight: 400; }
.chips { display: flex; flex-wrap: wrap; gap: 10px; }
.chips span { padding: 8px 18px; border-radius: 999px; border: 1px solid var(--line); font-size: 0.9rem; background: var(--cream); line-height: 1.5; }
.svc-mode { grid-column: 1/-1; display: flex; flex-wrap: wrap; gap: 10px 28px; font-size: 0.92rem; color: var(--muted); }
.svc-mode span { display: inline-flex; gap: 8px; align-items: center; }
.svc-mode svg { color: var(--gold); }

@media (min-width: 1101px) {
    .svc h3 { min-height: 2.24em; }
}
@media (max-width: 1100px) {
    .svc-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 900px) {
    .svc-note { grid-template-columns: 1fr; gap: 16px; }
}
@media (max-width: 600px) {
    .svc-grid { grid-template-columns: 1fr; gap: 16px; }
    .svc { padding: 28px 24px 24px; }
}
</style>
