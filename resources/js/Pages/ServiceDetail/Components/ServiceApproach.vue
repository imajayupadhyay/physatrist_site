<script setup>
defineProps({
    heading: { type: String, default: '' },
    steps: { type: Array, required: true },
});
</script>

<template>
    <section class="approach">
        <div class="wrap">
            <div v-reveal class="head center">
                <span class="eyebrow">How we help</span>
                <h2>{{ heading || 'What to expect' }}</h2>
            </div>
            <ol class="steps" :style="{ '--cols': Math.min(steps.length, 4) }">
                <li v-for="step in steps" :key="step.title" v-reveal>
                    <h3>{{ step.title }}</h3>
                    <p>{{ step.text }}</p>
                </li>
            </ol>
        </div>
    </section>
</template>

<style scoped>
.approach { background: var(--cream); }
.steps { list-style: none; display: grid; grid-template-columns: repeat(var(--cols), 1fr); gap: 22px; counter-reset: s; }
.steps li { background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius); padding: 32px 28px; position: relative; }
.steps li::before { counter-increment: s; content: '0' counter(s); font-family: var(--serif); font-size: 2.6rem; color: var(--gold); line-height: 1; display: block; margin-bottom: 18px; }
.steps h3 { font-size: 1.4rem; margin-bottom: 8px; }
.steps p { font-size: 0.94rem; color: var(--muted); line-height: 1.6; }

@media (max-width: 1100px) {
    .steps { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .steps { grid-template-columns: 1fr; gap: 16px; }
    .steps li { padding: 26px 24px; display: grid; grid-template-columns: auto 1fr; column-gap: 20px; }
    .steps li::before { grid-row: span 2; margin-bottom: 0; font-size: 2.2rem; }
}
</style>
