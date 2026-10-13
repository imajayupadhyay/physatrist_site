<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormInput from '@/Admin/Components/Global/FormInput.vue';
import Modal from '@/Admin/Components/Global/Modal.vue';
import ToggleSwitch from '@/Admin/Components/Global/ToggleSwitch.vue';

// Create (item = null) or edit a menu item.
const props = defineProps({
    show: { type: Boolean, default: false },
    item: { type: Object, default: null },
    parentId: { type: Number, default: null },
    topLevel: { type: Array, required: true }, // [{ id, label }]
    services: { type: Array, required: true },
});
const emit = defineEmits(['close']);

const isEdit = computed(() => !!props.item);

const form = useForm({
    type: 'link',
    label: '',
    url: '',
    service_id: '',
    parent_id: '',
    description: '',
    dropdown_style: 'simple',
    open_in_new_tab: false,
    is_visible: true,
});

watch(
    () => props.show,
    (show) => {
        if (!show) return;
        const item = props.item;
        form.clearErrors();
        form.type = item?.type ?? 'link';
        form.label = item?.label ?? '';
        form.url = item?.url ?? '';
        form.service_id = item?.service_id ?? '';
        form.parent_id = item?.parent_id ?? props.parentId ?? '';
        form.description = item?.description ?? '';
        form.dropdown_style = item?.dropdown_style ?? 'simple';
        form.open_in_new_tab = item?.open_in_new_tab ?? false;
        form.is_visible = item?.is_visible ?? true;
    },
);

const isTopLevel = computed(() => !form.parent_id);
const hasChildren = computed(() => (props.item?.children?.length ?? 0) > 0);
// An item can't be placed under itself.
const placements = computed(() => props.topLevel.filter((top) => top.id !== props.item?.id));
const selectedService = computed(() => props.services.find((s) => s.id === Number(form.service_id)));

const quickLinks = [
    { label: 'Home', url: '/' },
    { label: 'About', url: '/#about' },
    { label: 'Services', url: '/#services' },
    { label: 'Assessments', url: '/#assessment' },
    { label: 'Videos', url: '/#videos' },
    { label: 'FAQ', url: '/#faq' },
    { label: 'Contact', url: '/#contact' },
    { label: 'Booking form', url: '/#appointment' },
];

const submit = () => {
    const options = { preserveScroll: true, preserveState: true, onSuccess: () => emit('close') };
    isEdit.value ? form.put(`/secure-admin/header/menu/${props.item.id}`, options) : form.post('/secure-admin/header/menu', options);
};

const selectClass = 'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10';
</script>

<template>
    <Modal :show="show" :title="isEdit ? 'Edit menu item' : form.parent_id ? 'Add sub-item' : 'Add menu item'" max-width="max-w-xl" @close="emit('close')">
        <form id="menu-item-form" class="space-y-5" @submit.prevent="submit">
            <!-- Type -->
            <div class="grid grid-cols-2 gap-2 rounded-lg bg-slate-100 p-1 text-sm">
                <button type="button" class="rounded-md py-2 font-medium transition" :class="form.type === 'link' ? 'bg-white text-forest shadow-sm' : 'text-slate-500'" @click="form.type = 'link'">Custom link</button>
                <button type="button" class="rounded-md py-2 font-medium transition" :class="form.type === 'service' ? 'bg-white text-forest shadow-sm' : 'text-slate-500'" @click="form.type = 'service'">Service page</button>
            </div>

            <template v-if="form.type === 'service'">
                <div>
                    <label for="menu-service" class="mb-1.5 block text-sm font-medium text-slate-700">Service page <span class="text-red-500">*</span></label>
                    <select id="menu-service" v-model="form.service_id" :class="[selectClass, form.errors.service_id && 'border-red-400']">
                        <option value="" disabled>Choose a service…</option>
                        <option v-for="service in services" :key="service.id" :value="service.id">{{ service.title }}{{ service.is_published ? '' : ' (unpublished)' }}</option>
                    </select>
                    <p v-if="form.errors.service_id" class="mt-1 text-sm text-red-600">{{ form.errors.service_id }}</p>
                    <p v-else class="mt-1 text-xs text-slate-400">Links to /services/{{ selectedService?.slug ?? '…' }} and updates automatically if the service’s URL changes.</p>
                </div>
                <FormInput id="menu-label" v-model="form.label" label="Menu text" :placeholder="selectedService?.title ?? 'Same as the service name'" :error="form.errors.label" :maxlength="60" hint="Leave blank to use the service name." />
            </template>

            <template v-else>
                <FormInput id="menu-label" v-model="form.label" label="Menu text" :error="form.errors.label" :maxlength="60" required />
                <div>
                    <FormInput id="menu-url" v-model="form.url" label="Link" placeholder="/#about or https://…" :error="form.errors.url" :maxlength="255" required hint="A page path (/about), a homepage section (/#faq), a full URL, mailto: or tel:" />
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <button v-for="link in quickLinks" :key="link.url" type="button" class="rounded-full border border-slate-200 px-2.5 py-1 text-xs text-slate-600 transition hover:border-forest hover:text-forest" @click="form.url = link.url; form.label ||= link.label">
                            {{ link.label }}
                        </button>
                    </div>
                </div>
            </template>

            <!-- Placement -->
            <div>
                <label for="menu-parent" class="mb-1.5 block text-sm font-medium text-slate-700">Position</label>
                <select id="menu-parent" v-model="form.parent_id" :disabled="hasChildren" :class="[selectClass, form.errors.parent_id && 'border-red-400', hasChildren && 'bg-slate-50 text-slate-400']">
                    <option value="">Top level</option>
                    <option v-for="top in placements" :key="top.id" :value="top.id">Inside “{{ top.label }}” dropdown</option>
                </select>
                <p v-if="form.errors.parent_id" class="mt-1 text-sm text-red-600">{{ form.errors.parent_id }}</p>
                <p v-else-if="hasChildren" class="mt-1 text-xs text-slate-400">This item has sub-items, so it stays at the top level.</p>
            </div>

            <!-- Top level: dropdown style -->
            <div v-if="isTopLevel">
                <p class="mb-1.5 text-sm font-medium text-slate-700">Dropdown style <span class="font-normal text-slate-400">(when it has sub-items)</span></p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <label v-for="style in [{ key: 'simple', title: 'Simple dropdown', text: 'A compact list under the item.' }, { key: 'mega', title: 'Mega menu', text: 'Full-width panel with icons, descriptions and the promo box.' }]" :key="style.key" class="flex cursor-pointer gap-3 rounded-lg border p-3 transition" :class="form.dropdown_style === style.key ? 'border-forest bg-sage-soft/40' : 'border-slate-200 hover:border-slate-300'">
                        <input v-model="form.dropdown_style" type="radio" :value="style.key" class="mt-1 accent-forest" />
                        <span>
                            <span class="block text-sm font-medium text-slate-800">{{ style.title }}</span>
                            <span class="block text-xs text-slate-500">{{ style.text }}</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Sub-item: description -->
            <FormInput
                v-else
                id="menu-description"
                v-model="form.description"
                label="Short description"
                :placeholder="form.type === 'service' ? 'Uses the service’s short description' : 'Optional'"
                :error="form.errors.description"
                :maxlength="160"
                hint="Shown under the link in dropdowns and mega menus."
            />

            <div class="space-y-4 border-t border-slate-100 pt-4">
                <ToggleSwitch v-model="form.is_visible" label="Show on website" />
                <ToggleSwitch v-model="form.open_in_new_tab" label="Open in a new tab" description="Best for links to other websites." />
            </div>
        </form>

        <template #footer>
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50" @click="emit('close')">Cancel</button>
            <button type="submit" form="menu-item-form" :disabled="form.processing" class="rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep disabled:opacity-60">
                {{ form.processing ? 'Saving…' : isEdit ? 'Save changes' : 'Add item' }}
            </button>
        </template>
    </Modal>
</template>
