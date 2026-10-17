<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <v-app>
        <div class="workspace min-h-screen">
            <a href="#main-content" class="skip-link">Langsung ke konten</a>
            <header class="workspace-header sticky top-0 z-40">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
                    <Link href="/" class="flex min-w-0 items-center gap-3">
                        <span class="brand-mark"><i aria-hidden="true" class="fas fa-graduation-cap"></i></span>
                        <span><span class="block text-lg font-bold leading-tight tracking-wide">SIDIKLAT</span>
                            <span class="block text-xs text-slate-600">Sistem Informasi Diklat ASN</span></span>
                    </Link>
                    <Link href="/login" class="workspace-button-primary"><i aria-hidden="true" class="fas fa-right-to-bracket"></i>Masuk</Link>
                </div>
                <div class="bg-slate-900 px-4 py-3 text-sm text-white sm:px-6">
                    <p class="mx-auto max-w-7xl">Inspektorat Kota Banjarbaru <span class="mx-2 text-slate-400">/</span> Pemantauan Diklat ASN</p>
                </div>
            </header>
            <main id="main-content" class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8" tabindex="-1">
                <div class="mb-6 flex items-start gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3">
                    <i aria-hidden="true" class="fas fa-info-circle mt-1 text-sky-700"></i>
                    <div><p class="text-sm font-semibold text-slate-900">Mode Stakeholder</p>
                        <p class="mt-1 text-sm text-slate-600">Dasbor publik untuk memantau progress pelatihan. Masuk untuk mengelola data.</p></div>
                </div>
                <slot />
            </main>
            <footer class="mx-auto max-w-7xl border-t border-slate-200 px-4 py-5 text-xs text-slate-600 sm:px-6 lg:px-8">SIDIKLAT · Inspektorat Kota Banjarbaru</footer>
        </div>
    </v-app>
</template>
