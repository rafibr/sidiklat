<template>
	<AppLayout>
		<div class="container mx-auto px-4 py-8">
			<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
				<div class="flex items-center justify-between mb-6">
					<h1 class="text-3xl font-bold text-gray-800">Edit Pelatihan</h1>
					<Link :href="route('pelatihan.index')"
						class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
					<i class="fas fa-arrow-left mr-2"></i>Kembali
					</Link>
				</div>

				<form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
					<!-- Pegawai -->
					<div>
						<label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-2">Pegawai</label>
						<select v-model="form.pegawai_id" id="pegawai_id"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
							:class="{ 'border-rose-500': form.errors.pegawai_id }" required>
							<option value="">Pilih Pegawai</option>
							<option v-for="pegawai in pegawais" :key="pegawai.id" :value="pegawai.id">
								{{ pegawai.nama_lengkap }} - {{ pegawai.pangkat_golongan }}
							</option>
						</select>
						<p v-if="form.errors.pegawai_id" class="mt-1 text-sm text-rose-600">{{ form.errors.pegawai_id }}
						</p>
					</div>

					<!-- Nama Pelatihan -->
					<div>
						<label for="nama_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">Nama
							Pelatihan</label>
						<input type="text" v-model="form.nama_pelatihan" id="nama_pelatihan"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
							:class="{ 'border-rose-500': form.errors.nama_pelatihan }" required />
						<p v-if="form.errors.nama_pelatihan" class="mt-1 text-sm text-rose-600">{{
							form.errors.nama_pelatihan }}</p>
					</div>

					<!-- Jenis Pelatihan -->
					<div>
						<label for="jenis_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">Jenis
							Pelatihan</label>
						<select v-model="form.jenis_pelatihan_id" id="jenis_pelatihan"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
							:class="{ 'border-rose-500': form.errors.jenis_pelatihan_id }" required>
							<option value="">Pilih Jenis Pelatihan</option>
							<option v-for="jenis in jenisPelatihan" :key="jenis.id" :value="jenis.id">{{ jenis.nama }}
							</option>
						</select>
						<p v-if="form.errors.jenis_pelatihan_id" class="mt-1 text-sm text-rose-600">{{
							form.errors.jenis_pelatihan_id }}</p>
					</div>

					<!-- Penyelenggara -->
					<div>
						<label for="penyelenggara"
							class="block text-sm font-medium text-gray-700 mb-2">Penyelenggara</label>
						<input type="text" v-model="form.penyelenggara" id="penyelenggara"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
							:class="{ 'border-rose-500': form.errors.penyelenggara }" required />
						<p v-if="form.errors.penyelenggara" class="mt-1 text-sm text-rose-600">{{
							form.errors.penyelenggara }}</p>
					</div>

					<!-- Tempat -->
					<div>
						<label for="tempat" class="block text-sm font-medium text-gray-700 mb-2">Tempat</label>
						<input type="text" v-model="form.tempat" id="tempat"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
							:class="{ 'border-rose-500': form.errors.tempat }" />
						<p v-if="form.errors.tempat" class="mt-1 text-sm text-rose-600">{{ form.errors.tempat }}</p>
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<!-- Tanggal Mulai -->
						<div>
							<label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
								Mulai</label>
							<input type="text" v-model="form.tanggal_mulai" id="tanggal_mulai"
								placeholder="Contoh: 15 Oktober 2024"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
								:class="{ 'border-rose-500': form.errors.tanggal_mulai }" required />
							<p v-if="form.errors.tanggal_mulai" class="mt-1 text-sm text-rose-600">{{
								form.errors.tanggal_mulai }}</p>
						</div>

						<!-- Tanggal Selesai -->
						<div>
							<label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
								Selesai</label>
							<input type="text" v-model="form.tanggal_selesai" id="tanggal_selesai"
								placeholder="Contoh: 20 Oktober 2024"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
								:class="{ 'border-rose-500': form.errors.tanggal_selesai }" required />
							<p v-if="form.errors.tanggal_selesai" class="mt-1 text-sm text-rose-600">{{
								form.errors.tanggal_selesai }}</p>
						</div>
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<!-- JP -->
						<div>
							<label for="jp" class="block text-sm font-medium text-gray-700 mb-2">Jam Pelajaran
								(JP)</label>
							<input type="number" v-model="form.jp" id="jp" min="1"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
								:class="{ 'border-rose-500': form.errors.jp }" required />
							<p v-if="form.errors.jp" class="mt-1 text-sm text-rose-600">{{ form.errors.jp }}</p>
						</div>

						<!-- Status -->
						<div>
							<label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
							<select v-model="form.status" id="status"
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
								:class="{ 'border-rose-500': form.errors.status }" required>
								<option value="">Pilih Status</option>
								<option value="selesai">Selesai</option>
								<option value="sedang_berjalan">Sedang Berjalan</option>
								<option value="akan_datang">Akan Datang</option>
							</select>
							<p v-if="form.errors.status" class="mt-1 text-sm text-rose-600">{{ form.errors.status }}</p>
						</div>
					</div>

					<!-- Deskripsi -->
					<div>
						<label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
						<textarea v-model="form.deskripsi" id="deskripsi" rows="4"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
							:class="{ 'border-rose-500': form.errors.deskripsi }"></textarea>
						<p v-if="form.errors.deskripsi" class="mt-1 text-sm text-rose-600">{{ form.errors.deskripsi }}
						</p>
					</div>

					<!-- Sertifikat -->
					<div>
						<label for="sertifikat" class="block text-sm font-medium text-gray-700 mb-2">Sertifikat</label>
						<div v-if="pelatihan.sertifikat_path" class="mb-2">
							<p class="text-sm text-gray-600">Sertifikat saat ini:</p>
							<a :href="`/storage/${pelatihan.sertifikat_path}`" target="_blank"
								class="text-indigo-600 hover:text-indigo-700">
								<i class="fas fa-file-pdf mr-1"></i>Lihat sertifikat
							</a>
						</div>
						<input type="file" @change="handleFileUpload" id="sertifikat" accept=".pdf,.jpg,.jpeg,.png"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
							:class="{ 'border-rose-500': form.errors.sertifikat }" />
						<p class="mt-1 text-xs text-gray-500">Format: PDF, JPG, PNG. Maksimal 2MB. Kosongkan jika tidak
							ingin mengubah.</p>
						<p v-if="form.errors.sertifikat" class="mt-1 text-sm text-rose-600">{{ form.errors.sertifikat }}
						</p>
					</div>

					<!-- Submit Button -->
					<div class="flex justify-end">
						<button type="submit" :disabled="form.processing"
							class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition duration-200 disabled:opacity-50">
							<i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
							{{ form.processing ? 'Menyimpan...' : 'Perbarui Pelatihan' }}
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
		pelatihan: Object,
		pegawais: Array,
		jenisPelatihan: Array,
	},
	setup(props) {
		const form = useForm({
			pegawai_id: props.pelatihan.pegawai_id,
			nama_pelatihan: props.pelatihan.nama_pelatihan,
			jenis_pelatihan_id: props.pelatihan.jenis_pelatihan_id || '',
			penyelenggara: props.pelatihan.penyelenggara,
			tempat: props.pelatihan.tempat,
			tanggal_mulai: props.pelatihan.tanggal_mulai,
			tanggal_selesai: props.pelatihan.tanggal_selesai,
			jp: props.pelatihan.jp,
			status: props.pelatihan.status,
			deskripsi: props.pelatihan.deskripsi,
			sertifikat: null,
		});

		return { form };
	},
	methods: {
		handleFileUpload(event) {
			this.form.sertifikat = event.target.files[0];
		},
		submit() {
			this.form.put(route('pelatihan.update', this.pelatihan.id));
		},
	},
};
</script>
