<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import BrandLogo from './BrandLogo.vue';
import Icon from './Icon.vue';
import MegaMenu from './MegaMenu.vue';
import { site } from '@/Config/site';
import { iconMarkup } from '@/Shared/serviceIcons';

// Logo, menu and CTA come from the shared `header` prop (Admin → Header).
const page = usePage();
const header = computed(() => page.props.header ?? { menu: [], cta: null, logo: null, megaPromo: null, topbar: null });

// Top bar: what to show comes from `header.topbar`, the values from the contact details (Config/site.js).
const topbar = computed(() => header.value.topbar);
const topbarLeft = computed(() =>
    [topbar.value?.showHours && site.hours, topbar.value?.text].filter(Boolean).join(' \u00a0·\u00a0 '),
);
const topbarPhone = computed(() => (topbar.value?.showPhone ? (site.phones[0] ?? null) : null));
const topbarEmail = computed(() => (topbar.value?.showEmail ? site.email || null : null));

const scrolled = ref(false);
const mobileOpen = ref(false);

// Desktop dropdowns: open on hover (with a small close delay) or on click/keyboard.
const openId = ref(null);
const mobileExpanded = ref(null);
let closeTimer;
const openDropdown = (id) => {
    clearTimeout(closeTimer);
    openId.value = id;
};
const closeDropdownSoon = () => {
    clearTimeout(closeTimer);
    closeTimer = setTimeout(() => (openId.value = null), 160);
};
const toggleDropdown = (id) => (openId.value = openId.value === id ? null : id);
const closeAll = () => {
    openId.value = null;
    mobileOpen.value = false;
    mobileExpanded.value = null;
};

const navEl = ref(null);
const onScroll = () => (scrolled.value = window.scrollY > 10);
const onKey = (e) => e.key === 'Escape' && (openId.value = null);
const onClickOutside = (e) => navEl.value && !navEl.value.contains(e.target) && (openId.value = null);

onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('keydown', onKey);
    document.addEventListener('click', onClickOutside);
});
onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('keydown', onKey);
    document.removeEventListener('click', onClickOutside);
    clearTimeout(closeTimer);
});

const linkAttrs = (item) => (item.newTab ? { target: '_blank', rel: 'noopener' } : {});
</script>

<!-- Two root nodes on purpose: the nav must be a sibling of the page content for position: sticky to work. -->
<template>
    <div v-if="topbar" class="topbar">
        <div class="wrap" :class="{ 'has-right': topbarPhone || topbarEmail }">
            <span v-if="topbarLeft" class="tb-left">
                <Icon v-if="topbar.showHours && site.hours" name="i-clock" :size="14" />
                {{ topbarLeft }}
            </span>
            <div v-if="topbarPhone || topbarEmail" class="tb-right">
                <a v-if="topbarPhone" :href="`tel:${topbarPhone.tel}`"><span><Icon name="i-phone" :size="14" />{{ topbarPhone.display }}</span></a>
                <a v-if="topbarEmail" :href="`mailto:${topbarEmail}`"><span><Icon name="i-mail" :size="14" />{{ topbarEmail }}</span></a>
            </div>
        </div>
    </div>

    <nav ref="navEl" class="main" :class="{ scrolled: scrolled || openId, open: mobileOpen }" aria-label="Main">
        <div class="wrap">
            <BrandLogo :logo="header.logo" />

            <ul id="navLinks" class="nav-links">
                <li
                    v-for="item in header.menu"
                    :key="item.id"
                    :class="{ 'has-children': item.children.length, 'is-open': openId === item.id, mega: item.style === 'mega' }"
                    @mouseenter="item.children.length && openDropdown(item.id)"
                    @mouseleave="item.children.length && closeDropdownSoon()"
                >
                    <div class="item-row">
                        <a v-if="item.href" :href="item.href" class="top-link" v-bind="linkAttrs(item)" @click="closeAll">{{ item.label }}</a>
                        <button v-else type="button" class="top-link" @click="toggleDropdown(item.id)">{{ item.label }}</button>

                        <!-- Desktop: chevron toggles the dropdown (keyboard / touch). Mobile: expands the sub-list. -->
                        <button
                            v-if="item.children.length"
                            type="button"
                            class="chev"
                            :aria-expanded="openId === item.id || mobileExpanded === item.id"
                            :aria-label="`${item.label} submenu`"
                            @click.stop="mobileOpen ? (mobileExpanded = mobileExpanded === item.id ? null : item.id) : toggleDropdown(item.id)"
                        >
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6" /></svg>
                        </button>
                    </div>

                    <!-- Desktop dropdowns -->
                    <Transition name="drop">
                        <MegaMenu v-if="item.style === 'mega' && openId === item.id && !mobileOpen" :item="item" :promo="header.megaPromo" @navigate="closeAll" />
                    </Transition>
                    <Transition name="drop">
                        <ul v-if="item.style === 'simple' && openId === item.id && !mobileOpen" class="dropdown">
                            <li v-for="child in item.children" :key="child.id">
                                <a :href="child.href" v-bind="linkAttrs(child)" @click="closeAll">
                                    <b>{{ child.label }}</b>
                                    <span v-if="child.description">{{ child.description }}</span>
                                </a>
                            </li>
                        </ul>
                    </Transition>

                    <!-- Mobile sub-list -->
                    <ul v-if="item.children.length && mobileOpen && mobileExpanded === item.id" class="m-sub">
                        <li v-for="child in item.children" :key="child.id">
                            <a :href="child.href" v-bind="linkAttrs(child)" @click="closeAll">
                                <span v-if="child.icon" class="m-ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" v-html="iconMarkup(child.icon)" /></span>
                                {{ child.label }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <a v-if="header.cta" :href="header.cta.href" class="btn btn-primary nav-cta" v-bind="linkAttrs(header.cta)">{{ header.cta.label }}</a>
            <button class="burger" :aria-label="mobileOpen ? 'Close menu' : 'Open menu'" :aria-expanded="mobileOpen" aria-controls="navLinks" @click="mobileOpen = !mobileOpen">
                <svg v-if="!mobileOpen" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 7h16M4 12h16M4 17h10" /></svg>
                <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        </div>
    </nav>
</template>

<style scoped>
.topbar { background: var(--forest-deep); color: #d9e0d6; font-size: 0.82rem; letter-spacing: 0.04em; }
.topbar .wrap { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding: 9px 0; flex-wrap: wrap; }
.topbar span { display: inline-flex; gap: 8px; align-items: center; }
.topbar .tb-right { display: flex; gap: 24px; margin-left: auto; }
.topbar a:hover { color: var(--gold-soft); }

nav.main { position: sticky; top: 0; z-index: 50; background: rgba(248, 244, 236, 0.92); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-bottom: 1px solid transparent; transition: border-color 0.3s, box-shadow 0.3s; }
nav.main.scrolled { border-color: var(--line); box-shadow: 0 8px 30px -20px rgba(0, 0, 0, 0.25); }
nav.main > .wrap { display: flex; align-items: center; justify-content: space-between; height: var(--nav-h); gap: 24px; }
.nav-links { display: flex; gap: 32px; list-style: none; font-size: 0.93rem; font-weight: 400; height: 100%; }
.nav-links > li { position: relative; display: flex; align-items: center; }
.nav-links > li.mega { position: static; }
.item-row { display: flex; align-items: center; gap: 4px; }
.top-link { position: relative; padding: 6px 0; color: var(--ink); background: none; border: 0; cursor: pointer; font: inherit; }
.top-link::after { content: ''; position: absolute; left: 0; bottom: 0; width: 0; height: 1px; background: var(--gold); transition: width 0.3s; }
.top-link:hover::after, li.is-open .top-link::after { width: 100%; }
.chev { display: grid; place-items: center; width: 20px; height: 20px; border: 0; background: none; color: var(--muted); cursor: pointer; border-radius: 50%; transition: transform 0.25s, color 0.25s; }
li.is-open .chev { transform: rotate(180deg); color: var(--gold); }
.nav-cta { padding: 12px 24px; }
.burger { display: none; background: none; border: 0; width: 44px; height: 44px; cursor: pointer; color: var(--forest-deep); }

/* Simple dropdown */
.dropdown { position: absolute; top: calc(100% - 14px); left: -18px; min-width: 250px; list-style: none; background: var(--paper); border: 1px solid var(--line); border-radius: 16px; padding: 8px; box-shadow: 0 24px 50px -24px rgba(31, 49, 39, 0.4); }
.dropdown a { display: flex; flex-direction: column; gap: 2px; padding: 10px 14px; border-radius: 10px; transition: background 0.2s; }
.dropdown a:hover { background: var(--cream); }
.dropdown b { font-weight: 500; color: var(--forest-deep); }
.dropdown span { font-size: 0.8rem; color: var(--muted); line-height: 1.4; }

.drop-enter-active, .drop-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.drop-enter-from, .drop-leave-to { opacity: 0; transform: translateY(-6px); }

/* Mobile sub-list */
.m-sub { list-style: none; padding: 4px 0 10px 12px; }
.m-sub a { display: flex; align-items: center; gap: 12px; padding: 10px 0; font-size: 0.95rem; color: var(--forest-deep); }
.m-ico { width: 30px; height: 30px; border-radius: 50%; background: var(--sage-soft); color: var(--forest); display: grid; place-items: center; flex: none; }

@media (max-width: 1100px) {
    .nav-links { gap: 24px; }
}
@media (max-width: 960px) {
    .nav-links, .nav-cta { display: none; }
    .burger { display: grid; place-items: center; }
    nav.main.open .nav-links { display: flex; flex-direction: column; height: auto; position: absolute; top: var(--nav-h); left: 0; right: 0; max-height: calc(100vh - var(--nav-h) - 80px); overflow-y: auto; background: var(--cream); padding: 12px 20px 24px; gap: 0; border-bottom: 1px solid var(--line); box-shadow: 0 20px 30px -20px rgba(0, 0, 0, 0.25); }
    nav.main.open .nav-links > li { display: block; border-bottom: 1px solid var(--line); }
    nav.main.open .nav-links > li:last-child { border-bottom: 0; }
    nav.main.open .item-row { justify-content: space-between; }
    nav.main.open .top-link { display: block; flex: 1; text-align: left; padding: 14px 0; font-size: 1rem; }
    nav.main.open .top-link::after { display: none; }
    nav.main.open .chev { width: 40px; height: 40px; }
    nav.main.open .chev[aria-expanded='true'] { transform: rotate(180deg); color: var(--gold); }
}
@media (max-width: 900px) {
    /* Phone/email win on small screens; the left text only stays if it's all there is. */
    .topbar .wrap.has-right .tb-left { display: none; }
    .topbar .wrap { justify-content: center; }
    .topbar .tb-right { margin-left: 0; }
}
@media (max-width: 600px) {
    .topbar .tb-right { gap: 18px; font-size: 0.78rem; }
}
</style>
