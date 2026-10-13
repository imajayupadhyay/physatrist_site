<script setup>
import AppointmentSection from '@/Components/Global/AppointmentSection.vue';
import FaqSection from '@/Components/Global/FaqSection.vue';
import SeoHead from '@/Components/Global/SeoHead.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AboutSection from './Components/AboutSection.vue';
import AssessmentSection from './Components/AssessmentSection.vue';
import HeroCarousel from './Components/HeroCarousel.vue';
import ServicesSection from './Components/ServicesSection.vue';
import SpaceGallery from './Components/SpaceGallery.vue';
import StepsSection from './Components/StepsSection.vue';
import TestimonialsSection from './Components/TestimonialsSection.vue';
import TrustStrip from './Components/TrustStrip.vue';
import VideosSection from './Components/VideosSection.vue';

defineOptions({ layout: AppLayout });

// Content: `home` = Admin → Home page (one entry per section), `services*` = Admin → Services.
defineProps({
    seo: { type: Object, required: true },
    home: { type: Object, required: true },
    servicesSection: { type: Object, required: true },
    services: { type: Array, required: true },
});
</script>

<template>
    <SeoHead :seo="seo" />

    <HeroCarousel v-if="home.hero.visible && home.hero.slides.length" :content="home.hero" />
    <TrustStrip v-if="home.trust.visible" :content="home.trust" />
    <AboutSection v-if="home.about.visible" :content="home.about" />
    <ServicesSection :section="servicesSection" :services="services" />
    <AssessmentSection v-if="home.assessment.visible" :content="home.assessment" />
    <StepsSection v-if="home.steps.visible" :content="home.steps" />
    <SpaceGallery v-if="home.space.visible" :content="home.space" />
    <VideosSection v-if="home.videos.visible" :content="home.videos" />
    <TestimonialsSection v-if="home.testimonials.visible && home.testimonials.items.length" :content="home.testimonials" />
    <FaqSection v-if="home.faq.visible" :faqs="home.faq.items" :eyebrow="home.faq.eyebrow" :heading="home.faq.heading" :lead="home.faq.lead" />
    <AppointmentSection />
</template>
