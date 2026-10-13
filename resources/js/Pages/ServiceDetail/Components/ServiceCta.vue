<script setup>
import Icon from '@/Components/Global/Icon.vue';
import { site, whatsappUrl } from '@/Config/site';

defineProps({
    heading: { type: String, default: '' },
    text: { type: String, default: '' },
});
</script>

<template>
    <section class="cta">
        <Icon name="logo" class="leaf-bg" viewBox="0 0 64 64" aria-hidden="true" />
        <div v-reveal class="wrap inner">
            <div>
                <h2>{{ heading || 'Take the first step today' }}</h2>
                <p v-if="text">{{ text }}</p>
            </div>
            <div class="actions">
                <a href="#appointment" class="btn btn-gold">Book an Appointment</a>
                <a :href="whatsappUrl()" target="_blank" rel="noopener" class="btn btn-line-light"><Icon name="i-wa" :size="16" />WhatsApp us</a>
            </div>
        </div>
    </section>
</template>

<style scoped>
.cta { background: var(--forest-deep); position: relative; overflow: hidden; padding: 88px 0; }
.inner { display: flex; align-items: center; justify-content: space-between; gap: 40px; position: relative; }
h2 { color: #fff; font-size: clamp(2rem, 3.6vw, 2.8rem); }
p { color: #b8c4b6; margin-top: 12px; max-width: 54ch; }
.actions { display: flex; gap: 14px; flex-wrap: wrap; flex: none; }
.leaf-bg { position: absolute; right: -60px; top: -80px; width: 380px; opacity: 0.06; color: #fff; pointer-events: none; }

@media (max-width: 900px) {
    .inner { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 600px) {
    .cta { padding: 68px 0; }
    .actions { width: 100%; }
    .actions .btn { flex: 1; }
}
</style>
