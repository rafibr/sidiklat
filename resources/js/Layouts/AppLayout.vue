<template>
    <v-app>
        <div class="relative min-h-screen bg-slate-100 text-slate-900">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute inset-x-0 top-[-220px] h-[420px] bg-gradient-to-b from-indigo-200/70 via-indigo-100/40 to-transparent"></div>
                <div class="absolute right-[-160px] top-[320px] h-80 w-80 rounded-full bg-gradient-to-br from-slate-200/60 to-transparent blur-3xl"></div>
            </div>

            <header class="sticky top-0 z-50 border-b border-white/60 bg-white/80 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center space-x-3">
                        <button class="text-slate-600 transition md:hidden" @click="toggleMobileMenu" aria-label="Buka navigasi">
                            <i class="fas" :class="isMobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                        </button>
                        <Link href="/" class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-slate-100 shadow-sm">
                                <i class="fas fa-sparkles"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold leading-tight">SIDIKLAT</p>
                                <p class="text-xs text-slate-500">Sistem Informasi Diklat ASN</p>
                            </div>
                        </Link>
                    </div>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link v-for="tab in navigation" :key="tab.name" :href="tab.href"
                            :class="[
                                'inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-colors duration-150',
                                currentUrl === tab.href
                                    ? 'bg-slate-900 text-white shadow-sm'
                                    : 'text-slate-600 hover:bg-slate-200/60'
                            ]">
                            <i :class="tab.icon" class="text-xs"></i>
                            <span>{{ tab.name }}</span>
                        </Link>
                    </nav>
                    <div class="flex items-center space-x-3">
                        <div v-if="user" class="hidden text-right sm:flex sm:flex-col">
                            <span class="text-sm font-semibold">{{ user.name }}</span>
                            <span class="text-xs text-slate-500">{{ user.email }}</span>
                        </div>
                        <button type="button" @click="logout"
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 px-4 py-2 text-sm font-medium text-slate-600 backdrop-blur transition hover:border-slate-300 hover:text-slate-900">
                            <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                            Keluar
                        </button>
                    </div>
                </div>

                <transition name="fade">
                    <div v-if="isMobileMenuOpen" class="border-t border-slate-200/70 bg-white/95 backdrop-blur md:hidden">
                        <div class="mx-auto max-w-7xl px-4 py-4">
                            <div class="space-y-2">
                                <Link v-for="tab in navigation" :key="tab.name" :href="tab.href"
                                    class="flex items-center justify-between rounded-xl border border-transparent px-3 py-2 text-sm font-medium transition-colors"
                                    :class="currentUrl === tab.href ? 'border-slate-900 text-slate-900' : 'text-slate-600 hover:border-slate-300 hover:bg-slate-100'"
                                    @click="closeMobileMenu">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                                            <i :class="tab.icon"></i>
                                        </span>
                                        {{ tab.name }}
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                                </Link>
                            </div>
                            <div v-if="user" class="mt-5 rounded-2xl border border-slate-200/70 bg-slate-50/90 p-4">
                                <p class="text-sm font-semibold text-slate-800">{{ user.name }}</p>
                                <p class="text-xs text-slate-500">{{ user.email }}</p>
                            </div>
                        </div>
                    </div>
                </transition>
            </header>

            <main class="relative z-10 mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <slot />
            </main>
        </div>
    </v-app>
</template>

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
const currentUrl = computed(() => page.url);
const isMobileMenuOpen = ref(false);

function toggleMobileMenu() {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
}

function closeMobileMenu() {
    isMobileMenuOpen.value = false;
}

function logout() {
    router.post(route('logout'));
}
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
