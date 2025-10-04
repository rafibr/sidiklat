<template>
    <div
        class="relative min-h-screen overflow-hidden bg-gradient-to-br from-indigo-50 via-sky-50 to-emerald-50 text-slate-900">

        <Head title="Masuk" />
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div
                class="absolute inset-x-0 top-[-320px] h-[520px] bg-gradient-to-b from-indigo-200/40 via-sky-100/50 to-transparent">
            </div>
            <div class="absolute left-[-180px] bottom-[120px] h-96 w-96 rounded-full bg-indigo-200/30 blur-3xl"></div>
            <div class="absolute right-[-120px] top-[160px] h-72 w-72 rounded-full bg-emerald-200/30 blur-3xl"></div>
        </div>

        <div class="relative flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-5xl">
                <div
                    class="grid gap-8 rounded-3xl border border-indigo-200/50 bg-white/80 p-8 shadow-2xl backdrop-blur-xl lg:grid-cols-5 lg:p-12">
                    <div class="space-y-6 lg:col-span-3">
                        <div>
                            <p
                                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-indigo-500 to-sky-500 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-lg">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20">
                                    <i class="fas fa-sparkles"></i>
                                </span>
                                SIDIKLAT Platform
                            </p>
                            <h1
                                class="mt-4 text-3xl font-bold tracking-tight bg-gradient-to-r from-indigo-600 to-emerald-600 bg-clip-text text-transparent sm:text-4xl">
                                Satu portal
                                untuk memantau kinerja diklat ASN</h1>
                            <p class="mt-4 text-sm text-slate-700">Terintegrasi dengan data pegawai, jenis pelatihan,
                                serta target Jam Pelajaran (JP) tahunan untuk memastikan pengembangan kompetensi
                                berjalan efektif.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div
                                class="rounded-2xl border border-indigo-200 bg-white/60 p-4 shadow-sm backdrop-blur-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-slate-800">Pemantauan Pegawai</span>
                                    <i class="fas fa-users text-indigo-500"></i>
                                </div>
                                <p class="mt-2 text-xs text-slate-600">Cek progres JP pegawai secara real-time beserta
                                    unit kerja dan jabatan.</p>
                            </div>
                            <div class="rounded-2xl border border-sky-200 bg-white/60 p-4 shadow-sm backdrop-blur-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-slate-800">Database Pelatihan</span>
                                    <i class="fas fa-graduation-cap text-sky-500"></i>
                                </div>
                                <p class="mt-2 text-xs text-slate-600">Kelola jenis pelatihan, penyelenggara, dan
                                    sertifikat dalam satu tempat.</p>
                            </div>
                            <div
                                class="rounded-2xl border border-emerald-200 bg-white/60 p-4 shadow-sm backdrop-blur-sm sm:col-span-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-slate-800">Target Jam Pelajaran</span>
                                    <i class="fas fa-signal text-emerald-500"></i>
                                </div>
                                <p class="mt-2 text-xs text-slate-600">Bandingkan JP target dan realisasi setiap tahun
                                    melalui dasbor interaktif.</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-2xl bg-white p-8 text-slate-900 shadow-2xl border border-indigo-100 lg:col-span-2">
                        <div class="mb-6 text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-sky-500 text-white shadow-lg">
                                <i class="fas fa-sparkles text-2xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-900">Masuk ke SIDIKLAT</h2>
                            <p class="mt-2 text-sm text-slate-600">Gunakan akun terdaftar untuk mengakses data diklat.
                            </p>
                        </div>
                        <div v-if="statusMessage"
                            class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ statusMessage }}
                        </div>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700" for="email">Email</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-envelope text-slate-400"></i>
                                    </div>
                                    <input id="email" v-model="form.email" type="email" autocomplete="email" required
                                        class="w-full rounded-xl border border-slate-200 pl-11 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                        placeholder="nama@instansi.go.id" />
                                </div>
                                <p v-if="form.errors.email" class="text-sm text-rose-600">{{ form.errors.email }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700" for="password">Kata
                                    Sandi</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-lock text-slate-400"></i>
                                    </div>
                                    <input id="password" v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'" autocomplete="current-password"
                                        required
                                        class="w-full rounded-xl border border-slate-200 pl-11 pr-12 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                        placeholder="Masukkan kata sandi" />
                                    <button type="button" @click="togglePassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="text-sm text-rose-600">{{ form.errors.password }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <label class="flex items-center space-x-2 text-slate-600">
                                    <input v-model="form.remember" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>Ingat saya</span>
                                </label>
                                <button v-if="allowReset" type="button"
                                    class="font-medium text-indigo-600 hover:text-indigo-500">Lupa kata sandi?</button>
                            </div>

                            <button type="submit" :disabled="form.processing"
                                class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-sky-600 px-4 py-3.5 text-sm font-semibold text-white transition hover:from-indigo-700 hover:to-sky-700 disabled:cursor-not-allowed disabled:opacity-70 shadow-lg shadow-indigo-500/40 hover:shadow-xl hover:shadow-indigo-500/50">
                                <span class="flex items-center justify-center gap-2">
                                    <i class="fas fa-arrow-right-to-bracket"></i>
                                    Masuk
                                </span>
                            </button>
                        </form>
                        <p class="mt-6 text-center text-xs text-slate-500">
                            &copy; {{ currentYear }} <span class="font-semibold">SIDIKLAT</span>. Semua hak dilindungi.
                        </p>
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
