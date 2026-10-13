<script setup>
import { onMounted, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Icon from '@/Components/Global/Icon.vue';
import { site, whatsappUrl } from '@/Config/site';
import { useAppointment } from '@/Composables/useAppointment';

// Reusable on any page whose controller passes the `appointment` prop (Lead::formOptions()).
const props = defineProps({
    // Pre-select an "Area of concern" (e.g. on a service page).
    concern: { type: String, default: '' },
});

const { concern, concernOptions, modeOptions, selectConcern } = useAppointment();

const form = useForm({
    name: '',
    phone: '',
    email: '',
    concern: concern.value,
    preferred_date: '',
    mode: 'In-clinic',
    message: '',
    website: '', // honeypot, must stay empty
});

// "Book a session" links elsewhere on the page pre-select the concern.
watch(concern, (value) => (form.concern = value));

// No past dates (local timezone).
const minDate = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().split('T')[0];

const submitted = ref(null); // { name } after a successful request

const submit = () => {
    form.clearErrors();
    form.post('/appointment-requests', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            submitted.value = { name: form.name.trim().split(' ')[0] };
            form.reset();
            concern.value = form.concern;
        },
    });
};

const startOver = () => (submitted.value = null);

onMounted(() => props.concern && selectConcern(props.concern));
</script>

<template>
    <section id="appointment" class="appt">
        <div class="wrap">
            <div v-reveal class="card">
                <div id="contact" class="appt-info">
                    <span class="eyebrow">Get in touch</span>
                    <h2>Take the first step today.</h2>
                    <p>Everything you share stays strictly confidential. We usually respond within a few working hours.</p>
                    <div class="contact-list">
                        <div>
                            <span class="ci"><Icon name="i-pin" :size="18" /></span>
                            <div>
                                <small>Visit us</small><span>C-21 B, Shyam Nagar, Top Floor,<br />Opposite Gurudwara, New Delhi – 110018</span>
                                <a :href="site.mapsUrl" target="_blank" rel="noopener" class="map-link">Get directions →</a>
                            </div>
                        </div>
                        <div>
                            <span class="ci"><Icon name="i-phone" :size="18" /></span>
                            <div>
                                <small>Call / WhatsApp</small>
                                <span><template v-for="(phone, i) in site.phones" :key="phone.tel"><br v-if="i" /><a :href="`tel:${phone.tel}`">{{ phone.display }}</a></template></span>
                            </div>
                        </div>
                        <a :href="`mailto:${site.email}`">
                            <span class="ci"><Icon name="i-mail" :size="18" /></span>
                            <div><small>Email</small><span>{{ site.email }}</span></div>
                        </a>
                        <div>
                            <span class="ci"><Icon name="i-clock" :size="18" /></span>
                            <div><small>Hours</small><span>{{ site.hours }}</span></div>
                        </div>
                    </div>
                    <div class="appt-quick">
                        <a :href="`tel:${site.phones[0].tel}`" class="btn btn-light"><Icon name="i-phone" :size="16" />Call now</a>
                        <a :href="whatsappUrl()" target="_blank" rel="noopener" class="btn btn-line-light"><Icon name="i-wa" :size="16" />WhatsApp</a>
                    </div>
                </div>

                <div v-if="submitted" class="appt-form appt-done" role="status" aria-live="polite">
                    <span class="done-ico"><Icon name="i-check" :size="28" /></span>
                    <h3>Thank you, {{ submitted.name }}.</h3>
                    <p>We've received your appointment request. Our team will contact you shortly to confirm a convenient slot. Everything you've shared stays strictly confidential.</p>
                    <div class="done-actions">
                        <a :href="whatsappUrl(`Hello, I just requested an appointment on your website. My name is ${submitted.name}.`)" target="_blank" rel="noopener" class="btn btn-outline"><Icon name="i-wa" :size="16" />Message us on WhatsApp</a>
                        <button type="button" class="btn btn-primary" @click="startOver">Book another</button>
                    </div>
                </div>

                <form v-else class="appt-form" novalidate @submit.prevent="submit">
                    <h3>Book an appointment</h3>
                    <p class="sub">Fill in your details and we'll confirm a convenient slot on WhatsApp.</p>
                    <div class="field" :class="{ invalid: form.errors.name }">
                        <label for="name">Full name</label>
                        <input id="name" v-model="form.name" name="name" required maxlength="120" autocomplete="name" placeholder="Your name" :aria-invalid="!!form.errors.name" />
                        <span v-if="form.errors.name" class="err">{{ form.errors.name }}</span>
                    </div>
                    <div class="field" :class="{ invalid: form.errors.phone }">
                        <label for="phone">Phone</label>
                        <input id="phone" v-model="form.phone" name="phone" type="tel" required maxlength="30" autocomplete="tel" inputmode="tel" placeholder="+91" :aria-invalid="!!form.errors.phone" />
                        <span v-if="form.errors.phone" class="err">{{ form.errors.phone }}</span>
                    </div>
                    <div class="field" :class="{ invalid: form.errors.email }">
                        <label for="email">Email (optional)</label>
                        <input id="email" v-model="form.email" name="email" type="email" maxlength="255" autocomplete="email" placeholder="you@example.com" :aria-invalid="!!form.errors.email" />
                        <span v-if="form.errors.email" class="err">{{ form.errors.email }}</span>
                    </div>
                    <div class="field" :class="{ invalid: form.errors.concern }">
                        <label for="concern">Area of concern</label>
                        <select id="concern" v-model="concern" name="concern">
                            <option v-for="option in concernOptions" :key="option">{{ option }}</option>
                        </select>
                        <span v-if="form.errors.concern" class="err">{{ form.errors.concern }}</span>
                    </div>
                    <div class="field" :class="{ invalid: form.errors.preferred_date }">
                        <label for="date">Preferred date</label>
                        <input id="date" v-model="form.preferred_date" name="preferred_date" type="date" :min="minDate" :aria-invalid="!!form.errors.preferred_date" />
                        <span v-if="form.errors.preferred_date" class="err">{{ form.errors.preferred_date }}</span>
                    </div>
                    <div class="field">
                        <span id="modeLbl" class="lbl">Session mode</span>
                        <div class="mode" role="radiogroup" aria-labelledby="modeLbl">
                            <label v-for="mode in modeOptions" :key="mode"><input v-model="form.mode" type="radio" name="mode" :value="mode" /> {{ mode }}</label>
                        </div>
                        <span v-if="form.errors.mode" class="err">{{ form.errors.mode }}</span>
                    </div>
                    <div class="field full" :class="{ invalid: form.errors.message }">
                        <label for="msg">Anything you'd like us to know? (optional)</label>
                        <textarea id="msg" v-model="form.message" name="message" maxlength="2000" placeholder="Share briefly, only if you're comfortable" />
                        <span v-if="form.errors.message" class="err">{{ form.errors.message }}</span>
                    </div>

                    <!-- Honeypot: hidden from people, bots fill it in -->
                    <div class="hp" aria-hidden="true">
                        <label for="website">Website</label>
                        <input id="website" v-model="form.website" name="website" type="text" tabindex="-1" autocomplete="off" />
                    </div>

                    <div v-if="form.errors.form" class="form-error" role="alert">{{ form.errors.form }}</div>

                    <div class="form-foot">
                        <small>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                            Your information is private and only shared with our centre.
                        </small>
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Sending…' : 'Request Appointment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>

<style scoped>
.appt { background: var(--paper); }
.appt .card { display: grid; grid-template-columns: 1fr 1.25fr; background: var(--paper); border-radius: 28px; overflow: hidden; box-shadow: var(--shadow); border: 1px solid var(--line); }
.appt-info { background: var(--forest) url('/images/therapy-room-2.jpg') center/cover; position: relative; color: #fff; padding: 56px 48px; display: flex; flex-direction: column; gap: 22px; }
.appt-info::before { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(31, 49, 39, 0.9), rgba(31, 49, 39, 0.96)); }
.appt-info > * { position: relative; }
.appt-info h2 { color: #fff; }
.appt-info .eyebrow { color: var(--gold-soft); }
.appt-info p { color: #c8d2c6; }
.contact-list { display: flex; flex-direction: column; gap: 20px; margin-top: 8px; }
.contact-list > div, .contact-list > a { display: flex; gap: 16px; align-items: flex-start; }
.contact-list a:hover span { color: var(--gold-soft); }
.contact-list .ci { width: 44px; height: 44px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, 0.25); display: grid; place-items: center; flex: none; color: var(--gold-soft); }
.contact-list small { display: block; font-size: 0.75rem; letter-spacing: 0.18em; text-transform: uppercase; color: #9fb09c; line-height: 1.6; }
.contact-list span { font-size: 1rem; line-height: 1.5; transition: color 0.3s; }
.contact-list span a:hover { color: var(--gold-soft); }
.map-link { display: inline-block; margin-top: 6px; font-size: 0.85rem; font-weight: 500; letter-spacing: 0.04em; color: var(--gold-soft); }
.map-link:hover { color: #fff; }
.appt-quick { display: flex; gap: 12px; flex-wrap: wrap; margin-top: auto; padding-top: 8px; }
.appt-quick .btn { padding: 12px 22px; }
form.appt-form { padding: 56px 52px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-content: start; }
.appt-form h3 { grid-column: 1/-1; font-size: 2rem; }
.appt-form .sub { grid-column: 1/-1; color: var(--muted); font-size: 0.95rem; margin-top: -12px; }
.field { display: flex; flex-direction: column; gap: 7px; min-width: 0; }
.field.full { grid-column: 1/-1; }
.field label, .field .lbl { font-size: 0.78rem; letter-spacing: 0.14em; text-transform: uppercase; color: var(--muted); font-weight: 400; }
.field input, .field select, .field textarea { font: inherit; font-weight: 400; font-size: 0.98rem; padding: 14px 16px; border: 1px solid var(--line); border-radius: 12px; background: var(--cream); color: var(--ink); transition: border-color 0.3s, background 0.3s; width: 100%; min-height: 52px; }
.field select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%235E6A61' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; padding-right: 40px; }
.field textarea { resize: vertical; min-height: 110px; }
.field input:focus, .field select:focus, .field textarea:focus { outline: none; border-color: var(--sage); background: #fff; }
.mode { display: flex; gap: 10px; }
.mode label { flex: 1; text-transform: none; letter-spacing: 0; font-size: 0.95rem; color: var(--ink); border: 1px solid var(--line); background: var(--cream); border-radius: 12px; padding: 0 14px; min-height: 52px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: border-color 0.3s, background 0.3s; }
.mode label:has(input:checked) { border-color: var(--sage); background: #fff; }
.mode input { accent-color: var(--forest); width: auto; min-height: 0; }
.form-foot { grid-column: 1/-1; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-top: 6px; }
.form-foot small { color: var(--muted); font-size: 0.82rem; max-width: 36ch; display: flex; gap: 8px; align-items: center; line-height: 1.5; }
.form-foot small svg { flex: none; }
.field .err { font-size: 0.82rem; color: #b42318; line-height: 1.4; }
.field.invalid input, .field.invalid select, .field.invalid textarea { border-color: #e3796d; background: #fff; }
.form-error { grid-column: 1/-1; background: #fdecea; border: 1px solid #f5c2bd; border-radius: 12px; padding: 14px 18px; color: #8a1c12; font-size: 0.92rem; }
.form-foot .btn:disabled { opacity: 0.7; cursor: wait; transform: none; }
.hp { position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden; }

/* Success state (replaces the form) */
.appt-done { padding: 56px 52px; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; gap: 16px; }
.appt-done .done-ico { width: 60px; height: 60px; border-radius: 50%; background: var(--sage-soft); color: var(--forest); display: grid; place-items: center; }
.appt-done p { color: var(--muted); max-width: 46ch; }
.done-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; }

@media (max-width: 900px) {
    .appt .card { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    form.appt-form { grid-template-columns: 1fr; padding: 40px 24px; }
    .appt-done { padding: 40px 24px; }
    .done-actions .btn { flex: 1; }
    .appt-info { padding: 44px 24px; }
    .appt .card { border-radius: 22px; }
    .form-foot .btn { width: 100%; }
}
</style>
