<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';
import { iconMarkup } from '@/Shared/serviceIcons';

defineProps({
    form: { type: Object, required: true },
    icons: { type: Array, required: true },
});
</script>

<template>
    <FormSection id="card" title="Homepage card" description="The card in the homepage “Areas of practice” grid.">
        <div class="grid gap-6 lg:grid-cols-[1fr_260px]">
            <div class="space-y-5">
                <div>
                    <p class="mb-1.5 text-sm font-medium text-slate-700">Icon</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="icon in icons"
                            :key="icon"
                            type="button"
                            class="grid size-11 place-items-center rounded-lg border transition"
                            :class="form.icon === icon ? 'border-forest bg-forest text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'"
                            :aria-label="icon"
                            :aria-pressed="form.icon === icon"
                            @click="form.icon = icon"
                        >
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" v-html="iconMarkup(icon)" />
                        </button>
                    </div>
                    <p v-if="form.errors.icon" class="mt-1 text-sm text-red-600">{{ form.errors.icon }}</p>
                </div>
                <FormInput id="audience" v-model="form.audience" label="Who it’s for" placeholder="e.g. Teens · Adults" :error="form.errors.audience" :maxlength="80" />
                <FormTextarea id="summary" v-model="form.summary" label="Short description" :rows="3" :error="form.errors.summary" :maxlength="300" :recommended="[90, 160]" required />
                <ToggleSwitch v-model="form.show_on_home" label="Show on homepage" />
                <ToggleSwitch v-model="form.is_featured" label="Highlight card" description="Dark green card, like “Psychological Assessment”." />
            </div>

            <!-- Live preview -->
            <div>
                <p class="mb-1.5 text-sm font-medium text-slate-700">Preview</p>
                <div class="flex flex-col gap-2.5 rounded-2xl border p-6" :class="form.is_featured ? 'border-forest bg-forest' : 'border-[#E3DCCF] bg-[#F8F4EC]'">
                    <span class="mb-1 grid size-12 place-items-center rounded-full" :class="form.is_featured ? 'bg-white/10 text-gold-soft' : 'bg-sage-soft text-forest'">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="iconMarkup(form.icon)" />
                    </span>
                    <p class="font-serif text-xl leading-tight" :class="form.is_featured ? 'text-white' : 'text-forest-deep'" style="font-family: 'Cormorant Garamond', Georgia, serif">{{ form.title || 'Service name' }}</p>
                    <p v-if="form.audience" class="text-[10px] font-medium tracking-[0.12em] uppercase" :class="form.is_featured ? 'text-gold-soft' : 'text-gold'">{{ form.audience }}</p>
                    <p class="text-xs leading-relaxed" :class="form.is_featured ? 'text-[#c8d2c6]' : 'text-slate-500'">{{ form.summary || 'Short description…' }}</p>
                    <p class="pt-1 text-xs font-medium" :class="form.is_featured ? 'text-gold-soft' : 'text-forest'">Learn more →</p>
                </div>
                <p v-if="!form.show_on_home || !form.is_published" class="mt-2 text-xs text-amber-600">Not currently shown on the homepage.</p>
            </div>
        </div>
    </FormSection>
</template>
