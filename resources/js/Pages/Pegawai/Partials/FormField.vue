<template>
    <div class="space-y-1.5">
        <label v-if="label" :for="id" class="text-sm font-medium text-slate-600">
            {{ label }}<span v-if="required" class="text-rose-600"> *</span>
        </label>
        <slot />
        <p v-if="error" class="text-xs font-medium text-rose-600">{{ error }}</p>
    </div>
</template>

<script setup>
const props = defineProps({
    label: { type: String, default: '' },
    id: { type: String, default: '' },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false },
});
</script>

<style scoped>
/* styling handled globally via .form-input utility */
</style>
