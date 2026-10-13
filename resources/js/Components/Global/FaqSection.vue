<script setup>
import Icon from '@/Components/Global/Icon.vue';
import { site, whatsappUrl } from '@/Config/site';
import { textLinks } from '@/Shared/textLinks';

// FAQ accordion with a "Still have a question?" box.
// Each item: { question, answer }. Answers may contain links written as [link text](/#section or https://…).
defineProps({
    faqs: { type: Array, required: true },
    id: { type: String, default: 'faq' },
    eyebrow: { type: String, default: 'FAQ' },
    heading: { type: String, default: 'Questions you may have' },
    lead: { type: String, default: 'Starting therapy can bring up a lot of questions. Here are answers to the ones we hear most often.' },
});
</script>

<template>
    <section :id="id" class="faq">
        <div class="wrap faq-grid">
            <div v-reveal class="faq-side">
                <div class="head">
                    <span class="eyebrow">{{ eyebrow }}</span>
                    <h2>{{ heading }}</h2>
                    <p v-if="lead" class="lead">{{ lead }}</p>
                </div>
                <div class="faq-help">
                    <b>Still have a question?</b>
                    <p>Call or message us and we'll be happy to help, in confidence.</p>
                    <div class="actions">
                        <a :href="`tel:${site.phones[0].tel}`" class="btn btn-primary"><Icon name="i-phone" :size="16" />Call</a>
                        <a :href="whatsappUrl()" target="_blank" rel="noopener" class="btn btn-outline"><Icon name="i-wa" :size="16" />WhatsApp</a>
                    </div>
                </div>
            </div>

            <div v-reveal class="faq-list">
                <details v-for="(faq, i) in faqs" :key="faq.question" :open="i === 0">
                    <summary>{{ faq.question }}<span class="pm"><Icon name="i-plus" :size="16" /></span></summary>
                    <div class="ans">
                        <p><template v-for="(part, j) in textLinks(faq.answer)" :key="j"><a v-if="part.href" :href="part.href" class="inline-link">{{ part.text }}</a><template v-else>{{ part.text }}</template></template></p>
                    </div>
                </details>
            </div>
        </div>
    </section>
</template>

<style scoped>
.faq { background: var(--cream); }
.faq-grid { display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 72px; align-items: start; }
.faq-side { position: sticky; top: calc(var(--nav-h) + 32px); display: flex; flex-direction: column; }
.faq-side .head { margin-bottom: 32px; }
.faq-help { background: var(--paper); border: 1px solid var(--line); border-radius: var(--radius); padding: 26px 26px 24px; display: flex; flex-direction: column; gap: 14px; }
.faq-help b { font-family: var(--serif); font-weight: 600; font-size: 1.3rem; color: var(--forest-deep); line-height: 1.2; }
.faq-help p { font-size: 0.93rem; color: var(--muted); }
.faq-help .actions { display: flex; gap: 10px; flex-wrap: wrap; }
.faq-help .btn { padding: 12px 22px; }
.faq-list { border-top: 1px solid var(--line); }
.faq-list details { border-bottom: 1px solid var(--line); }
.faq-list summary { list-style: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 24px; padding: 24px 0; font-family: var(--serif); font-size: 1.38rem; font-weight: 500; line-height: 1.3; color: var(--forest-deep); }
.faq-list summary::-webkit-details-marker { display: none; }
.faq-list summary .pm { flex: none; width: 34px; height: 34px; border-radius: 50%; border: 1px solid var(--line); display: grid; place-items: center; color: var(--forest); transition: all 0.3s; }
.faq-list summary .pm svg { transition: transform 0.3s; }
.faq-list details[open] summary .pm { background: var(--forest); border-color: var(--forest); color: #fff; }
.faq-list details[open] summary .pm svg { transform: rotate(45deg); }
.faq-list summary:hover { color: var(--forest); }
.faq-list .ans { padding: 0 58px 26px 0; color: var(--muted); font-size: 0.98rem; }
.faq-list .ans p + p { margin-top: 10px; }
.inline-link { color: var(--forest); font-weight: 500; text-decoration: underline; }

@media (max-width: 900px) {
    .faq-grid { grid-template-columns: 1fr; gap: 40px; }
    .faq-side { position: static; }
}
@media (max-width: 600px) {
    .faq-list summary { font-size: 1.2rem; padding: 20px 0; }
    .faq-list .ans { padding-right: 0; }
}
</style>
