<script setup>
import AppointmentSection from '@/Components/Global/AppointmentSection.vue';
import FaqSection from '@/Components/Global/FaqSection.vue';
import SeoHead from '@/Components/Global/SeoHead.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import RelatedServices from './Components/RelatedServices.vue';
import ServiceApproach from './Components/ServiceApproach.vue';
import ServiceCta from './Components/ServiceCta.vue';
import ServiceHero from './Components/ServiceHero.vue';
import ServiceOverview from './Components/ServiceOverview.vue';

defineOptions({ layout: AppLayout });

// Template for every /services/{slug} page. Content is managed in the admin (Services).
defineProps({
    seo: { type: Object, required: true },
    service: { type: Object, required: true },
    otherServices: { type: Array, default: () => [] },
    isPreview: { type: Boolean, default: false },
});
</script>

<template>
    <SeoHead :seo="seo" />

    <div v-if="isPreview" class="preview-bar">Draft preview: this page is not published and only visible to logged-in admins.</div>

    <ServiceHero :service="service" />
    <ServiceOverview :service="service" />
    <ServiceApproach v-if="service.approach.length" :heading="service.approach_heading" :steps="service.approach" />
    <ServiceCta :heading="service.cta_heading" :text="service.cta_text" />
    <FaqSection
        v-if="service.faqs.length"
        id="service-faq"
        eyebrow="FAQ"
        :heading="`${service.title}: your questions`"
        lead="Answers to what people often ask before starting. If your question isn't here, we're happy to help."
        :faqs="service.faqs"
    />
    <AppointmentSection :concern="service.concern" />
    <RelatedServices v-if="otherServices.length" :services="otherServices" />
</template>

<style scoped>
.preview-bar { background: var(--gold); color: #fff; text-align: center; font-size: 0.88rem; font-weight: 500; padding: 10px 16px; letter-spacing: 0.02em; }
</style>
