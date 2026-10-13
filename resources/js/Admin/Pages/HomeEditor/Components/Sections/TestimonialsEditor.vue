<script setup>
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import FormSection from '@/Admin/Components/Global/FormSection.vue';
import FormTextarea from '@/Admin/Components/Global/FormTextarea.vue';
import ItemList from '@/Admin/Components/Global/ItemList.vue';

defineProps({
    form: { type: Object, required: true },
});

const newItem = () => ({ quote: '', name: '', service: '', rating: 5 });
</script>

<template>
    <FormSection id="heading" title="Heading">
        <FormInput id="t-eyebrow" v-model="form.eyebrow" label="Small label" :error="form.errors.eyebrow" :maxlength="80" />
        <FormInput id="t-heading" v-model="form.heading" label="Heading" :error="form.errors.heading" :maxlength="120" required />
        <div class="flex items-center gap-3 text-sm text-slate-600">
            <label for="t-interval">Change testimonial every</label>
            <input id="t-interval" v-model.number="form.interval" type="number" min="3" max="30" class="w-20 rounded-lg border border-slate-300 px-2 py-1.5 text-center" />
            seconds
        </div>
    </FormSection>

    <FormSection id="items" title="Testimonials" description="Only publish testimonials with the client’s consent. Use initials to protect their privacy.">
        <ItemList v-model="form.items" v-slot="{ item, index: i }" :new-item="newItem" item-label="Testimonial" add-label="Add testimonial" title-key="name" :min="1" :max="20" :errors="form.errors" error-key="items">
            <FormTextarea :id="`t-${i}-quote`" v-model="item.quote" label="Quote" :rows="3" :error="form.errors[`items.${i}.quote`]" :maxlength="600" required />
            <div class="grid gap-4 sm:grid-cols-[1fr_1fr_auto]">
                <FormInput :id="`t-${i}-name`" v-model="item.name" label="Name / initials" :error="form.errors[`items.${i}.name`]" :maxlength="60" required />
                <FormInput :id="`t-${i}-service`" v-model="item.service" label="Service" :error="form.errors[`items.${i}.service`]" :maxlength="60" />
                <div>
                    <p class="mb-1.5 text-sm font-medium text-slate-700">Rating</p>
                    <div class="flex gap-0.5 py-2" role="radiogroup" :aria-label="`Rating for testimonial ${i + 1}`">
                        <button v-for="n in 5" :key="n" type="button" class="text-xl leading-none transition" :class="n <= item.rating ? 'text-gold' : 'text-slate-300'" :aria-label="`${n} star${n > 1 ? 's' : ''}`" @click="item.rating = n">★</button>
                    </div>
                </div>
            </div>
        </ItemList>
    </FormSection>
</template>
