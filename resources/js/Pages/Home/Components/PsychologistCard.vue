<script setup>
import { computed } from 'vue';
import Icon from '@/Components/Global/Icon.vue';

const props = defineProps({
    card: { type: Object, required: true },
});

const details = computed(() => (props.card.details || '').split('\n').map((l) => l.trim()).filter(Boolean));
</script>

<template>
    <aside class="psych-card" aria-label="Your psychologist">
        <div class="pc-inner">
            <div class="pc-photo"><img :src="card.photo" :alt="card.name" /></div>
            <div class="pc-info">
                <small v-if="card.label">{{ card.label }}</small>
                <b>{{ card.name }}</b>
                <span v-if="details.length"><template v-for="(line, i) in details" :key="i"><br v-if="i" />{{ line }}</template></span>
                <span v-if="card.badge" class="pc-reg"><Icon name="i-shield" :size="14" />{{ card.badge }}</span>
            </div>
            <a v-if="card.link_label" :href="card.link_url" class="pc-link">{{ card.link_label }} <Icon name="i-arrow" :size="15" /></a>
        </div>
    </aside>
</template>

<style scoped>
.psych-card { position: absolute; z-index: 6; top: 50%; right: max(20px, calc((100% - 1180px) / 2)); transform: translateY(-50%); width: 290px; }
.pc-inner { background: var(--paper); border-radius: 22px; padding: 12px; box-shadow: 0 30px 70px -30px rgba(0, 0, 0, 0.65); }
.pc-photo { border-radius: 14px; overflow: hidden; aspect-ratio: 1/1; background: #e8e6e2; }
.pc-photo img { width: 100%; height: 100%; object-fit: cover; object-position: center 8%; }
.pc-info { padding: 18px 10px 6px; display: flex; flex-direction: column; gap: 4px; }
.pc-info small { font-size: 0.68rem; letter-spacing: 0.24em; text-transform: uppercase; color: var(--gold); font-weight: 500; }
.pc-info b { font-family: var(--serif); font-weight: 600; font-size: 1.55rem; line-height: 1.15; color: var(--forest-deep); }
.pc-info span { font-size: 0.88rem; color: var(--muted); line-height: 1.45; }
.pc-reg { display: inline-flex; align-items: center; gap: 7px; margin-top: 10px; align-self: flex-start; font-size: 0.76rem; font-weight: 400; letter-spacing: 0.04em; color: var(--forest); background: var(--sage-soft); border-radius: 999px; padding: 6px 12px; }
.pc-link { display: flex; justify-content: space-between; align-items: center; margin: 12px 10px 4px; padding-top: 12px; border-top: 1px solid var(--line); font-size: 0.85rem; font-weight: 500; letter-spacing: 0.04em; color: var(--forest); }
.pc-link:hover { color: var(--gold); }

@media (max-width: 1100px) {
    .psych-card { position: static; transform: none; width: auto; background: var(--paper); border-bottom: 1px solid var(--line); }
    .pc-inner { width: min(1180px, 100% - 40px); margin-inline: auto; background: none; box-shadow: none; border-radius: 0; padding: 20px 0; display: flex; align-items: center; gap: 18px; }
    .pc-photo { width: 76px; height: 76px; flex: none; border-radius: 50%; aspect-ratio: auto; }
    .pc-info { padding: 0; flex: 1; gap: 2px; }
    .pc-info small { display: none; }
    .pc-info b { font-size: 1.4rem; }
    .pc-reg { margin-top: 6px; }
    .pc-link { margin: 0; padding: 0; border: 0; flex: none; gap: 8px; }
}
@media (max-width: 600px) {
    .pc-inner { width: calc(100% - 32px); gap: 14px; padding: 16px 0; }
    .pc-photo { width: 64px; height: 64px; }
    .pc-info b { font-size: 1.25rem; }
    .pc-info span { font-size: 0.82rem; }
    .pc-link { display: none; }
}
</style>
