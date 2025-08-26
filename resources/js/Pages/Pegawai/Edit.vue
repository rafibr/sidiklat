<template>
	<AppLayout>
		<div class="p-4">
			<div class="mb-6">
				<div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
					<Link :href="route('pegawai.index')" class="hover:text-blue-600">Data Pegawai</Link>
					<i class="fas fa-chevron-right text-xs"></i>
					<span>Edit Pegawai</span>
				</div>
				<h1 class="text-2xl font-bold text-gray-900">Edit Pegawai: {{ pegawai.nama_lengkap }}</h1>
			</div>

			<div class="bg-white rounded-lg shadow-sm">
				<form @submit.prevent="submit" class="p-6">
					<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
						<!-- Left Column -->
						<div class="space-y-4">
							<h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Dasar
							</h3>

							<div>
								<label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">
									Nama Lengkap <span class="text-red-500">*</span>
								</label>
								<input type="text" id="nama_lengkap" v-model="form.nama_lengkap" required
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.nama_lengkap }">
								<p v-if="errors.nama_lengkap" class="text-red-500 text-xs mt-1">{{ errors.nama_lengkap
									}}
								</p>
							</div>

							<div>
								<label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
									NIP
								</label>
								<input type="text" id="nip" v-model="form.nip"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.nip }">
								<p v-if="errors.nip" class="text-red-500 text-xs mt-1">{{ errors.nip }}</p>
								<p class="text-gray-500 text-xs mt-1">Opsional. Kosongkan jika belum ada.</p>
							</div>

							<div>
								<label for="pangkat_golongan" class="block text-sm font-medium text-gray-700 mb-2">
									Pangkat/Golongan
								</label>
								<input type="text" id="pangkat_golongan" v-model="form.pangkat_golongan"
									placeholder="Contoh: Penata, III/c"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.pangkat_golongan }">
								<p v-if="errors.pangkat_golongan" class="text-red-500 text-xs mt-1">{{
									errors.pangkat_golongan }}</p>
							</div>

							<div>
								<label for="jabatan" class="block text-sm font-medium text-gray-700 mb-2">
									Jabatan
								</label>
								<input type="text" id="jabatan" v-model="form.jabatan"
									placeholder="Contoh: Staff Keuangan"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.jabatan }">
								<p v-if="errors.jabatan" class="text-red-500 text-xs mt-1">{{ errors.jabatan }}</p>
							</div>

							<div>
								<label for="unit_kerja" class="block text-sm font-medium text-gray-700 mb-2">
									Unit Kerja
								</label>
								<input type="text" id="unit_kerja" v-model="form.unit_kerja"
									placeholder="Contoh: Bagian Keuangan"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.unit_kerja }">
								<p v-if="errors.unit_kerja" class="text-red-500 text-xs mt-1">{{ errors.unit_kerja }}
								</p>
							</div>

							<div>
								<label for="status" class="block text-sm font-medium text-gray-700 mb-2">
									Status <span class="text-red-500">*</span>
								</label>
								<select id="status" v-model="form.status" required
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.status }">
									<option value="">Pilih Status</option>
									<option value="aktif">Aktif</option>
									<option value="tidak_aktif">Tidak Aktif</option>
									<option value="pensiun">Pensiun</option>
								</select>
								<p v-if="errors.status" class="text-red-500 text-xs mt-1">{{ errors.status }}</p>
							</div>
						</div>

						<!-- Right Column -->
						<div class="space-y-4">
							<h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi
								Tambahan</h3>

							<div>
								<label for="email" class="block text-sm font-medium text-gray-700 mb-2">
									Email
								</label>
								<input type="email" id="email" v-model="form.email" placeholder="nama@example.com"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.email }">
								<p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
							</div>

							<div>
								<label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">
									Telepon
								</label>
								<input type="tel" id="telepon" v-model="form.telepon" placeholder="08xx-xxxx-xxxx"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.telepon }">
								<p v-if="errors.telepon" class="text-red-500 text-xs mt-1">{{ errors.telepon }}</p>
							</div>

							<div>
								<label for="tanggal_pengangkatan" class="block text-sm font-medium text-gray-700 mb-2">
									Tanggal Pengangkatan
								</label>
								<input type="date" id="tanggal_pengangkatan" v-model="form.tanggal_pengangkatan"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.tanggal_pengangkatan }">
								<p v-if="errors.tanggal_pengangkatan" class="text-red-500 text-xs mt-1">{{
									errors.tanggal_pengangkatan }}</p>
							</div>

							<div>
								<label for="jp_target" class="block text-sm font-medium text-gray-700 mb-2">
									Target JP Tahunan
								</label>
								<input type="number" id="jp_target" v-model.number="form.jp_target" min="0" max="1000"
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.jp_target }">
								<p v-if="errors.jp_target" class="text-red-500 text-xs mt-1">{{ errors.jp_target }}</p>
								<p class="text-gray-500 text-xs mt-1">
									Target JP yang harus dicapai dalam satu tahun
								</p>
							</div>

							<div class="bg-gray-50 p-4 rounded-lg">
								<h4 class="text-sm font-medium text-gray-700 mb-2">Progress JP</h4>
								<div class="text-sm text-gray-600">
									<div class="flex justify-between items-center mb-2">
										<span>JP Tercapai:</span>
										<span class="font-medium">{{ pegawai.jp_tercapai || 0 }}</span>
									</div>
									<div class="w-full bg-gray-200 rounded-full h-2">
										<div class="h-2 rounded-full bg-blue-500 transition-all duration-300"
											:style="{ width: Math.min(100, calculateProgress()) + '%' }"></div>
									</div>
									<div class="text-right text-xs text-gray-500 mt-1">
										{{ Math.round(calculateProgress()) }}% tercapai
									</div>
								</div>
							</div>

							<div>
								<label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
									Keterangan
								</label>
								<textarea id="keterangan" v-model="form.keterangan" rows="3"
									placeholder="Catatan atau keterangan tambahan..."
									class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
									:class="{ 'border-red-300': errors.keterangan }"></textarea>
								<p v-if="errors.keterangan" class="text-red-500 text-xs mt-1">{{ errors.keterangan }}
								</p>
							</div>
						</div>
					</div>

					<!-- Form Actions -->
					<div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
						<Link :href="route('pegawai.index')"
							class="px-6 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-200">
						Batal
						</Link>
						<Link :href="route('pegawai.show', pegawai.id)"
							class="px-6 py-2 text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition duration-200">
						Lihat Detail
						</Link>
						<button type="submit" :disabled="form.processing"
							class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition duration-200 disabled:opacity-50">
							<i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
							{{ form.processing ? 'Menyimpan...' : 'Update Pegawai' }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default {
	components: { AppLayout, Link },
	props: {
		pegawai: Object,
		errors: {
			type: Object,
			default: () => ({})
		}
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
			jp_target: props.pegawai.jp_target || null,
			keterangan: props.pegawai.keterangan || ''
		});

		const submit = () => {
			form.put(route('pegawai.update', props.pegawai.id), {
				onSuccess: () => {
					// Redirect handled by controller
				}
			});
		};

		const calculateProgress = () => {
			const target = form.jp_target || 0;
			const achieved = props.pegawai.jp_tercapai || 0;
			return target > 0 ? (achieved / target) * 100 : 0;
		};

		return {
			form,
			submit,
			calculateProgress
		};
	}
};
</script>
