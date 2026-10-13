<script setup>
import { iconMarkup } from '@/Shared/serviceIcons';

defineProps({
    services: { type: Array, required: true },
});
</script>

<template>
    <section class="related">
        <div class="wrap">
            <div v-reveal class="head">
                <span class="eyebrow">Explore more</span>
                <h2>Other ways we can help</h2>
            </div>
            <div class="grid">
                <a v-for="svc in services" :key="svc.slug" v-reveal :href="`/services/${svc.slug}`" class="card">
                    <span class="ico"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="iconMarkup(svc.icon)" /></span>
                    <h3>{{ svc.title }}</h3>
                    <p>{{ svc.summary }}</p>
                    <span class="more">Learn more →</span>
                </a>
            </div>
        </div>
    </section>
</template>

<style scoped>
.related { background: var(--cream); }
.grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
.card { background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px 26px; display: flex; flex-direction: column; gap: 10px; transition: box-shadow 0.4s ease, border-color 0.4s ease; }
.card:hover { box-shadow: var(--shadow); border-color: transparent; }
.ico { width: 50px; height: 50px; border-radius: 50%; background: var(--sage-soft); display: grid; place-items: center; color: var(--forest); margin-bottom: 4px; }
.card h3 { font-size: 1.38rem; }
.card p { font-size: 0.93rem; color: var(--muted); line-height: 1.6; }
.more { margin-top: auto; padding-top: 6px; font-size: 0.85rem; font-weight: 500; letter-spacing: 0.06em; color: var(--forest); }
.card:hover .more { color: var(--gold); }

@media (max-width: 900px) {
    .grid { grid-template-columns: 1fr; gap: 16px; }
}
</style>
