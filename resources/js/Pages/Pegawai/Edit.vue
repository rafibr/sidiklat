<template>
    <AppLayout>
        <div class="space-y-6 pb-12">
            <nav class="text-xs text-slate-500">
                <Link :href="route('pegawai.index')" class="hover:text-indigo-600">Data pegawai</Link>
                <span class="mx-2">/</span>
                <span class="text-slate-700">Edit {{ pegawai.nama_lengkap }}</span>
            </nav>

            <section class="rounded-2xl border border-slate-200/70 bg-white px-6 py-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-2">
                    <h1 class="text-xl font-semibold text-slate-900">Perbarui data pegawai</h1>
                    <p class="text-sm text-slate-500">Sesuaikan data dan target JP untuk memastikan monitoring berjalan akurat.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="space-y-5">
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Informasi dasar</h2>

                            <FormField label="Nama lengkap" :error="errors.nama_lengkap" required>
                                <input id="nama_lengkap" v-model="form.nama_lengkap" type="text" class="form-input" required />
                            </FormField>

                            <FormField label="NIP" :error="errors.nip">
                                <input id="nip" v-model="form.nip" type="text" class="form-input" />
                                <p class="mt-1 text-xs text-slate-400">Opsional. Kosongkan jika belum tersedia.</p>
                            </FormField>

                            <FormField label="Pangkat / golongan" :error="errors.pangkat_golongan">
                                <input id="pangkat_golongan" v-model="form.pangkat_golongan" type="text" class="form-input" />
                            </FormField>

                            <FormField label="Jabatan" :error="errors.jabatan">
                                <input id="jabatan" v-model="form.jabatan" type="text" class="form-input" />
                            </FormField>

                            <FormField label="Unit kerja" :error="errors.unit_kerja">
                                <input id="unit_kerja" v-model="form.unit_kerja" type="text" class="form-input" />
                            </FormField>

                            <FormField label="Status" :error="errors.status" required>
                                <select id="status" v-model="form.status" class="form-input" required>
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak_aktif">Tidak aktif</option>
                                    <option value="pensiun">Pensiun</option>
                                </select>
                            </FormField>
                        </div>

                        <div class="space-y-5">
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Kontak & target</h2>

                            <FormField label="Email" :error="errors.email">
                                <input id="email" v-model="form.email" type="email" class="form-input" />
                            </FormField>

                            <FormField label="Nomor telepon" :error="errors.telepon">
                                <input id="telepon" v-model="form.telepon" type="tel" class="form-input" />
                            </FormField>

                            <FormField label="Tanggal pengangkatan" :error="errors.tanggal_pengangkatan">
                                <input id="tanggal_pengangkatan" v-model="form.tanggal_pengangkatan" type="date" class="form-input" />
                            </FormField>

                            <FormField label="Target JP tahunan" :error="errors.jp_target">
                                <input id="jp_target" v-model.number="form.jp_target" type="number" min="0" class="form-input" />
                                <p class="mt-1 text-xs text-slate-400">Target default instansi: {{ jpDefault }} JP.</p>
                            </FormField>

                            <div class="rounded-2xl border border-slate-200/70 bg-slate-50 px-4 py-4">
                                <p class="text-sm font-semibold text-slate-700">Progress saat ini</p>
                                <div class="mt-3 space-y-2 text-xs text-slate-500">
                                    <div class="flex items-center justify-between">
                                        <span>JP tercapai</span>
                                        <span class="font-semibold text-slate-800">{{ pegawai.jp_tercapai || 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>JP target</span>
                                        <span class="font-semibold text-slate-800">{{ form.jp_target || jpDefault }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-white">
                                        <div class="h-full rounded-full bg-indigo-600" :style="{ width: Math.min(100, calculateProgress()) + '%' }"></div>
                                    </div>
                                    <div class="text-right font-medium text-slate-600">{{ Math.round(calculateProgress()) }}% tercapai</div>
                                </div>
                            </div>

                            <FormField label="Catatan tambahan" :error="errors.keterangan">
                                <textarea id="keterangan" v-model="form.keterangan" rows="3" class="form-input"></textarea>
                            </FormField>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-end">
                        <Link :href="route('pegawai.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200/70 px-5 py-2 text-sm font-medium text-slate-600 hover:border-slate-400">Kembali</Link>
                        <Link :href="route('pegawai.show', pegawai.id)"
                            class="inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-2 text-sm font-medium text-indigo-600 hover:border-indigo-300">Lihat detail</Link>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-70">
                            <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import FormField from '@/Pages/Pegawai/Partials/FormField.vue';

export default {
    components: { AppLayout, Link, FormField },
    props: {
        pegawai: { type: Object, required: true },
        errors: { type: Object, default: () => ({}) },
        jpDefault: { type: Number, default: 20 },
    },
    setup(props) {
        const form = useForm({
            nama_lengkap: props.pegawai.nama_lengkap || '',
            nip: props.pegawai.nip || '',
            pangkat_golongan: props.pegawai.pangkat_golongan || '',
            jabatan: props.pegawai.jabatan || '',
            unit_kerja: props.pegawai.unit_kerja || '',
            status: props.pegawai.status || 'aktif',
            email: props.pegawai.email || '',
            telepon: props.pegawai.telepon || '',
            tanggal_pengangkatan: props.pegawai.tanggal_pengangkatan || '',
            jp_target: props.pegawai.jp_target || props.jpDefault,
            keterangan: props.pegawai.keterangan || '',
        });

        const submit = () => {
            form.put(route('pegawai.update', props.pegawai.id));
        };

        const calculateProgress = () => {
            const achieved = props.pegawai.jp_tercapai || 0;
            const target = form.jp_target || props.jpDefault || 1;
            return target > 0 ? (achieved / target) * 100 : 0;
        };

        return { form, submit, calculateProgress };
    },
};
</script>
