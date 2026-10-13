<script setup>
import Icon from '@/Components/Global/Icon.vue';
import { site } from '@/Config/site';

defineProps({
    service: { type: Object, required: true },
});
</script>

<template>
    <section class="svc-hero">
        <div class="wrap grid">
            <div v-reveal class="copy">
                <nav class="crumbs" aria-label="Breadcrumb">
                    <a href="/">Home</a><span>/</span><a href="/#services">Services</a><span>/</span><span aria-current="page">{{ service.title }}</span>
                </nav>
                <span class="eyebrow">{{ service.hero_eyebrow || service.title }}</span>
                <h1>{{ service.hero_title }}</h1>
                <p class="lead">{{ service.intro }}</p>
                <div class="actions">
                    <a href="#appointment" class="btn btn-primary">Book an Appointment <Icon name="i-arrow" :size="16" /></a>
                    <a :href="`tel:${site.phones[0].tel}`" class="btn btn-outline"><Icon name="i-phone" :size="16" />Call {{ site.phones[0].display }}</a>
                </div>
                <ul class="facts">
                    <li v-if="service.audience"><Icon name="i-check" :size="16" />For {{ service.audience }}</li>
                    <li><Icon name="i-check" :size="16" />In-clinic &amp; online</li>
                    <li><Icon name="i-check" :size="16" />Hindi &amp; English</li>
                </ul>
            </div>
            <div v-reveal class="visual">
                <div class="img"><img :src="service.hero_image || '/images/therapy-room-1.jpg'" :alt="service.title" /></div>
                <div class="badge">
                    <div class="monogram"><Icon name="i-shield" :size="22" /></div>
                    <div><b>Ms. Loveleena Sharma</b><small>RCI Registered Clinical Psychologist</small></div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.svc-hero { background: var(--cream); padding: 64px 0 96px; }
.grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 72px; align-items: center; }
.crumbs { display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.82rem; color: var(--muted); margin-bottom: 26px; }
.crumbs a:hover { color: var(--forest); }
.crumbs span[aria-current] { color: var(--forest-deep); font-weight: 400; }
h1 { font-size: clamp(2.4rem, 4.6vw, 3.7rem); margin: 18px 0 20px; }
.lead { font-size: 1.12rem; }
.actions { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 32px; }
.facts { list-style: none; display: flex; flex-wrap: wrap; gap: 10px 26px; margin-top: 30px; font-size: 0.92rem; color: var(--muted); }
.facts li { display: inline-flex; gap: 8px; align-items: center; }
.facts svg { color: var(--gold); }
.visual { position: relative; max-width: 440px; width: 100%; justify-self: center; }
.img { border-radius: 220px 220px var(--radius) var(--radius); overflow: hidden; aspect-ratio: 4/5; box-shadow: var(--shadow); background: #e8e6e2; }
.img img { width: 100%; height: 100%; object-fit: cover; }
.badge { position: absolute; left: -28px; bottom: 40px; background: var(--paper); padding: 16px 20px; border-radius: var(--radius); box-shadow: var(--shadow); display: flex; gap: 14px; align-items: center; max-width: 290px; }
.monogram { width: 48px; height: 48px; border-radius: 50%; background: var(--forest); color: var(--gold-soft); display: grid; place-items: center; flex: none; }
.badge b { font-family: var(--serif); font-size: 1.15rem; font-weight: 600; display: block; line-height: 1.2; color: var(--forest-deep); }
.badge small { font-size: 0.8rem; color: var(--muted); line-height: 1.4; display: block; }

@media (max-width: 900px) {
    .svc-hero { padding: 40px 0 72px; }
    .grid { grid-template-columns: 1fr; gap: 48px; }
    .visual { max-width: 380px; }
    .badge { left: -12px; }
}
@media (max-width: 600px) {
    h1 { font-size: 2.3rem; }
    .actions .btn { flex: 1; }
    .img { border-radius: 180px 180px var(--radius) var(--radius); }
    .badge { left: 12px; right: 12px; bottom: 18px; max-width: none; }
}
</style>
