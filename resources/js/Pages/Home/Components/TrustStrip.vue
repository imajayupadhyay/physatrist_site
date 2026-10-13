<script setup>
defineProps({
    content: { type: Object, required: true },
});
</script>

<template>
    <div class="trust">
        <div class="wrap">
            <div v-for="(item, i) in content.items" :key="i" class="item"><b>{{ item.value }}</b><span>{{ item.label }}</span></div>
        </div>
    </div>
</template>

<style scoped>
.trust { padding: 0; position: relative; z-index: 6; background: var(--paper); border-bottom: 1px solid var(--line); }
.trust .wrap { display: grid; grid-template-columns: repeat(4, 1fr); }
.trust .item { padding: 32px 26px; border-right: 1px solid var(--line); display: flex; flex-direction: column; justify-content: center; gap: 6px; }
.trust .item:first-child { padding-left: 0; }
.trust .item:last-child { border-right: 0; }
.trust b { font-family: var(--serif); font-size: 2.3rem; font-weight: 500; color: var(--forest); line-height: 1; }
.trust span { font-size: 0.9rem; color: var(--muted); line-height: 1.4; }

@media (max-width: 900px) {
    .trust .wrap { grid-template-columns: repeat(2, 1fr); }
    .trust .item { padding: 26px 22px; }
    .trust .item:nth-child(odd) { padding-left: 0; }
    .trust .item:nth-child(2) { border-right: 0; }
    .trust .item:nth-child(-n + 2) { border-bottom: 1px solid var(--line); }
}
@media (max-width: 600px) {
    .trust .item { padding: 22px 16px; }
    .trust b { font-size: 1.9rem; }
    .trust span { font-size: 0.84rem; }
}
</style>
