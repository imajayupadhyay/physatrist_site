<script setup>
// Coloured pill for a lead status (keys from config/leads.php).
defineProps({
    status: { type: String, required: true },
    label: { type: String, default: '' },
});

const styles = {
    new: 'bg-amber-50 text-amber-700 ring-amber-200',
    contacted: 'bg-sky-50 text-sky-700 ring-sky-200',
    booked: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    closed: 'bg-slate-100 text-slate-600 ring-slate-200',
};
</script>

<template>
    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap ring-1 ring-inset" :class="styles[status] ?? styles.closed">
        <span v-if="status === 'new'" class="size-1.5 rounded-full bg-amber-500" />
        {{ label || status }}
    </span>
</template>
