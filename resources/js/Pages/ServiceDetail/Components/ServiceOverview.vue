<script setup>
import { computed } from 'vue';
import Icon from '@/Components/Global/Icon.vue';

const props = defineProps({
    service: { type: Object, required: true },
});

// Blank lines in the admin text become paragraphs.
const paragraphs = computed(() => (props.service.overview_body || '').split(/\n\s*\n/).map((p) => p.trim()).filter(Boolean));
</script>

<template>
    <section class="overview">
        <div class="wrap grid" :class="{ single: !service.issues.length }">
            <div v-if="paragraphs.length" v-reveal class="body">
                <span class="eyebrow">About this service</span>
                <h2>{{ service.overview_heading || `About ${service.title}` }}</h2>
                <p v-for="(paragraph, i) in paragraphs" :key="i" class="lead">{{ paragraph }}</p>
            </div>
            <aside v-if="service.issues.length" v-reveal class="issues">
                <h3>{{ service.issues_heading || 'What we help with' }}</h3>
                <ul>
                    <li v-for="issue in service.issues" :key="issue"><Icon name="i-check" :size="18" />{{ issue }}</li>
                </ul>
                <a href="#appointment" class="link">Book a consultation <Icon name="i-arrow" :size="14" /></a>
            </aside>
        </div>
    </section>
</template>

<style scoped>
.overview { background: var(--paper); }
.grid { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 72px; align-items: start; }
.grid.single { grid-template-columns: 1fr; max-width: 820px; }
h2 { margin: 16px 0 22px; }
.lead { max-width: 62ch; }
.lead + .lead { margin-top: 16px; }
.issues { background: var(--cream); border: 1px solid var(--line); border-radius: var(--radius); padding: 36px 34px; position: sticky; top: calc(var(--nav-h) + 24px); }
.issues h3 { font-size: 1.6rem; margin-bottom: 20px; }
.issues ul { list-style: none; display: flex; flex-direction: column; gap: 13px; }
.issues li { display: flex; gap: 12px; align-items: flex-start; font-size: 0.98rem; line-height: 1.5; }
.issues li svg { flex: none; color: var(--gold); margin-top: 3px; }
.link { display: inline-flex; align-items: center; gap: 8px; margin-top: 26px; padding-top: 18px; border-top: 1px solid var(--line); width: 100%; font-size: 0.88rem; font-weight: 500; letter-spacing: 0.05em; color: var(--forest); }
.link:hover { color: var(--gold); }

@media (max-width: 900px) {
    .grid { grid-template-columns: 1fr; gap: 40px; }
    .issues { position: static; }
}
@media (max-width: 600px) {
    .issues { padding: 28px 22px; }
}
</style>
