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

function submit() {
    form.post(route('login.store'));
}

function togglePassword() {
    showPassword.value = !showPassword.value;
}
</script>

<template>
    <div class="workspace min-h-screen">
        <Head title="Masuk" />
        <header class="workspace-header">
            <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-5 sm:px-6 lg:px-8">
                <span class="brand-mark"><i aria-hidden="true" class="fas fa-graduation-cap"></i></span>
                <div><p class="text-lg font-bold tracking-wide">SIDIKLAT</p><p class="text-xs text-slate-600">Inspektorat Kota Banjarbaru</p></div>
            </div>
        </header>
        <main class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:px-6 sm:py-12 lg:grid-cols-2 lg:items-center lg:gap-20 lg:py-20">
            <section>
                <p class="mb-4 text-sm font-semibold text-sky-700">Sistem Informasi Diklat ASN</p>
                <h1 class="max-w-lg text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Pantau pengembangan kompetensi pegawai.</h1>
                <p class="mt-5 max-w-lg text-base leading-relaxed text-slate-600">Kelola data pelatihan dan sertifikat, lalu pantau pencapaian Jam Pelajaran (JP) tahunan dalam satu tempat.</p>
                <ul class="mt-8 space-y-4 border-t border-slate-200 pt-6 text-sm text-slate-700">
                    <li class="flex items-center gap-3"><i aria-hidden="true" class="fas fa-users w-5 text-sky-700"></i>Data pegawai, unit kerja, dan jabatan</li>
                    <li class="flex items-center gap-3"><i aria-hidden="true" class="fas fa-graduation-cap w-5 text-sky-700"></i>Riwayat pelatihan dan sertifikat</li>
                    <li class="flex items-center gap-3"><i aria-hidden="true" class="fas fa-chart-line w-5 text-sky-700"></i>Target dan realisasi JP per tahun</li>
                </ul>
                <a href="/" class="mt-7 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-sky-700 hover:underline"><i aria-hidden="true" class="fas fa-arrow-left"></i>Lihat dasbor publik</a>
            </section>
            <section class="workspace-panel p-6 sm:p-8">
                <h2 class="text-2xl font-bold">Masuk ke SIDIKLAT</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Gunakan akun terdaftar untuk mengelola data diklat.</p>
                <div v-if="statusMessage" role="status" class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ statusMessage }}</div>
                <form @submit.prevent="submit" class="mt-7 space-y-5">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="email">Email</label>
                        <input id="email" v-model="form.email" type="email" autocomplete="email" required class="form-input"
                            :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'email-error' : undefined" placeholder="nama@instansi.go.id" />
                        <p v-if="form.errors.email" id="email-error" role="alert" class="mt-2 text-sm text-rose-700">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="password">Kata sandi</label>
                        <div class="relative">
                            <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required
                                class="form-input pr-12" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? 'password-error' : undefined" placeholder="Masukkan kata sandi" />
                            <button type="button" @click="togglePassword" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-lg text-slate-600 hover:text-sky-700"
                                :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" :aria-pressed="showPassword">
                                <i aria-hidden="true" :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <p v-if="form.errors.password" id="password-error" role="alert" class="mt-2 text-sm text-rose-700">{{ form.errors.password }}</p>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                        <label class="flex min-h-11 items-center gap-2 text-slate-600"><input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-slate-400 text-sky-700 focus:ring-sky-700" />Ingat saya</label>
                    </div>
                    <button type="submit" :disabled="form.processing" class="workspace-button-primary w-full disabled:cursor-not-allowed disabled:opacity-60">
                        <i aria-hidden="true" class="fas fa-arrow-right-to-bracket"></i>{{ form.processing ? 'Memproses…' : 'Masuk' }}
                    </button>
                </form>
                <p class="mt-7 border-t border-slate-200 pt-5 text-center text-xs text-slate-600">&copy; {{ currentYear }} SIDIKLAT · Inspektorat Kota Banjarbaru</p>
            </section>
        </main>
    </div>
</template>
