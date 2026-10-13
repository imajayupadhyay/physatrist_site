<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

// Managed in Admin → Home page → Testimonials.
const props = defineProps({
    content: { type: Object, required: true },
});

const testimonials = props.content.items;
const current = ref(0);
let timer;

const go = (i) => (current.value = i % testimonials.length);
const restart = () => {
    clearInterval(timer);
    if (testimonials.length > 1) timer = setInterval(() => go(current.value + 1), (props.content.interval || 7) * 1000);
};
const goAndRestart = (i) => {
    go(i);
    restart();
};

onMounted(restart);
onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <section id="stories" class="testi">
        <div class="wrap">
            <div v-reveal class="head center">
                <span v-if="content.eyebrow" class="eyebrow">{{ content.eyebrow }}</span>
                <h2>{{ content.heading }}</h2>
            </div>
            <div v-reveal class="t-wrap">
                <div class="t-quote-mark" aria-hidden="true">“</div>
                <div class="t-track">
                    <div v-for="(t, i) in testimonials" :key="i" class="t-item" :class="{ active: i === current }">
                        <div class="stars" :aria-label="`${t.rating} out of 5`">{{ '★'.repeat(t.rating) }}</div>
                        <p>{{ t.quote }}</p>
                        <div class="who"><b>{{ t.name }}</b><template v-if="t.service"> · {{ t.service }}</template></div>
                    </div>
                </div>
                <div v-if="testimonials.length > 1" class="t-nav">
                    <button
                        v-for="(t, i) in testimonials"
                        :key="i"
                        :class="{ active: i === current }"
                        :aria-label="`Testimonial ${i + 1}`"
                        @click="goAndRestart(i)"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.testi { background: var(--sage-soft); }
.t-wrap { max-width: 860px; margin-inline: auto; text-align: center; position: relative; }
.t-quote-mark { font-family: var(--serif); font-size: 7rem; line-height: 0.6; color: var(--gold); height: 50px; }
.t-track { position: relative; display: grid; }
.t-item { grid-area: 1/1; opacity: 0; transform: translateY(16px); transition: all 0.8s ease; pointer-events: none; visibility: hidden; }
.t-item.active { opacity: 1; transform: none; pointer-events: auto; visibility: visible; }
.t-item p { font-family: var(--serif); font-size: clamp(1.4rem, 2.6vw, 2rem); line-height: 1.45; color: var(--forest-deep); font-style: italic; }
.t-item .who { margin-top: 28px; font-size: 0.92rem; color: var(--muted); letter-spacing: 0.05em; }
.t-item .who b { color: var(--forest); font-weight: 500; }
.stars { color: var(--gold); letter-spacing: 4px; margin-bottom: 8px; }
.t-nav { display: flex; justify-content: center; gap: 10px; margin-top: 34px; }
.t-nav button { width: 10px; height: 10px; border-radius: 50%; border: 0; background: #c3cdbf; cursor: pointer; transition: all 0.3s; }
.t-nav button.active { background: var(--forest); width: 30px; border-radius: 5px; }

@media (max-width: 600px) {
    .t-quote-mark { font-size: 5.5rem; height: 40px; }
}
</style>
