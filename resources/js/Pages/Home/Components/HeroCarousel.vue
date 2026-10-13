<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import PsychologistCard from './PsychologistCard.vue';

// Managed in Admin → Home page → Hero slider.
const props = defineProps({
    content: { type: Object, required: true },
});

const slides = props.content.slides;
const current = ref(0);
let timer;

const go = (i) => (current.value = (i + slides.length) % slides.length);
const restart = () => {
    clearInterval(timer);
    if (slides.length > 1) timer = setInterval(() => go(current.value + 1), (props.content.interval || 6.5) * 1000);
};
const goAndRestart = (i) => {
    go(i);
    restart();
};

onMounted(restart);
onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <div id="top" class="hero-wrap">
        <section class="hero" aria-label="Welcome">
            <div v-for="(slide, i) in slides" :key="i" class="slide" :class="{ active: i === current }">
                <img :src="slide.image" :alt="slide.alt" />
                <div class="slide-content">
                    <div class="wrap">
                        <div class="inner">
                            <span v-if="slide.eyebrow" class="eyebrow">{{ slide.eyebrow }}</span>
                            <h1>{{ slide.title }}<template v-if="slide.title_em"><br /><em>{{ slide.title_em }}</em></template></h1>
                            <p v-if="slide.text">{{ slide.text }}</p>
                            <div v-if="slide.primary_label || slide.secondary_label" class="actions">
                                <a v-if="slide.primary_label" :href="slide.primary_url" class="btn btn-gold">{{ slide.primary_label }}</a>
                                <a v-if="slide.secondary_label" :href="slide.secondary_url" class="btn btn-ghost">{{ slide.secondary_label }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="slides.length > 1" class="hero-controls">
                <div class="wrap">
                    <div class="dots">
                        <button
                            v-for="(slide, i) in slides"
                            :key="i"
                            :class="{ active: i === current }"
                            :aria-label="`Go to slide ${i + 1}`"
                            @click="goAndRestart(i)"
                        />
                    </div>
                    <div class="arrows">
                        <button aria-label="Previous slide" @click="goAndRestart(current - 1)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M15 18l-6-6 6-6" /></svg>
                        </button>
                        <button aria-label="Next slide" @click="goAndRestart(current + 1)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M9 18l6-6-6-6" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Floats over the hero on desktop, becomes a band below it on tablet/mobile -->
        <PsychologistCard v-if="content.card.visible" :card="content.card" />
    </div>
</template>

<style scoped>
.hero-wrap { position: relative; }
.hero { position: relative; height: min(88vh, 780px); min-height: 580px; overflow: hidden; background: var(--forest-deep); padding: 0; }
.slide { position: absolute; inset: 0; opacity: 0; transition: opacity 1.2s ease; }
.slide.active { opacity: 1; z-index: 1; }
.slide img { width: 100%; height: 100%; object-fit: cover; transform: scale(1.08); transition: transform 7s ease; }
.slide.active img { transform: scale(1); }
.slide::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(20, 32, 25, 0.88) 0%, rgba(20, 32, 25, 0.6) 45%, rgba(20, 32, 25, 0.2) 100%); }
.slide-content { position: absolute; inset: 0; z-index: 2; display: flex; align-items: center; }
.slide-content .inner { max-width: 600px; color: #fff; }
.slide-content .eyebrow { color: var(--gold-soft); }
.slide-content h1 { color: #fff; font-size: clamp(2.6rem, 5.6vw, 4.5rem); margin: 20px 0 22px; }
.slide-content h1 em { font-style: italic; color: var(--gold-soft); }
.slide-content p { font-size: 1.12rem; color: rgba(255, 255, 255, 0.86); max-width: 46ch; margin-bottom: 36px; }
.slide-content .actions { display: flex; gap: 14px; flex-wrap: wrap; }
.slide .inner > * { opacity: 0; transform: translateY(24px); transition: all 0.9s ease; }
.slide.active .inner > * { opacity: 1; transform: none; }
.slide.active .inner > *:nth-child(2) { transition-delay: 0.15s; }
.slide.active .inner > *:nth-child(3) { transition-delay: 0.3s; }
.slide.active .inner > *:nth-child(4) { transition-delay: 0.45s; }
.hero-controls { position: absolute; z-index: 5; bottom: 40px; left: 0; right: 0; }
.hero-controls .wrap { display: flex; align-items: center; justify-content: space-between; }
.dots { display: flex; gap: 10px; }
.dots button { width: 34px; height: 3px; border: 0; background: rgba(255, 255, 255, 0.35); cursor: pointer; border-radius: 2px; transition: all 0.4s; }
.dots button.active { background: var(--gold); width: 58px; }
.arrows { display: flex; gap: 10px; }
.arrows button { width: 48px; height: 48px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, 0.4); background: transparent; color: #fff; cursor: pointer; transition: all 0.3s; display: grid; place-items: center; }
.arrows button:hover { background: #fff; color: var(--forest-deep); }

@media (max-width: 600px) {
    .hero { height: calc(100svh - 120px); min-height: 560px; max-height: 720px; }
    .slide::after { background: linear-gradient(180deg, rgba(20, 32, 25, 0.55) 0%, rgba(20, 32, 25, 0.75) 55%, rgba(20, 32, 25, 0.9) 100%); }
    .slide-content { align-items: flex-end; padding-bottom: 96px; }
    .slide-content h1 { font-size: 2.5rem; margin: 14px 0 16px; }
    .slide-content p { font-size: 1rem; margin-bottom: 26px; }
    .slide-content .actions .btn { padding: 14px 22px; flex: 1; }
    .hero-controls { bottom: 32px; }
    .hero-controls .arrows { display: none; }
}
</style>
