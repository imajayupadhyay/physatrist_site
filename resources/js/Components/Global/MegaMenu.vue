<script setup>
import Icon from './Icon.vue';
import { site } from '@/Config/site';
import { iconMarkup } from '@/Shared/serviceIcons';

// Full-width dropdown panel for a menu item with dropdown_style "mega".
defineProps({
    item: { type: Object, required: true },
    promo: { type: Object, default: null },
});
defineEmits(['navigate']);
</script>

<template>
    <div class="mega" role="region" :aria-label="`${item.label} menu`">
        <div class="wrap mega-inner" :class="{ 'no-promo': !promo }">
            <div>
                <p class="mega-title">{{ item.label }}</p>
                <ul class="mega-grid">
                    <li v-for="child in item.children" :key="child.id">
                        <a :href="child.href" :target="child.newTab ? '_blank' : null" :rel="child.newTab ? 'noopener' : null" class="mega-link" @click="$emit('navigate')">
                            <span class="mega-ico">
                                <svg v-if="child.icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" v-html="iconMarkup(child.icon)" />
                                <Icon v-else name="i-arrow" :size="16" />
                            </span>
                            <span class="mega-text">
                                <b>{{ child.label }}</b>
                                <small v-if="child.audience">{{ child.audience }}</small>
                                <span v-if="child.description" class="desc">{{ child.description }}</span>
                            </span>
                        </a>
                    </li>
                </ul>
                <a v-if="item.href" :href="item.href" class="mega-all" @click="$emit('navigate')">View all {{ item.label.toLowerCase() }} <Icon name="i-arrow" :size="14" /></a>
            </div>

            <aside v-if="promo" class="promo">
                <Icon name="logo" class="promo-leaf" viewBox="0 0 64 64" aria-hidden="true" />
                <p class="promo-title">{{ promo.title }}</p>
                <p v-if="promo.text" class="promo-text">{{ promo.text }}</p>
                <a :href="promo.ctaHref" class="btn btn-gold promo-btn" @click="$emit('navigate')">{{ promo.ctaLabel }}</a>
                <a :href="`tel:${site.phones[0].tel}`" class="promo-call"><Icon name="i-phone" :size="14" />{{ site.phones[0].display }}</a>
            </aside>
        </div>
    </div>
</template>

<style scoped>
.mega { position: absolute; left: 0; right: 0; top: 100%; background: var(--paper); border-top: 1px solid var(--line); box-shadow: 0 30px 50px -30px rgba(31, 49, 39, 0.35); }
.mega-inner { display: grid; grid-template-columns: 1fr 300px; gap: 36px; padding: 30px 0 34px; }
.mega-inner.no-promo { grid-template-columns: 1fr; }
.mega-title { font-size: 0.72rem; letter-spacing: 0.26em; text-transform: uppercase; color: var(--gold); font-weight: 500; margin-bottom: 14px; }
.mega-grid { list-style: none; display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px 18px; }
.mega-link { display: flex; gap: 14px; align-items: flex-start; padding: 12px 14px; border-radius: 14px; transition: background 0.25s ease; }
.mega-link:hover, .mega-link:focus-visible { background: var(--cream); }
.mega-ico { width: 40px; height: 40px; flex: none; border-radius: 50%; background: var(--sage-soft); color: var(--forest); display: grid; place-items: center; transition: background 0.25s, color 0.25s; }
.mega-link:hover .mega-ico { background: var(--forest); color: var(--gold-soft); }
.mega-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.mega-text b { font-family: var(--serif); font-weight: 600; font-size: 1.18rem; line-height: 1.2; color: var(--forest-deep); }
.mega-text small { font-size: 0.66rem; letter-spacing: 0.12em; text-transform: uppercase; color: var(--gold); font-weight: 500; }
.mega-text .desc { font-size: 0.84rem; line-height: 1.5; color: var(--muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.mega-all { display: inline-flex; align-items: center; gap: 8px; margin: 14px 14px 0; font-size: 0.85rem; font-weight: 500; letter-spacing: 0.05em; color: var(--forest); }
.mega-all:hover { color: var(--gold); }

.promo { position: relative; overflow: hidden; background: var(--forest-deep); border-radius: var(--radius); padding: 28px 26px; color: #c8d2c6; display: flex; flex-direction: column; gap: 10px; align-self: start; }
.promo-leaf { position: absolute; right: -40px; top: -40px; width: 170px; opacity: 0.07; color: #fff; pointer-events: none; }
.promo-title { position: relative; font-family: var(--serif); font-size: 1.55rem; line-height: 1.15; color: #fff; }
.promo-text { position: relative; font-size: 0.9rem; line-height: 1.55; }
.promo-btn { position: relative; margin-top: 8px; padding: 12px 20px; }
.promo-call { position: relative; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.86rem; color: var(--gold-soft); padding-top: 4px; }
.promo-call:hover { color: #fff; }

@media (max-width: 1100px) {
    .mega-inner { grid-template-columns: 1fr 260px; gap: 24px; }
}
</style>
