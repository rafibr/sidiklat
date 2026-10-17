<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const navigation = [
    { name: 'Dashboard', href: '/', icon: 'fas fa-gauge' },
    { name: 'Data Pegawai', href: '/pegawai', icon: 'fas fa-users' },
    { name: 'Data Pelatihan', href: '/pelatihan', icon: 'fas fa-graduation-cap' },
    { name: 'Progress JP', href: '/progress', icon: 'fas fa-signal' },
    { name: 'Perbandingan', href: '/pelatihan/comparison', icon: 'fas fa-scale-balanced' },
];
const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const currentUrl = computed(() => page.url.split('?')[0]);
const isMobileMenuOpen = ref(false);
function isActive(href) {
    if (href === '/' || href === '/pelatihan/comparison') return currentUrl.value === href;
    if (href === '/pelatihan' && currentUrl.value === '/pelatihan/comparison') return false;
    return currentUrl.value === href || currentUrl.value.startsWith(href + '/');
}
function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <v-app>
        <div class="workspace min-h-screen">
            <a href="#main-content" class="skip-link">Langsung ke konten</a>
            <header class="workspace-header sticky top-0 z-40">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
                    <Link href="/" class="flex min-w-0 items-center gap-3">
                        <span class="brand-mark"><i aria-hidden="true" class="fas fa-graduation-cap"></i></span>
                        <span class="min-w-0">
                            <span class="block text-lg font-bold leading-tight tracking-wide">SIDIKLAT</span>
                            <span class="block text-xs text-slate-600">Sistem Informasi Diklat ASN</span>
                        </span>
                    </Link>
                    <div class="flex items-center gap-3">
                        <div v-if="user" class="hidden max-w-64 text-right sm:block">
                            <p class="truncate text-sm font-semibold">{{ user.name }}</p>
                            <p class="truncate text-xs text-slate-600">{{ user.email }}</p>
                        </div>
                        <button type="button" @click="logout" class="workspace-button-secondary">
                            <i aria-hidden="true" class="fas fa-arrow-right-from-bracket"></i><span class="sr-only sm:not-sr-only">Keluar</span>
                        </button>
                        <button type="button" class="workspace-button-secondary md:hidden" aria-controls="mobile-navigation"
                            :aria-expanded="isMobileMenuOpen" :aria-label="isMobileMenuOpen ? 'Tutup navigasi' : 'Buka navigasi'"
                            @click="isMobileMenuOpen = !isMobileMenuOpen">
                            <i aria-hidden="true" class="fas" :class="isMobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                        </button>
                    </div>
                </div>
                <div class="border-t border-slate-200 bg-slate-900">
                    <nav aria-label="Navigasi utama" class="mx-auto hidden max-w-7xl items-center gap-1 px-4 py-2 sm:px-6 md:flex lg:px-8">
                        <Link v-for="tab in navigation" :key="tab.href" :href="tab.href" class="workspace-nav-link"
                            :class="{ 'is-active': isActive(tab.href) }" :aria-current="isActive(tab.href) ? 'page' : undefined">
                            <i aria-hidden="true" :class="tab.icon"></i><span>{{ tab.name }}</span>
                        </Link>
                    </nav>
                    <nav v-if="isMobileMenuOpen" id="mobile-navigation" aria-label="Navigasi utama seluler"
                        class="grid gap-1 px-4 py-3 md:hidden" @keydown.esc="isMobileMenuOpen = false">
                        <Link v-for="tab in navigation" :key="tab.href" :href="tab.href" class="workspace-nav-link"
                            :class="{ 'is-active': isActive(tab.href) }" :aria-current="isActive(tab.href) ? 'page' : undefined"
                            @click="isMobileMenuOpen = false">
                            <i aria-hidden="true" :class="tab.icon"></i><span>{{ tab.name }}</span>
                        </Link>
                    </nav>
                </div>
            </header>
            <main id="main-content" class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8" tabindex="-1"><slot /></main>
            <footer class="mx-auto max-w-7xl border-t border-slate-200 px-4 py-5 text-xs text-slate-600 sm:px-6 lg:px-8">
                SIDIKLAT · Inspektorat Kota Banjarbaru
            </footer>
        </div>
    </v-app>
</template>
