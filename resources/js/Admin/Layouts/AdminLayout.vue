<script setup>
import { ref } from 'vue';
import FlashToast from '@/Admin/Components/Global/FlashToast.vue';
import AdminSidebar from '@/Admin/Components/Global/AdminSidebar.vue';
import AdminTopbar from '@/Admin/Components/Global/AdminTopbar.vue';

defineProps({
    // Shown in the top bar.
    title: { type: String, default: '' },
});

const sidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-screen">
        <AdminSidebar :open="sidebarOpen" @close="sidebarOpen = false" />

        <div class="lg:pl-64">
            <AdminTopbar :title="title" @toggle-sidebar="sidebarOpen = !sidebarOpen" />
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>

        <FlashToast />
    </div>
</template>
