<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ListEditor from '@/Admin/Components/Global/ListEditor.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

defineProps({
    form: { type: Object, required: true },
});
</script>

<template>
    <FormSection id="contact" title="Contact details" description="Used across the whole website, not just the footer.">
        <div class="flex gap-3 rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-800">
            <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10" /><path d="M12 16v-4M12 8h.01" /></svg>
            <p>Changes here also update the top bar, the “Get in touch” booking section, the WhatsApp buttons, the mega menu and Google’s business information.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <FormInput id="contact_email" v-model="form.contact.email" type="email" label="Email" :error="form.errors['contact.email']" :maxlength="255" required />
            <FormInput id="contact_whatsapp" v-model="form.contact.whatsapp_number" label="WhatsApp number" placeholder="918285354608" :error="form.errors['contact.whatsapp_number']" :maxlength="20" required hint="Country code + number, digits only." />
        </div>
        <ListEditor v-model="form.contact.phones" label="Phone numbers (first one is the main number)" placeholder="+91 82853 54608" add-label="Add phone number" :max="4" :errors="form.errors" error-key="contact.phones" />
        <p v-if="form.errors['contact.phones']" class="-mt-3 text-sm text-red-600">{{ form.errors['contact.phones'] }}</p>
        <FormTextarea id="contact_address" v-model="form.contact.address" label="Address" :rows="2" :error="form.errors['contact.address']" :maxlength="300" required />
        <FormInput id="contact_maps" v-model="form.contact.maps_url" type="url" label="Google Maps link" placeholder="https://maps.app.goo.gl/…" :error="form.errors['contact.maps_url']" :maxlength="255" hint="Used for “Get directions” and the clickable footer address." />
        <div class="grid gap-5 sm:grid-cols-2">
            <FormInput id="contact_hours" v-model="form.contact.hours" label="Opening hours" :error="form.errors['contact.hours']" :maxlength="80" required hint="Top bar and booking section." />
            <FormInput id="contact_hours_short" v-model="form.contact.hours_short" label="Opening hours (short)" :placeholder="form.contact.hours" :error="form.errors['contact.hours_short']" :maxlength="60" hint="Footer. Leave blank to use the full version." />
        </div>
        <FormInput id="contact_website" v-model="form.contact.website_url" type="url" label="Website address" placeholder="https://www.mindessencewellness.com" :error="form.errors['contact.website_url']" :maxlength="255" />

        <div class="space-y-4 border-t border-slate-100 pt-5">
            <p class="text-sm font-semibold text-slate-800">Footer contact column</p>
            <FormInput id="contact_heading" v-model="form.contact_heading" label="Column heading" :error="form.errors.contact_heading" :maxlength="40" required />
            <ToggleSwitch v-model="form.show_website" label="Show the website address" />
            <ToggleSwitch v-model="form.show_hours" label="Show opening hours" />
        </div>
    </FormSection>
</template>
