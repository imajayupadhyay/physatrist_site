<script setup>
import { computed } from 'vue';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

const props = defineProps({
    form: { type: Object, required: true },
});

const helplineParts = computed(() => (props.form.helpline_text ?? '').split('**').map((text, i) => ({ text, bold: i % 2 === 1 })));
const year = new Date().getFullYear();
</script>

<template>
    <FormSection id="helpline" title="Crisis helpline box" description="The highlighted box under the contact details.">
        <ToggleSwitch v-model="form.helpline_visible" label="Show the helpline box" />
        <template v-if="form.helpline_visible">
            <FormTextarea id="helpline_text" v-model="form.helpline_text" label="Text" :rows="2" :error="form.errors.helpline_text" :maxlength="300" required hint="Wrap words in **double asterisks** to make them bold." />
            <div class="rounded-xl bg-forest-deep p-4">
                <p class="mb-2 text-[11px] font-medium tracking-wide text-white/40 uppercase">Preview</p>
                <div class="rounded-xl border border-gold/40 bg-gold/10 px-4 py-3 text-sm text-[#e6dcc9]">
                    <template v-for="(part, i) in helplineParts" :key="i"><b v-if="part.bold">{{ part.text }}</b><template v-else>{{ part.text }}</template></template>
                </div>
            </div>
        </template>
    </FormSection>

    <FormSection id="bottom" title="Bottom bar" description="The thin line at the very bottom of the page.">
        <FormInput id="copyright_text" v-model="form.copyright_text" label="Left text" :error="form.errors.copyright_text" :maxlength="200" :hint="`Use {year} for the current year (shows ${year}).`" />
        <FormInput id="bottom_text" v-model="form.bottom_text" label="Right text" :error="form.errors.bottom_text" :maxlength="200" />
    </FormSection>
</template>
