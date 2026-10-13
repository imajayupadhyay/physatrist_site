<script setup>
// Renders a symbol from SvgSprite. `name` is the symbol id, e.g. "i-phone" or "logo".
// Omit `size` when CSS sizes the icon.
defineProps({
    name: { type: String, required: true },
    size: { type: [Number, String], default: undefined },
});
</script>

<template>
    <svg :width="size" :height="size"><use :href="`#${name}`" /></svg>
</template>
