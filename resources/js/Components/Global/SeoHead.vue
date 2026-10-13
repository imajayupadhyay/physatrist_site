<script setup>
import { Head } from '@inertiajs/vue3';

// Renders a page's `seo` prop (built by App\Support\Seo). The same tags are rendered
// server-side by resources/views/partials/seo.blade.php; head-keys must match its `inertia` attributes.
defineProps({
    seo: { type: Object, required: true },
});
</script>

<template>
    <Head>
        <title>{{ seo.title }}</title>
        <meta head-key="description" name="description" :content="seo.description" />
        <meta v-if="seo.keywords" head-key="keywords" name="keywords" :content="seo.keywords" />
        <meta head-key="robots" name="robots" :content="seo.robots" />
        <link head-key="canonical" rel="canonical" :href="seo.canonical" />
        <meta head-key="og:type" property="og:type" :content="seo.og.type" />
        <meta head-key="og:site_name" property="og:site_name" :content="seo.og.site_name" />
        <meta head-key="og:locale" property="og:locale" :content="seo.og.locale" />
        <meta head-key="og:title" property="og:title" :content="seo.og.title" />
        <meta head-key="og:description" property="og:description" :content="seo.og.description" />
        <meta head-key="og:url" property="og:url" :content="seo.og.url" />
        <meta v-if="seo.og.image" head-key="og:image" property="og:image" :content="seo.og.image" />
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
    </Head>
</template>
