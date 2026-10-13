<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AddServicesModal from './AddServicesModal.vue';
import DeleteMenuItemModal from './DeleteMenuItemModal.vue';
import MenuItemModal from './MenuItemModal.vue';
import MenuItemRow from './MenuItemRow.vue';

const props = defineProps({
    menu: { type: Array, required: true },
    services: { type: Array, required: true },
});

const visit = { preserveScroll: true, preserveState: true };
const move = (item, direction) => router.post(`/secure-admin/header/menu/${item.id}/move`, { direction }, visit);
const toggle = (item) => router.patch(`/secure-admin/header/menu/${item.id}/toggle`, {}, visit);

// Modals
const editor = ref({ show: false, item: null, parentId: null });
const openCreate = (parentId = null) => (editor.value = { show: true, item: null, parentId });
const openEdit = (item) => (editor.value = { show: true, item, parentId: item.parent_id });

const addingServicesTo = ref(null);
const deleting = ref(null);

const topLevel = computed(() => props.menu.map(({ id, display_label }) => ({ id, label: display_label })));
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-semibold text-slate-800">Menu items</h3>
                <p class="mt-0.5 text-sm text-slate-500">Use the arrows to reorder. Items with sub-items open a dropdown or mega menu.</p>
            </div>
            <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-forest px-4 py-2.5 text-sm font-medium text-white transition hover:bg-forest-deep" @click="openCreate()">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
                Add menu item
            </button>
        </div>

        <ul class="divide-y divide-slate-100">
            <li v-for="(item, i) in menu" :key="item.id">
                <MenuItemRow
                    :item="item"
                    :first="i === 0"
                    :last="i === menu.length - 1"
                    @move="move(item, $event)"
                    @toggle="toggle(item)"
                    @edit="openEdit(item)"
                    @delete="deleting = item"
                    @add-child="openCreate(item.id)"
                    @add-services="addingServicesTo = item"
                />
                <ul v-if="item.children.length" class="border-t border-dashed border-slate-100 bg-slate-50/50 py-1">
                    <li v-for="(child, j) in item.children" :key="child.id">
                        <MenuItemRow
                            :item="child"
                            nested
                            :first="j === 0"
                            :last="j === item.children.length - 1"
                            @move="move(child, $event)"
                            @toggle="toggle(child)"
                            @edit="openEdit(child)"
                            @delete="deleting = child"
                        />
                    </li>
                </ul>
            </li>
            <li v-if="!menu.length" class="px-5 py-14 text-center text-sm text-slate-500">The menu is empty. Click “Add menu item” to start.</li>
        </ul>
    </div>

    <MenuItemModal
        :show="editor.show"
        :item="editor.item"
        :parent-id="editor.parentId"
        :top-level="topLevel"
        :services="services"
        @close="editor.show = false"
    />
    <AddServicesModal :parent="addingServicesTo" :services="services" @close="addingServicesTo = null" />
    <DeleteMenuItemModal :item="deleting" @close="deleting = null" />
</template>
