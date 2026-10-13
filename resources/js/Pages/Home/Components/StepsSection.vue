<script setup>
import Icon from '@/Components/Global/Icon.vue';

defineProps({
    content: { type: Object, required: true },
});
</script>

<template>
    <section class="steps">
        <Icon name="logo" class="leaf-bg" viewBox="0 0 64 64" aria-hidden="true" />
        <div class="wrap">
            <div v-reveal class="head center">
                <span v-if="content.eyebrow" class="eyebrow">{{ content.eyebrow }}</span>
                <h2>{{ content.heading }}</h2>
                <p v-if="content.lead" class="lead">{{ content.lead }}</p>
            </div>
            <div class="step-grid">
                <div v-for="(step, i) in content.items" :key="i" v-reveal class="step"><h3>{{ step.title }}</h3><p>{{ step.text }}</p></div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.steps { background: var(--forest-deep); color: #dfe5dc; position: relative; overflow: hidden; }
.steps h2 { color: #fff; }
.steps .lead { color: #b8c4b6; }
.steps .eyebrow { color: var(--gold-soft); }
.step-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; counter-reset: s; }
.step { padding: 40px 34px; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: var(--radius); position: relative; }
.step::before { counter-increment: s; content: '0' counter(s); font-family: var(--serif); font-size: 3.4rem; color: var(--gold); line-height: 1; display: block; margin-bottom: 22px; }
.step h3 { color: #fff; margin-bottom: 10px; }
.step p { font-size: 0.95rem; color: #b8c4b6; }
.leaf-bg { position: absolute; right: -80px; top: -60px; width: 420px; opacity: 0.06; color: #fff; pointer-events: none; }

@media (max-width: 900px) {
    .step-grid { grid-template-columns: 1fr; gap: 18px; }
    .step { padding: 30px 28px; display: grid; grid-template-columns: auto 1fr; column-gap: 24px; }
    .step::before { grid-row: span 2; margin-bottom: 0; font-size: 2.8rem; }
}
</style>
