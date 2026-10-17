<template>
    <transition name="slide-up">
        <div v-if="visible" class="fixed inset-x-0 bottom-4 z-40 flex justify-center px-4 sm:bottom-6">
            <div class="relative" @mouseenter="expand" @mouseleave="collapse">
                <button type="button"
                    class="flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:bg-slate-800"
                    :aria-expanded="expanded" aria-controls="floating-year-options" @click="toggle"
                >
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                        <i class="fas fa-calendar"></i>
                    </span>
                    <span>{{ selectedYear }}</span>
                    <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div v-if="expanded" id="floating-year-options" class="absolute bottom-full right-0 z-50 mb-3 w-60 overflow-hidden rounded-xl border border-sky-100 bg-white shadow-lg">
                    <div class="border-b border-sky-100 bg-slate-900 px-4 py-3 text-white">
                        <p class="text-xs font-semibold tracking-wide uppercase">Pilih Tahun Laporan</p>
                    </div>
                    <div class="px-4 py-3">
                        <select aria-label="Tahun laporan" :value="selectedYear" @change="onYearChange"
                            class="w-full rounded-xl border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/30">
                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                        </select>
                        <p class="mt-2 text-xs text-slate-500">Sesuaikan ringkasan performa dengan tahun yang Anda perlukan.</p>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    selectedYear: { type: [Number, String], required: true },
    years: { type: Array, default: () => [] },
    visible: { type: Boolean, default: false },
});

const emit = defineEmits(['change']);
const expanded = ref(false);

watch(() => props.visible, (value) => {
    if (!value) {
        expanded.value = false;
    }
});

function expand() {
    expanded.value = true;
}

function collapse() {
    expanded.value = false;
}

function toggle() {
    expanded.value = !expanded.value;
}

function onYearChange(event) {
    emit('change', event.target.value);
    expanded.value = false;
}
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.25s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(16px);
}
</style>
