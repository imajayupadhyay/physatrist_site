<script setup>
import Icon from '@/Components/Global/Icon.vue';
import { whatsappUrl } from '@/Config/site';
import { useAppointment } from '@/Composables/useAppointment';

defineProps({
    content: { type: Object, required: true },
});

const { selectConcern } = useAppointment();
</script>

<template>
    <section id="assessment" class="assess">
        <div class="wrap">
            <div v-reveal class="head center">
                <span v-if="content.eyebrow" class="eyebrow">{{ content.eyebrow }}</span>
                <h2>{{ content.heading }}</h2>
                <p v-if="content.lead" class="lead">{{ content.lead }}</p>
            </div>

            <div v-if="content.types.length" class="as-types">
                <article v-for="(type, i) in content.types" :key="i" v-reveal class="as-type">
                    <span class="num">{{ String(i + 1).padStart(2, '0') }}</span>
                    <h3>{{ type.title }}</h3>
                    <p>{{ type.text }}</p>
                    <span v-if="type.for" class="for">{{ type.for }}</span>
                </article>
            </div>

            <div class="as-lower">
                <div v-if="content.process.length" v-reveal class="as-process">
                    <h3>{{ content.process_heading }}</h3>
                    <ol>
                        <li v-for="(step, i) in content.process" :key="i"><b>{{ step.title }}</b><span>{{ step.text }}</span></li>
                    </ol>
                </div>
                <div v-reveal class="as-book">
                    <h3>{{ content.who_heading }}</h3>
                    <ul v-if="content.who_items.length">
                        <li v-for="item in content.who_items" :key="item"><Icon name="i-check" :size="16" />{{ item }}</li>
                    </ul>
                    <p v-if="content.note" class="note">{{ content.note }}</p>
                    <div class="actions">
                        <a href="#appointment" class="btn btn-gold" @click="selectConcern('Psychological Assessment')">{{ content.primary_label }}</a>
                        <a v-if="content.whatsapp_label" :href="whatsappUrl(content.whatsapp_message)" target="_blank" rel="noopener" class="btn btn-line-light">{{ content.whatsapp_label }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.assess { background: var(--cream); }
.as-types { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
.as-type { background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px 28px; display: flex; flex-direction: column; gap: 10px; }
.as-type .num { font-family: var(--serif); font-size: 1rem; color: var(--gold); letter-spacing: 0.1em; }
.as-type h3 { font-size: 1.38rem; }
.as-type p { font-size: 0.93rem; color: var(--muted); line-height: 1.6; }
.as-type .for { margin-top: auto; padding-top: 10px; font-size: 0.74rem; letter-spacing: 0.14em; text-transform: uppercase; color: var(--forest); font-weight: 500; }
.as-lower { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 28px; margin-top: 28px; align-items: stretch; }
.as-process { background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius); padding: 44px 44px 40px; }
.as-process h3 { font-size: 1.75rem; margin-bottom: 28px; }
.as-process ol { list-style: none; display: flex; flex-direction: column; counter-reset: a; }
.as-process li { position: relative; padding: 0 0 26px 62px; counter-increment: a; }
.as-process li:last-child { padding-bottom: 0; }
.as-process li::before { content: '0' counter(a); position: absolute; left: 0; top: -2px; width: 42px; height: 42px; border-radius: 50%; border: 1px solid var(--gold); color: var(--gold); font-family: var(--serif); font-size: 1.1rem; display: grid; place-items: center; background: var(--paper); z-index: 1; }
.as-process li:not(:last-child)::after { content: ''; position: absolute; left: 21px; top: 40px; bottom: 0; width: 1px; background: var(--line); }
.as-process li b { display: block; font-weight: 500; color: var(--forest-deep); font-size: 1.02rem; margin-bottom: 2px; }
.as-process li span { font-size: 0.93rem; color: var(--muted); line-height: 1.6; display: block; }
.as-book { background: var(--forest-deep); color: #c8d2c6; border-radius: var(--radius); padding: 44px 40px 40px; display: flex; flex-direction: column; gap: 18px; position: relative; overflow: hidden; }
.as-book h3 { color: #fff; font-size: 1.75rem; }
.as-book ul { list-style: none; display: flex; flex-direction: column; gap: 12px; }
.as-book li { display: flex; gap: 12px; font-size: 0.95rem; line-height: 1.55; }
.as-book li svg { flex: none; color: var(--gold-soft); margin-top: 4px; }
.as-book .note { font-size: 0.86rem; color: #9fb09c; border-top: 1px solid rgba(255, 255, 255, 0.12); padding-top: 16px; }
.as-book .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: auto; }

@media (max-width: 1100px) {
    .as-types { grid-template-columns: repeat(2, 1fr); }
    .as-lower { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .as-types { grid-template-columns: 1fr; gap: 16px; }
    .as-process { padding: 32px 24px; }
    .as-book { padding: 32px 24px; }
    .as-book .actions .btn { flex: 1; }
}
</style>
