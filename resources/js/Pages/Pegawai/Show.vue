<template>
	<AppLayout>
		<div class="p-4">
			<div class="mb-4">
				<Link :href="route('progress')" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Progress
				</Link>
			</div>

			<div class="bg-white p-4 rounded-lg shadow">
				<h1 class="text-2xl font-bold mb-2">{{ pegawai.nama_lengkap }}</h1>
				<div class="text-sm text-gray-600 mb-4">
					<div><strong>NIP:</strong> {{ pegawai.nip || 'Tidak Ada' }}</div>
					<div><strong>Pangkat:</strong> {{ pegawai.pangkat_golongan }}</div>
					<div><strong>Unit:</strong> {{ pegawai.unit_kerja }}</div>
					<div><strong>JP Tercapai:</strong> {{ formatNumber(pegawai.jp_tercapai) }} / {{
						formatNumber(pegawai.jp_target) }}</div>
				</div>

				<div v-if="hasPelatihan">
					<h2 class="text-lg font-semibold mb-2">Riwayat Pelatihan</h2>

					<div v-for="(pelList, year) in pelatihansByYear" :key="year" class="mb-4">
						<h3 class="text-sm font-medium text-gray-700 mb-2">{{ year }}</h3>
						<ul class="space-y-2">
							<li v-for="pel in pelList" :key="pel.id"
								class="p-3 border rounded flex justify-between items-center">
								<div>
									<div class="font-semibold">{{ pel.nama_pelatihan }}</div>
									<div class="text-xs text-gray-500">{{ pel.tanggal_mulai }} - {{ pel.tanggal_selesai
										}} • {{ pel.penyelenggara }}</div>
								</div>
								<div class="text-sm font-medium">{{ pel.jp }} JP</div>
							</li>
						</ul>
					</div>
				</div>
				<div v-else class="text-sm text-gray-500 italic">Belum ada data pelatihan.</div>
			</div>
		</div>
	</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
	components: { AppLayout, Link },
	props: {
		pegawai: Object,
		pelatihansByYear: Object,
	},
	computed: {
		hasPelatihan() {
			return Object.keys(this.pelatihansByYear || {}).length > 0;
		}
	},
	methods: {
		formatNumber(num) {
			return new Intl.NumberFormat().format(num);
		}
	}
};
</script>
