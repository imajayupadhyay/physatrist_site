<script setup>
import Icon from './Icon.vue';
import { site, whatsappUrl } from '@/Config/site';
</script>

<!-- Sticky quick-contact bar, shown below 768px. -->
<template>
    <div class="m-cta" role="navigation" aria-label="Quick contact">
        <a :href="`tel:${site.phones[0].tel}`"><Icon name="i-phone" :size="18" />Call</a>
        <a :href="whatsappUrl()" target="_blank" rel="noopener" class="wa-btn"><Icon name="i-wa" :size="18" />WhatsApp</a>
        <a href="/#appointment" class="book"><Icon name="i-cal" :size="18" />Book Appointment</a>
    </div>
</template>

<style scoped>
.m-cta { display: none; }

@media (max-width: 768px) {
    .m-cta { display: grid; grid-template-columns: 0.8fr 1.1fr 1.5fr; gap: 8px; position: fixed; left: 0; right: 0; bottom: 0; z-index: 70; background: rgba(255, 253, 249, 0.97); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-top: 1px solid var(--line); padding: 10px 12px calc(10px + env(safe-area-inset-bottom)); box-shadow: 0 -10px 30px -18px rgba(0, 0, 0, 0.3); }
    .m-cta a { display: flex; align-items: center; justify-content: center; gap: 6px; height: 50px; padding: 0 6px; white-space: nowrap; border-radius: 14px; font-size: 0.84rem; font-weight: 500; letter-spacing: 0.03em; border: 1px solid var(--line); color: var(--forest-deep); background: var(--cream); }
    .m-cta a.wa-btn svg { color: #25d366; }
    .m-cta a.book { background: var(--forest); border-color: var(--forest); color: #fff; }
}
@media (max-width: 370px) {
    .m-cta a.book svg { display: none; }
}
</style>
