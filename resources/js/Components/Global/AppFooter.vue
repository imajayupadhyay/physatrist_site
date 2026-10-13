<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import BrandLogo from './BrandLogo.vue';
import Icon from './Icon.vue';
import { site, whatsappUrl } from '@/Config/site';

// Content comes from the shared `footer` prop (Admin → Footer); contact details from Config/site.js (synced).
const page = usePage();
const footer = computed(() => page.props.footer);

const socials = computed(() =>
    [
        { key: 'instagram', label: 'Instagram', icon: 'i-ig', href: site.instagram },
        { key: 'youtube', label: 'YouTube', icon: 'i-yt', href: site.youtube },
        { key: 'facebook', label: 'Facebook', icon: 'i-fb', href: site.facebook },
        { key: 'linkedin', label: 'LinkedIn', icon: 'i-in', href: site.linkedin },
        { key: 'whatsapp', label: 'WhatsApp', icon: 'i-wa', href: site.whatsappNumber ? whatsappUrl() : null },
    ].filter((social) => social.href),
);

// "Call **Tele-MANAS 14416** now" → [{ text, bold }] so **…** renders bold without v-html.
const helplineParts = computed(() =>
    (footer.value?.helpline ?? '').split('**').map((text, i) => ({ text, bold: i % 2 === 1 })),
);

const linkAttrs = (link) => (link.newTab ? { target: '_blank', rel: 'noopener' } : {});
const host = (url) => (url ?? '').replace(/^https?:\/\//, '').replace(/\/$/, '');
</script>

<template>
    <footer v-if="footer">
        <div class="wrap">
            <div class="f-grid" :style="{ '--cols': footer.columns.length }">
                <div>
                    <BrandLogo on-dark :logo="footer.logo" />
                    <p v-if="footer.aboutText" class="f-tag">{{ footer.aboutText }}</p>
                    <p v-if="footer.credentials" class="f-psych">
                        <b v-if="footer.credentials.name">{{ footer.credentials.name }}</b>
                        <template v-for="line in footer.credentials.lines" :key="line"><br />{{ line }}</template>
                    </p>
                    <div v-if="footer.showSocials && socials.length" class="socials">
                        <a v-for="social in socials" :key="social.key" :href="social.href" :aria-label="social.label" target="_blank" rel="noopener"><Icon :name="social.icon" :size="18" /></a>
                    </div>
                </div>

                <div v-for="column in footer.columns" :key="column.heading">
                    <h4>{{ column.heading }}</h4>
                    <ul>
                        <li v-for="link in column.links" :key="link.href + link.label"><a :href="link.href" v-bind="linkAttrs(link)">{{ link.label }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4>{{ footer.contactHeading }}</h4>
                    <ul class="f-contact">
                        <li><Icon name="i-pin" :size="16" /><a v-if="site.mapsUrl" :href="site.mapsUrl" target="_blank" rel="noopener">{{ site.address }}</a><span v-else>{{ site.address }}</span></li>
                        <li>
                            <Icon name="i-phone" :size="16" />
                            <span><template v-for="(phone, i) in site.phones" :key="phone.tel"><br v-if="i" /><a :href="`tel:${phone.tel}`">{{ phone.display }}</a></template></span>
                        </li>
                        <li><Icon name="i-mail" :size="16" /><a :href="`mailto:${site.email}`">{{ site.email }}</a></li>
                        <li v-if="footer.showWebsite && site.url"><Icon name="i-globe" :size="16" /><a :href="site.url">{{ host(site.url) }}</a></li>
                        <li v-if="footer.showHours && site.hoursShort"><Icon name="i-clock" :size="16" /><span>{{ site.hoursShort }}</span></li>
                    </ul>
                    <div v-if="footer.helpline" class="helpline">
                        <template v-for="(part, i) in helplineParts" :key="i"><b v-if="part.bold">{{ part.text }}</b><template v-else>{{ part.text }}</template></template>
                    </div>
                </div>
            </div>
            <div v-if="footer.copyright || footer.bottomText" class="f-bottom">
                <span>{{ footer.copyright }}</span>
                <span>{{ footer.bottomText }}</span>
            </div>
        </div>
    </footer>
</template>

<style scoped>
footer { background: var(--forest-deep); color: #b6c2b3; padding: 80px 0 30px; font-size: 0.93rem; }
/* brand · N link columns · contact */
.f-grid { display: grid; grid-template-columns: 1.4fr repeat(var(--cols), minmax(0, 1fr)) 1.3fr; gap: 48px; padding-bottom: 50px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
footer h4 { font-family: var(--serif); color: #fff; font-weight: 500; font-size: 1.3rem; margin-bottom: 18px; }
footer ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
footer a:hover { color: var(--gold-soft); }
.f-tag { margin: 20px 0; max-width: 34ch; }
.f-psych { font-size: 0.88rem; line-height: 1.6; margin-bottom: 20px; color: #9fb09c; }
.f-psych b { color: #e6dcc9; font-weight: 400; }
.socials { display: flex; gap: 10px; flex-wrap: wrap; }
.socials a { width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, 0.2); display: grid; place-items: center; transition: all 0.3s; }
.socials a:hover { background: var(--gold); border-color: var(--gold); color: #fff; }
.f-contact li { display: flex; gap: 10px; align-items: flex-start; line-height: 1.5; }
.f-contact svg { flex: none; margin-top: 3px; color: var(--gold-soft); }
.f-bottom { display: flex; justify-content: space-between; padding-top: 26px; font-size: 0.82rem; flex-wrap: wrap; gap: 12px; }
.helpline { background: rgba(184, 146, 90, 0.12); border: 1px solid rgba(184, 146, 90, 0.35); border-radius: 12px; padding: 16px 18px; font-size: 0.88rem; color: #e6dcc9; margin-top: 22px; }

@media (max-width: 1100px) {
    .f-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
    .f-grid { grid-template-columns: 1fr; gap: 36px; }
    .f-bottom { flex-direction: column; }
}
</style>
