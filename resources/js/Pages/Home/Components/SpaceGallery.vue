<script setup>
defineProps({
    content: { type: Object, required: true },
});

// Fixed layout: 1 tall photo, 1 wide, 2 small.
const layout = ['tall', 'wide', '', ''];
</script>

<template>
    <section id="space" class="space">
        <div class="wrap">
            <div v-reveal class="head">
                <span v-if="content.eyebrow" class="eyebrow">{{ content.eyebrow }}</span>
                <h2>{{ content.heading }}</h2>
                <p v-if="content.lead" class="lead">{{ content.lead }}</p>
            </div>
            <div class="grid">
                <figure v-for="(photo, i) in content.photos" :key="i" v-reveal class="ph" :class="layout[i]">
                    <img :src="photo.image" :alt="photo.alt" :style="i === 1 ? { objectPosition: 'center 35%' } : null" loading="lazy" />
                    <figcaption v-if="photo.caption">{{ photo.caption }}</figcaption>
                </figure>
            </div>
        </div>
    </section>
</template>

<style scoped>
.space .grid { display: grid; grid-template-columns: 1.2fr 1fr 1fr; grid-template-rows: 280px 280px; gap: 18px; }
.space .ph { border-radius: var(--radius); overflow: hidden; position: relative; }
.space .ph img { width: 100%; height: 100%; object-fit: cover; transition: transform 1s ease; }
.space .ph:hover img { transform: scale(1.05); }
.space .ph.tall { grid-row: span 2; }
.space .ph.wide { grid-column: span 2; }
.space .ph figcaption { position: absolute; left: 16px; bottom: 16px; background: rgba(255, 253, 249, 0.92); padding: 7px 15px; border-radius: 999px; font-size: 0.8rem; letter-spacing: 0.06em; }

@media (max-width: 900px) {
    .space .grid { grid-template-columns: 1fr 1fr; grid-template-rows: repeat(3, 220px); }
    .space .ph.tall { grid-row: span 1; }
}
@media (max-width: 600px) {
    .space .grid { grid-template-columns: 1fr; grid-template-rows: none; grid-auto-rows: 240px; }
    .space .ph.wide { grid-column: span 1; }
}
</style>
