<template>
	<AppLayout>
		<div class="p-6">
			<div class="flex items-center mb-6">
				<Link :href="route('pelatihan.index')" class="text-blue-600 hover:text-blue-800 mr-4">
				<i class="fas fa-arrow-left mr-2"></i>Kembali
				</Link>
				<h2 class="text-2xl font-bold text-gray-800">Tambah Data Pelatihan</h2>
			</div>

			<div class="max-w-4xl bg-white rounded-lg shadow-lg p-6">
				<form @submit.prevent="submit" enctype="multipart/form-data">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<!-- Pegawai -->
						<div class="md:col-span-2">
							<label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-2">
								Pegawai <span class="text-red-500">*</span>
							</label>
							<select v-model="form.pegawai_id" id="pegawai_id" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.pegawai_id }">
								<option value="">Pilih Pegawai</option>
								<option v-for="pegawai in pegawais" :key="pegawai.id" :value="pegawai.id">
									{{ pegawai.nama_lengkap }} - {{ pegawai.nip || 'Tidak Ada NIP' }}
								</option>
							</select>
							<p v-if="form.errors.pegawai_id" class="mt-1 text-sm text-red-600">{{ form.errors.pegawai_id
								}}</p>
						</div>

						<!-- Nama Pelatihan -->
						<div class="md:col-span-2">
							<label for="nama_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">
								Nama Pelatihan <span class="text-red-500">*</span>
							</label>
							<input type="text" v-model="form.nama_pelatihan" id="nama_pelatihan" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.nama_pelatihan }" />
							<p v-if="form.errors.nama_pelatihan" class="mt-1 text-sm text-red-600">{{
								form.errors.nama_pelatihan }}</p>
						</div>

						<!-- Jenis Pelatihan -->
						<div>
							<label for="jenis_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">
								Jenis Pelatihan <span class="text-red-500">*</span>
							</label>
							<select v-model="form.jenis_pelatihan_id" id="jenis_pelatihan" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.jenis_pelatihan_id }">
								<option value="">Pilih Jenis</option>
								<option v-for="jenis in jenisPelatihan" :key="jenis.id" :value="jenis.id">
									{{ jenis.nama }}
								</option>
							</select>
							<p v-if="form.errors.jenis_pelatihan_id" class="mt-1 text-sm text-red-600">{{
								form.errors.jenis_pelatihan_id }}</p>
						</div>

						<!-- Penyelenggara -->
						<div>
							<label for="penyelenggara" class="block text-sm font-medium text-gray-700 mb-2">
								Penyelenggara <span class="text-red-500">*</span>
							</label>
							<input type="text" v-model="form.penyelenggara" id="penyelenggara" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.penyelenggara }" />
							<p v-if="form.errors.penyelenggara" class="mt-1 text-sm text-red-600">{{
								form.errors.penyelenggara }}</p>
						</div>

						<!-- Tempat -->
						<div>
							<label for="tempat" class="block text-sm font-medium text-gray-700 mb-2">
								Tempat Pelaksanaan
							</label>
							<input type="text" v-model="form.tempat" id="tempat"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.tempat }" />
							<p v-if="form.errors.tempat" class="mt-1 text-sm text-red-600">{{ form.errors.tempat }}</p>
						</div>

						<!-- Tanggal Mulai -->
						<div>
							<label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">
								Tanggal Mulai <span class="text-red-500">*</span>
							</label>
							<input type="text" v-model="form.tanggal_mulai" id="tanggal_mulai" required
								placeholder="Contoh: 15 Oktober 2024"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.tanggal_mulai }" />
							<p v-if="form.errors.tanggal_mulai" class="mt-1 text-sm text-red-600">{{
								form.errors.tanggal_mulai }}</p>
						</div>

						<!-- Tanggal Selesai -->
						<div>
							<label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
								Tanggal Selesai <span class="text-red-500">*</span>
							</label>
							<input type="text" v-model="form.tanggal_selesai" id="tanggal_selesai" required
								placeholder="Contoh: 20 Oktober 2024"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.tanggal_selesai }" />
							<p v-if="form.errors.tanggal_selesai" class="mt-1 text-sm text-red-600">{{
								form.errors.tanggal_selesai }}</p>
						</div>

						<!-- JP -->
						<div>
							<label for="jp" class="block text-sm font-medium text-gray-700 mb-2">
								Jam Pelajaran (JP) <span class="text-red-500">*</span>
							</label>
							<input type="number" v-model="form.jp" id="jp" required min="1"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.jp }" />
							<p v-if="form.errors.jp" class="mt-1 text-sm text-red-600">{{ form.errors.jp }}</p>
						</div>

						<!-- Status -->
						<div>
							<label for="status" class="block text-sm font-medium text-gray-700 mb-2">
								Status <span class="text-red-500">*</span>
							</label>
							<select v-model="form.status" id="status" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.status }">
								<option value="">Pilih Status</option>
								<option value="selesai">Selesai</option>
								<option value="sedang_berjalan">Sedang Berjalan</option>
								<option value="akan_datang">Akan Datang</option>
							</select>
							<p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
						</div>

						<!-- Deskripsi -->
						<div class="md:col-span-2">
							<label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
								Deskripsi
							</label>
							<textarea v-model="form.deskripsi" id="deskripsi" rows="4"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.deskripsi }"></textarea>
							<p v-if="form.errors.deskripsi" class="mt-1 text-sm text-red-600">{{ form.errors.deskripsi
								}}</p>
						</div>

						<!-- Sertifikat -->
						<div class="md:col-span-2">
							<label for="sertifikat" class="block text-sm font-medium text-gray-700 mb-2">
								Sertifikat (PDF, JPG, PNG - Max 2MB)
							</label>
							<input type="file" @change="handleFileUpload" id="sertifikat" accept=".pdf,.jpg,.jpeg,.png"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
								:class="{ 'border-red-300': form.errors.sertifikat }" />
							<p v-if="form.errors.sertifikat" class="mt-1 text-sm text-red-600">{{ form.errors.sertifikat
								}}</p>
						</div>
					</div>

					<!-- Submit Buttons -->
					<div class="flex justify-end gap-4 mt-8">
						<Link :href="route('pelatihan.index')"
							class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
						Batal
						</Link>
						<button type="submit" :disabled="form.processing"
							class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
							<i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
							{{ form.processing ? 'Menyimpan...' : 'Simpan' }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</AppLayout>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
	components: {
		Link,
		AppLayout,
	},
	props: {
		pegawais: Array,
		jenisPelatihan: Array,
	},
	setup() {
		const form = useForm({
			pegawai_id: '',
			nama_pelatihan: '',
			jenis_pelatihan_id: '',
			penyelenggara: '',
			tempat: '',
			tanggal_mulai: '',
			tanggal_selesai: '',
			jp: '',
			status: '',
			deskripsi: '',
			sertifikat: null,
		});

		return { form };
	},
	methods: {
		handleFileUpload(event) {
			this.form.sertifikat = event.target.files[0];
		},
		submit() {
			this.form.post(route('pelatihan.store'));
		},
	},
};
</script>
