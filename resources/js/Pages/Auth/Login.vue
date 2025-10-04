<template>
    <div class="relative min-h-screen overflow-hidden bg-slate-950 text-slate-100">
        <Head title="Masuk" />
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute inset-x-0 top-[-320px] h-[520px] bg-gradient-to-b from-indigo-600/20 via-slate-900/60 to-transparent"></div>
            <div class="absolute left-[-180px] bottom-[120px] h-96 w-96 rounded-full bg-indigo-500/10 blur-3xl"></div>
            <div class="absolute right-[-120px] top-[160px] h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
        </div>

        <div class="relative flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-5xl">
                <div class="grid gap-8 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-xl lg:grid-cols-5 lg:p-12">
                    <div class="space-y-6 lg:col-span-3">
                        <div>
                            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-200">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-white">
                                    <i class="fas fa-sparkles"></i>
                                </span>
                                SIDIKLAT Platform
                            </p>
                            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-white sm:text-4xl">Satu portal untuk memantau kinerja diklat ASN</h1>
                            <p class="mt-4 text-sm text-slate-300">Terintegrasi dengan data pegawai, jenis pelatihan, serta target Jam Pelajaran (JP) tahunan untuk memastikan pengembangan kompetensi berjalan efektif.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-200">Pemantauan Pegawai</span>
                                    <i class="fas fa-users text-slate-300"></i>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Cek progres JP pegawai secara real-time beserta unit kerja dan jabatan.</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-200">Database Pelatihan</span>
                                    <i class="fas fa-graduation-cap text-slate-300"></i>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Kelola jenis pelatihan, penyelenggara, dan sertifikat dalam satu tempat.</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 sm:col-span-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-200">Target Jam Pelajaran</span>
                                    <i class="fas fa-signal text-slate-300"></i>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Bandingkan JP target dan realisasi setiap tahun melalui dasbor interaktif.</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white p-6 text-slate-900 shadow-lg lg:col-span-2">
                        <div class="mb-6 text-center">
                            <h2 class="text-2xl font-semibold text-slate-900">Masuk ke SIDIKLAT</h2>
                            <p class="mt-2 text-sm text-slate-500">Gunakan akun terdaftar untuk mengakses data diklat.</p>
                        </div>
                        <div v-if="statusMessage" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ statusMessage }}
                        </div>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700" for="email">Email</label>
                                <div class="relative">
                                    <input id="email" v-model="form.email" type="email" autocomplete="email" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20"
                                        placeholder="nama@instansi.go.id" />
                                    <i class="fas fa-envelope text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                </div>
                                <p v-if="form.errors.email" class="text-sm text-rose-600">{{ form.errors.email }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700" for="password">Kata Sandi</label>
                                <div class="relative">
                                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required
                                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20"
                                        placeholder="Masukkan kata sandi" />
                                    <button type="button" @click="togglePassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="text-sm text-rose-600">{{ form.errors.password }}</p>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <label class="flex items-center space-x-2 text-slate-600">
                                    <input v-model="form.remember" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/40">
                                    <span>Ingat saya</span>
                                </label>
                                <button v-if="allowReset" type="button"
                                    class="font-medium text-slate-900 hover:text-slate-700">Lupa kata sandi?</button>
                            </div>

                            <button type="submit" :disabled="form.processing"
                                class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-70">
                                Masuk
                            </button>
                        </form>
                        <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ currentYear }} Pemerintah Kabupaten. Semua hak dilindungi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    canResetPassword: { type: Boolean, default: false },
    status: { type: String, default: '' },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const currentYear = computed(() => new Date().getFullYear());
const statusMessage = computed(() => props.status);
const allowReset = computed(() => props.canResetPassword);

function submit() {
    form.post(route('login.store'));
}

function togglePassword() {
    showPassword.value = !showPassword.value;
}
</script>
