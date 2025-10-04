<template>
    <v-app>
        <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-50 via-sky-50 to-emerald-50 text-slate-900">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute inset-x-0 top-[-280px] h-[520px] bg-gradient-to-b from-indigo-400/40 via-sky-300/30 to-transparent"></div>
                <div class="absolute left-[-120px] top-[220px] h-72 w-72 rounded-full bg-sky-300/40 blur-3xl"></div>
                <div class="absolute right-[-200px] bottom-[-40px] h-96 w-96 rounded-full bg-emerald-300/40 blur-3xl"></div>
            </div>

            <header class="sticky top-0 z-50">
                <div class="relative overflow-hidden border-b border-indigo-200/40 bg-gradient-to-r from-indigo-700 via-sky-600 to-emerald-500 text-white shadow-lg">
                    <div class="absolute inset-y-0 right-0 w-1/3 bg-white/10 blur-3xl"></div>
                    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex items-center space-x-3">
                            <button class="text-white transition md:hidden" @click="toggleMobileMenu" aria-label="Buka navigasi">
                            <i class="fas text-white" :class="isMobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                            </button>
                        <Link href="/" class="flex items-center space-x-3 text-white">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/20 text-white shadow-sm">
                                <i class="fas fa-sparkles"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold leading-tight">SIDIKLAT</p>
                                <p class="text-xs text-white/80">Sistem Informasi Diklat ASN</p>
                            </div>
                        </Link>
                    </div>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link v-for="tab in navigation" :key="tab.name" :href="tab.href"
                            :class="[
                                'inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-colors duration-150',
                                currentUrl === tab.href
                                    ? 'bg-white/20 text-white shadow-sm'
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]">
                            <i :class="tab.icon" class="text-xs"></i>
                            <span>{{ tab.name }}</span>
                        </Link>
                    </nav>
                    <div class="flex items-center space-x-3">
                        <div v-if="user" class="hidden text-right sm:flex sm:flex-col text-white">
                            <span class="text-sm font-semibold text-white">{{ user.name }}</span>
                            <span class="text-xs text-white/80">{{ user.email }}</span>
                        </div>
                        <button type="button" @click="logout"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                            <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                            Keluar
                        </button>
                    </div>
                </div>

                <transition name="fade">
                    <div v-if="isMobileMenuOpen" class="border-t border-white/20 bg-gradient-to-b from-indigo-700 via-sky-700 to-emerald-600 text-white backdrop-blur md:hidden">
                        <div class="mx-auto max-w-7xl px-4 py-4">
                            <div class="space-y-2">
                                <Link v-for="tab in navigation" :key="tab.name" :href="tab.href"
                                    class="flex items-center justify-between rounded-xl border border-transparent px-3 py-2 text-sm font-medium transition-colors"
                                    :class="currentUrl === tab.href ? 'border-white/40 bg-white/10 text-white' : 'text-white/80 hover:border-white/30 hover:bg-white/10'"
                                    @click="closeMobileMenu">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-white">
                                            <i :class="tab.icon"></i>
                                        </span>
                                        {{ tab.name }}
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-white/50"></i>
                                </Link>
                            </div>
                            <div v-if="user" class="mt-5 rounded-2xl border border-white/20 bg-white/10 p-4 text-white">
                                <p class="text-sm font-semibold text-white">{{ user.name }}</p>
                                <p class="text-xs text-white/80">{{ user.email }}</p>
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
