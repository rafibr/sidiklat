<template>
    <v-app>
        <div
            class="relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-50 via-sky-50 to-emerald-50 text-slate-900">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute inset-x-0 top-[-280px] h-[520px] bg-gradient-to-b from-indigo-400/40 via-sky-300/30 to-transparent">
                </div>
                <div class="absolute left-[-120px] top-[220px] h-72 w-72 rounded-full bg-sky-300/40 blur-3xl"></div>
                <div class="absolute right-[-200px] bottom-[-40px] h-96 w-96 rounded-full bg-emerald-300/40 blur-3xl">
                </div>
            </div>

            <header class="sticky top-0 z-50">
                <div
                    class="relative overflow-hidden border-b border-indigo-200/40 bg-gradient-to-r from-indigo-700 via-sky-600 to-emerald-500 text-white shadow-lg">
                    <div class="absolute inset-y-0 right-0 w-1/3 bg-white/10 blur-3xl"></div>
                    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex items-center space-x-3">
                            <Link href="/" class="flex items-center space-x-3 text-white">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/20 text-white shadow-sm">
                                <i class="fas fa-sparkles"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold leading-tight">SIDIKLAT</p>
                                <p class="text-xs text-white/80">Sistem Informasi Diklat ASN</p>
                            </div>
                            </Link>
                        </div>
                        <div class="flex items-center space-x-3">
                            <Link href="/login"
                                class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                            <i class="fas fa-right-to-bracket text-xs"></i>
                            Login
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <!-- Info banner untuk stakeholder -->
                <div class="mb-6 rounded-2xl border border-indigo-200/60 bg-indigo-50/50 px-4 py-3 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100">
                            <i class="fas fa-info-circle text-indigo-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-indigo-900">Mode Stakeholder</p>
                            <p class="text-xs text-indigo-700">Anda melihat halaman ini dalam mode publik untuk
                                pemantauan progress. Login untuk akses penuh.</p>
                        </div>
                    </div>
                </div>

                <slot />
            </main>
        </div>
    </v-app>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
