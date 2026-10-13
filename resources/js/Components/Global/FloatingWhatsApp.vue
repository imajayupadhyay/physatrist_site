<script setup>
import Icon from './Icon.vue';
import { whatsappUrl } from '@/Config/site';
</script>

<!-- Desktop/tablet only; MobileCta takes over below 768px. -->
<template>
    <a :href="whatsappUrl()" class="wa" aria-label="Chat on WhatsApp" target="_blank" rel="noopener">
        <Icon name="i-wa" :size="28" />
    </a>
</template>

<style scoped>
.wa { position: fixed; right: 22px; bottom: 22px; z-index: 60; width: 58px; height: 58px; border-radius: 50%; background: #25d366; color: #fff; display: grid; place-items: center; box-shadow: 0 12px 30px -10px rgba(0, 0, 0, 0.4); transition: transform 0.3s; }
.wa:hover { transform: scale(1.08); }

@media (max-width: 768px) {
    .wa { display: none; }
}
</style>
