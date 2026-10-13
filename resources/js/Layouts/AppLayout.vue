<script setup>
import AppFooter from '@/Components/Global/AppFooter.vue';
import AppHeader from '@/Components/Global/AppHeader.vue';
import FloatingWhatsApp from '@/Components/Global/FloatingWhatsApp.vue';
import MobileCta from '@/Components/Global/MobileCta.vue';
import SvgSprite from '@/Components/Global/SvgSprite.vue';
</script>

<template>
    <SvgSprite />
    <AppHeader />
    <main>
        <slot />
    </main>
    <AppFooter />
    <FloatingWhatsApp />
    <MobileCta />
</template>
