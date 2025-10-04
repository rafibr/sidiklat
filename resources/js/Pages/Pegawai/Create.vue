<template>
    <AppLayout>
        <div class="space-y-6 pb-12">
            <nav class="text-xs text-slate-500">
                <Link :href="route('pegawai.index')" class="hover:text-indigo-600">Data pegawai</Link>
                <span class="mx-2">/</span>
                <span class="text-slate-700">Tambah pegawai</span>
            </nav>

            <section class="rounded-2xl border border-slate-200/70 bg-white px-6 py-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-2">
                    <h1 class="text-xl font-semibold text-slate-900">Tambah pegawai baru</h1>
                    <p class="text-sm text-slate-500">Lengkapi data pegawai untuk memulai pemantauan progres pelatihan.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="space-y-5">
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Informasi dasar</h2>

                            <FormField label="Nama lengkap" :error="errors.nama_lengkap" required>
                                <input id="nama_lengkap" v-model="form.nama_lengkap" type="text" autocomplete="name" required
                                    class="form-input" placeholder="Masukkan nama sesuai identitas" />
                            </FormField>

                            <FormField label="NIP" :error="errors.nip">
                                <input id="nip" v-model="form.nip" type="text" class="form-input" placeholder="Opsional" />
                                <p class="mt-1 text-xs text-slate-400">Kosongkan jika belum tersedia.</p>
                            </FormField>

                            <FormField label="Pangkat / golongan" :error="errors.pangkat_golongan">
                                <input id="pangkat_golongan" v-model="form.pangkat_golongan" type="text" class="form-input"
                                    placeholder="Contoh: Penata, III/c" />
                            </FormField>

                            <FormField label="Jabatan" :error="errors.jabatan">
                                <input id="jabatan" v-model="form.jabatan" type="text" class="form-input" placeholder="Contoh: Staf Keuangan" />
                            </FormField>

                            <FormField label="Unit kerja" :error="errors.unit_kerja">
                                <input id="unit_kerja" v-model="form.unit_kerja" type="text" class="form-input" placeholder="Contoh: Bagian Umum" />
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
                                <input id="email" v-model="form.email" type="email" autocomplete="email" class="form-input" placeholder="nama@instansi.go.id" />
                            </FormField>

                            <FormField label="Nomor telepon" :error="errors.telepon">
                                <input id="telepon" v-model="form.telepon" type="tel" class="form-input" placeholder="08xx xxxx xxxx" />
                            </FormField>

                            <FormField label="Tanggal pengangkatan" :error="errors.tanggal_pengangkatan">
                                <input id="tanggal_pengangkatan" v-model="form.tanggal_pengangkatan" type="date" class="form-input" />
                            </FormField>

                            <FormField label="Target JP tahunan" :error="errors.jp_target">
                                <input id="jp_target" v-model.number="form.jp_target" type="number" min="0" class="form-input"
                                    :placeholder="`Default ${jpDefault} JP`" />
                                <p class="mt-1 text-xs text-slate-400">Biarkan kosong untuk mengikuti target default instansi.</p>
                            </FormField>

                            <FormField label="Catatan tambahan" :error="errors.keterangan">
                                <textarea id="keterangan" v-model="form.keterangan" rows="3" class="form-input"
                                    placeholder="Catatan mengenai penugasan atau kebutuhan pelatihan"></textarea>
                            </FormField>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-end">
                        <Link :href="route('pegawai.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200/70 px-5 py-2 text-sm font-medium text-slate-600 transition-colors hover:border-slate-400">Batal</Link>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white transition-colors hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-70">
                            <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan pegawai' }}</span>
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
        jpDefault: { type: Number, default: 20 },
        errors: { type: Object, default: () => ({}) },
    },
    setup() {
        const form = useForm({
            nama_lengkap: '',
            nip: '',
            pangkat_golongan: '',
            jabatan: '',
            unit_kerja: '',
            status: 'aktif',
            email: '',
            telepon: '',
            tanggal_pengangkatan: '',
            jp_target: null,
            keterangan: '',
        });

        const submit = () => {
            form.post(route('pegawai.store'));
        };

        return { form, submit };
    },
};
</script>
