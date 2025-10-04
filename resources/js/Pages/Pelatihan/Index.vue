<template>
	<AppLayout>
		<div class="p-3 sm:p-4 md:p-6 animate-slide-up">
			<div
				class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3 sm:gap-0 animate-fade-scale">
				<h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Data Pelatihan</h2>
				<div class="flex items-center gap-3">

					<button @click="startAddNew" v-if="!isAddingNew"
						class="w-full sm:w-auto bg-indigo-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-indigo-700 transition-all hover:shadow-lg hover:scale-105 text-center text-sm">
						<i class="fas fa-plus mr-1 sm:mr-2"></i>Tambah Pelatihan
					</button>
				</div>
			</div>

			<!-- Filters -->
			<form @submit.prevent="submitFilter"
				class="bg-gray-50 p-3 sm:p-4 rounded-lg mb-4 md:mb-6 animate-slide-left delay-100">
				<div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
						<input type="text" v-model="filters.search" placeholder="Nama pelatihan atau pegawai..."
							class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" />
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelatihan</label>
						<v-combobox v-model="filters.jenis" :items="jenisPelatihanItems"
							@update:model-value="submitFilter" placeholder="Pilih atau ketik jenis..." clearable
							density="compact" variant="outlined" hide-details class="vuetify-input"></v-combobox>
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
						<select v-model="filters.year" @change="submitFilter"
							class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
							<option value="">Semua Tahun</option>
							<option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
						</select>
					</div>

					<div class="flex items-end gap-2">
						<!-- Filter button removed: search & jenis auto-apply -->
						<div class="flex-1">
							<button type="button" @click="resetFilter"
								class="w-full bg-gray-500 text-white px-3 sm:px-4 py-2 text-sm rounded-md hover:bg-gray-600 transition-colors text-center">
								Reset
							</button>
						</div>
					</div>
				</div>
			</form>

			<div v-if="$page.props.flash && $page.props.flash.success"
				class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-4">
				{{ $page.props.flash.success }}
			</div>

			<!-- Table -->
			<div
				class="bg-white rounded-lg shadow-lg overflow-hidden border-compact card-compact animate-slide-up delay-200">
				<div class="overflow-x-auto">
					<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
						<div class="flex items-center gap-4">
							<div class="text-sm text-gray-600">Total: <strong>{{ totalPelatihans }}</strong></div>
							<div class="text-sm text-gray-600" v-if="rangeText">Menampilkan <strong>{{ rangeText
							}}</strong></div>
						</div>

						<div class="flex items-center gap-3">
							<div class="flex items-center gap-2">
								<label class="text-sm text-gray-600 hidden sm:inline">Item per halaman:</label>
								<select v-model.number="filters.per_page" @change="submitFilter"
									class="w-full sm:w-auto px-3 py-2 pr-8 text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
									<option :value="10">10</option>
									<option :value="25">25</option>
									<option :value="50">50</option>
									<option :value="100">100</option>
									<option :value="500">500</option>
								</select>
							</div>

							<div class="flex items-center gap-2">
								<div v-if="editingCount > 1" class="flex items-center gap-2 mr-2">
									<button @click.prevent="saveAll"
										class="text-xs bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1 rounded">Save
										All</button>
									<button @click.prevent="cancelAll"
										class="text-xs bg-gray-300 text-gray-800 hover:bg-gray-400 px-3 py-1 rounded">Cancel
										All</button>
								</div>
								<button @click.prevent="exportData('csv')"
									class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded border">CSV</button>
								<button @click.prevent="exportData('xls')"
									class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded border">XLS</button>
								<button @click.prevent="exportData('pdf')"
									class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded border">PDF</button>
							</div>
						</div>
					</div>
					<table class="min-w-full divide-y divide-gray-200 text-xs">
						<thead class="bg-gray-50">
							<tr>
								<th
									class="px-2 py-1 sm:px-3 sm:py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Pegawai
								</th>
								<th
									class="px-2 py-1 sm:px-3 sm:py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Pelatihan
								</th>
								<th
									class="px-2 py-1 sm:px-3 sm:py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Jenis
								</th>
								<th
									class="px-2 py-1 sm:px-3 sm:py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Tanggal
								</th>
								<th
									class="px-2 py-1 sm:px-3 sm:py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									JP
								</th>
								<th
									class="px-2 py-1 sm:px-3 sm:py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Sertifikat
								</th>
								<th
									class="px-2 py-1 sm:px-3 sm:py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									Aksi
								</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200">
							<!-- Add New Row -->
							<tr v-if="isAddingNew" class="adding-row">
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap editable-cell">
									<v-combobox v-model="newRowPegawai" :items="pegawaiOptions"
										item-title="nama_lengkap" item-value="id" placeholder="Pilih Pegawai"
										density="compact" variant="outlined" hide-details clearable
										class="mini-combobox"></v-combobox>
								</td>
								<td class="px-2 py-2 sm:px-3 sm:py-2 editable-cell">
									<input v-model="newRow.nama_pelatihan"
										class="text-xs border rounded px-1 py-1 w-full" placeholder="Nama Pelatihan" />
									<input v-model="newRow.penyelenggara"
										class="text-xs border rounded px-1 py-1 w-full mt-1"
										placeholder="Penyelenggara" />
								</td>
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap editable-cell">
									<v-combobox v-model="newRowJenis" :items="jenisPelatihan" item-title="nama"
										item-value="id" placeholder="Pilih Jenis" density="compact" variant="outlined"
										hide-details clearable class="mini-combobox"></v-combobox>
								</td>
								<td class="px-2 py-2 sm:px-3 sm:py-2 editable-cell date-jp-container">
									<div class="flex gap-1">
										<div class="date-inputs flex-1">
											<input v-model="newRow.tanggal_mulai" type="date"
												class="text-xs border rounded px-1 py-1 w-full mb-1" />
											<input v-model="newRow.tanggal_selesai" type="date"
												class="text-xs border rounded px-1 py-1 w-full" />
										</div>
									</div>
								</td>
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap editable-cell">
									<input v-model="newRow.jp" type="number"
										class="text-xs border rounded px-1 py-1 w-full" placeholder="JP" />
								</td>
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap editable-cell">
									<div class="upload-area border-2 border-dashed border-gray-300 rounded p-1 text-center cursor-pointer hover:border-indigo-400"
										@dragover.prevent @drop.prevent="handleFileDrop($event, 'new')"
										@click="$refs.newFileInput.click()">
										<span v-if="!newRow.sertifikat" class="text-xs text-gray-500">
											<i class="fas fa-cloud-upload"></i> Drop/Click
										</span>
										<span v-else class="text-xs text-emerald-600">
											<i class="fas fa-file-pdf"></i> {{ newRow.sertifikat.name }}
										</span>
										<input ref="newFileInput" type="file" @change="handleFileSelect($event, 'new')"
											accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
									</div>
								</td>
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap text-xs font-medium">
									<div class="action-buttons">
										<button @click="saveNew" class="text-emerald-600 hover:text-emerald-700 p-1"
											title="Simpan">
											<i class="fas fa-save"></i>
										</button>
										<button @click="cancelAddNew" class="text-rose-600 hover:text-rose-700 p-1"
											title="Batal">
											<i class="fas fa-times"></i>
										</button>
									</div>
								</td>
							</tr>

							<!-- Empty State -->
							<tr v-if="pelatihans.data.length === 0 && !isAddingNew">
								<td colspan="7" class="px-2 py-6 sm:px-3 sm:py-6 text-center text-gray-500">
									<i class="fas fa-database text-4xl mb-4"></i>
									<p>Tidak ada data pelatihan</p>
								</td>
							</tr>

							<!-- Existing Rows -->
							<tr v-for="pelatihan in pelatihans.data" :key="pelatihan.id"
								:class="{ 'editing-row': isEditing(pelatihan) }"
								@dblclick="!isEditing(pelatihan) && startEdit(pelatihan)"
								class="hover:bg-gray-50 cursor-pointer">

								<!-- Pegawai Column -->
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap"
									:class="{ 'editable-cell': isEditing(pelatihan) }">
									<div v-if="!isEditing(pelatihan)">
										<div class="text-sm font-medium text-gray-900">{{ pelatihan.pegawai.nama_lengkap
										}}</div>
										<div class="text-xs text-gray-500">{{ pelatihan.pegawai.nip || 'Tidak Ada NIP'
										}}</div>
									</div>
									<v-combobox v-else
										:model-value="pegawaiOptions.find(p => p.id === pelatihan.pegawai_id)"
										@update:model-value="pelatihan.pegawai_id = $event ? $event.id : null"
										:items="pegawaiOptions" item-title="nama_lengkap" item-value="id"
										density="compact" variant="outlined" hide-details clearable
										class="mini-combobox">
									</v-combobox>
								</td>

								<!-- Pelatihan Column -->
								<!-- Pelatihan Column -->
								<td class="px-2 py-2 sm:px-3 sm:py-2"
									:class="{ 'editable-cell': isEditing(pelatihan) }">
									<div v-if="!isEditing(pelatihan)">
										<div class="text-xs font-medium text-gray-900">{{ pelatihan.nama_pelatihan }}
										</div>
										<div class="text-xs text-gray-500">{{ pelatihan.penyelenggara }}</div>
									</div>
									<div v-else>
										<input v-model="pelatihan.nama_pelatihan"
											class="text-xs border rounded px-1 py-1 w-full mb-1" />
										<input v-model="pelatihan.penyelenggara"
											class="text-xs border rounded px-1 py-1 w-full" />
									</div>
								</td>

								<!-- Jenis Column -->
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap"
									:class="{ 'editable-cell': isEditing(pelatihan) }">
									<span v-if="!isEditing(pelatihan)"
										class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
										:class="getJenisColor(pelatihan.jenis_pelatihan.nama)">
										{{ pelatihan.jenis_pelatihan.nama }}
									</span>
									<v-combobox v-else
										:model-value="jenisPelatihan.find(j => j.id === pelatihan.jenis_pelatihan_id)"
										@update:model-value="pelatihan.jenis_pelatihan_id = $event ? $event.id : null"
										:items="jenisPelatihan" item-title="nama" item-value="id" density="compact"
										variant="outlined" hide-details clearable class="mini-combobox">
									</v-combobox>
								</td>

								<!-- Tanggal Column -->
								<!-- Tanggal Column -->
								<td class="px-2 py-2 sm:px-3 sm:py-2 editable-cell date-jp-container"
									:class="{ 'editable-cell': isEditing(pelatihan) }">
									<div v-if="!isEditing(pelatihan)" class="text-xs text-gray-500">
										{{ formatDate(pelatihan.tanggal_mulai) }} - {{
											formatDate(pelatihan.tanggal_selesai) }}
									</div>
									<div v-else class="flex gap-1">
										<div class="date-inputs flex-1">
											<input v-model="pelatihan.tanggal_mulai" type="date"
												class="text-xs border rounded px-1 py-1 w-full mb-1" />
											<input v-model="pelatihan.tanggal_selesai" type="date"
												class="text-xs border rounded px-1 py-1 w-full" />
										</div>
									</div>
								</td> <!-- JP Column -->
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap text-xs text-gray-900 font-semibold"
									:class="{ 'editable-cell': isEditing(pelatihan) }">
									<span v-if="!isEditing(pelatihan)">{{ pelatihan.jp }}</span>
									<input v-else v-model="pelatihan.jp" type="number"
										class="text-xs border rounded px-1 py-1 w-full" />
								</td>

								<!-- Sertifikat Column -->
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap text-xs text-gray-500">
									<div v-if="!isEditing(pelatihan)">
										<div v-if="pelatihan.sertifikat_path" class="flex items-center gap-1">
											<a :href="`/storage/${pelatihan.sertifikat_path}`" target="_blank"
												rel="noopener"
												class="file-link inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700">
												<i class="fas fa-file-pdf"></i>
												<span class="file-name text-xs truncate max-w-20"
													:title="getFileName(pelatihan.sertifikat_path)">
													{{ getFileName(pelatihan.sertifikat_path) }}
												</span>
											</a>
										</div>
										<span v-else class="text-gray-400">Tidak ada</span>
									</div>
									<div v-else
										class="upload-area border-2 border-dashed border-gray-300 rounded p-1 text-center cursor-pointer hover:border-indigo-400"
										@dragover.prevent @drop.prevent="handleFileDrop($event, pelatihan.id)"
										@click="$refs[`fileInput${pelatihan.id}`][0].click()">
										<div v-if="!editingFiles[pelatihan.id]" class="text-xs text-center">
											<div v-if="pelatihan.sertifikat_path"
												class="text-emerald-600 mb-1 truncate max-w-28">
												<i class="fas fa-file-pdf mr-1"></i>
												<span class="text-sm">{{ getFileName(pelatihan.sertifikat_path)
												}}</span>
											</div>
											<div class="text-gray-500">
												<i class="fas fa-cloud-upload mr-1"></i>
												<span class="text-xs">Drop/Click untuk {{ pelatihan.sertifikat_path ?
													'ganti' : 'upload' }}</span>
											</div>
										</div>
										<span v-else class="text-xs text-emerald-600">
											<i class="fas fa-file-pdf"></i> {{ editingFiles[pelatihan.id].name }}
										</span>
										<input :ref="`fileInput${pelatihan.id}`" type="file"
											@change="handleFileSelect($event, pelatihan.id)"
											accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
									</div>
								</td>

								<!-- Action Column -->
								<td class="px-2 py-2 sm:px-3 sm:py-2 whitespace-nowrap text-xs font-medium">
									<div v-if="!isEditing(pelatihan)" class="action-buttons">
										<button @click="startEdit(pelatihan)"
											class="text-amber-600 hover:text-amber-700 p-1" title="Edit">
											<i class="fas fa-edit"></i>
										</button>
										<button @click="deleteItem(pelatihan.id)"
											class="text-rose-600 hover:text-rose-700 p-1" title="Hapus">
											<i class="fas fa-trash"></i>
										</button>
									</div>
									<div v-else class="action-buttons">
										<button @click="saveEdit(pelatihan)"
											class="text-emerald-600 hover:text-emerald-700 p-1" title="Simpan">
											<i class="fas fa-save"></i>
										</button>
										<button @click="cancelEdit(pelatihan)"
											class="text-rose-600 hover:text-rose-700 p-1" title="Batal">
											<i class="fas fa-times"></i>
										</button>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Pagination -->
			<div v-if="pelatihans.links" class="mt-6">
				<div class="flex justify-center space-x-2">
					<button v-for="link in pelatihans.links" :key="link.label" @click="changePage(link.url)"
						:disabled="!link.url" class="px-3 py-2 text-sm border rounded"
						:class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600'"
						v-html="link.label"></button>
				</div>
			</div>
		</div>
	</AppLayout>
</template>

<script>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
	components: {
		Link,
		AppLayout,
	},
	props: {
		pelatihans: Object,
		jenisPelatihan: Array,
		pegawais: Array,
		availableYears: Array,
	},
	data() {
		return {
			filters: {
				search: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('search') || '' : '',
				jenis: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('jenis') || '' : '',
				year: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('year') || '' : '',
				per_page: this.$page.props.per_page || 500,
			},
			searchTimer: null,
			editingRows: {},
			isAddingNew: false,
			isAutoSaving: false,
			newRow: {
				pegawai_id: '',
				nama_pelatihan: '',
				jenis_pelatihan_id: '',
				penyelenggara: '',
				tanggal_mulai: '',
				tanggal_selesai: '',
				jp: '',
				sertifikat: null
			},
			originalData: {},
			newRowPegawai: null,
			newRowJenis: null,
			editingFiles: {}
		};
	},
	computed: {
		jenisPelatihanItems() {
			const items = this.jenisPelatihan.map(jenis => jenis.nama);
			return ['Semua Jenis', ...items];
		},
		pegawaiOptions() {
			return this.pegawais || [];
		}
		,
		totalPelatihans() {
			return this.$page.props.total_pelatihans || (this.pelatihans && this.pelatihans.total) || 0;
		}
		,
		rangeText() {
			const p = this.pelatihans;
			if (!p || !p.meta) {
				// Inertia pagination sometimes provides pagination info directly on the object
				if (!p || typeof p.current_page === 'undefined') return null;
				const current = p.current_page || 1;
				const per = p.per_page || this.filters.per_page || 25;
				const total = p.total || this.totalPelatihans;
				const from = (current - 1) * per + 1;
				const to = Math.min(current * per, total);
				return `${from}–${to} dari ${total}`;
			}

			const current = p.meta.current_page || p.meta.current_page === 0 ? p.meta.current_page : (p.current_page || 1);
			const per = p.meta.per_page || p.per_page || this.filters.per_page || 25;
			const total = p.meta.total || p.total || this.totalPelatihans;
			const from = (current - 1) * per + 1;
			const to = Math.min(current * per, total);
			return `${from}–${to} dari ${total}`;
		}
		,
		editingCount() {
			return Object.keys(this.editingRows).length;
		}
	},
	mounted() {
		// Add click listener for auto-save when clicking outside editing row
		document.addEventListener('click', this.handleOutsideClick);
	},
	beforeUnmount() {
		// Remove event listener when component is destroyed
		document.removeEventListener('click', this.handleOutsideClick);
	},
	methods: {
		getCsrfToken() {
			const meta = document.querySelector('meta[name="csrf-token"]');
			return meta ? meta.getAttribute('content') : '';
		},

		handleOutsideClick(event) {
			// Tidak auto-save ketika klik di luar row
			// User harus mengklik tombol save secara manual
			return;
		},

		getJenisColor(jenis) {
			const colorMap = {
				'Diklat Struktural': 'bg-indigo-100 text-indigo-700',
				'Diklat Fungsional': 'bg-emerald-100 text-emerald-700',
				'Diklat Teknis': 'bg-indigo-100 text-indigo-700',
				'Workshop': 'bg-amber-100 text-amber-700',
				'Pelatihan Jarak Jauh': 'bg-indigo-100 text-indigo-700',
				'E-Learning': 'bg-emerald-100 text-emerald-700',
				'Seminar': 'bg-rose-100 text-rose-700'
			};
			return colorMap[jenis] || 'bg-gray-100 text-gray-800';
		},
		formatDate(dateStr) {
			if (!dateStr) return '-';
			try {
				const d = new Date(dateStr);
				return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(d);
			} catch (e) {
				return dateStr;
			}
		},

		submitFilter() {
			router.get(route('pelatihan.index'), this.filters, { preserveState: true });
		},
		exportData(format = 'csv') {
			// Build query from filters
			const params = new URLSearchParams();
			if (this.filters.search) params.set('search', this.filters.search);
			if (this.filters.jenis) params.set('jenis', this.filters.jenis);
			if (this.filters.year) params.set('year', this.filters.year);
			if (this.filters.per_page) params.set('per_page', this.filters.per_page);
			params.set('format', format);

			const url = route('pelatihan.export') + '?' + params.toString();
			// open in new tab to trigger download
			window.open(url, '_blank');
		},
		resetFilter() {
			this.filters = { search: '', jenis: '', year: '' };
			router.get(route('pelatihan.index'));
		},
		changePage(url) {
			if (url) {
				router.visit(url);
			}
		},
		async deleteItem(id) {
			if (confirm('Yakin ingin menghapus?')) {
				try {
					const response = await fetch(route('pelatihan.destroy', id), {
						method: 'DELETE',
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': this.getCsrfToken(),
							'Content-Type': 'application/json'
						}
					});

					if (response.ok) {
						// Success - reload page data
						router.reload({
							only: ['pelatihans']
						});
					} else {
						const errorData = await response.json();
						console.error('Delete failed:', errorData);
					}
				} catch (error) {
					console.error('Delete failed:', error);
				}
			}
		},

		// Inline editing methods
		startEdit(pelatihan) {
			this.editingRows = { ...this.editingRows, [pelatihan.id]: true };
			this.originalData = { ...this.originalData, [pelatihan.id]: { ...pelatihan } };

			// Ensure proper data mapping for editing
			console.log('Starting edit for:', pelatihan);
			console.log('Current jenis_pelatihan_id:', pelatihan.jenis_pelatihan_id);
			console.log('Current pegawai_id:', pelatihan.pegawai_id);
		},

		cancelEdit(pelatihan) {
			const { [pelatihan.id]: removed, ...remaining } = this.editingRows;
			this.editingRows = remaining;

			// Reset data to original
			if (this.originalData[pelatihan.id]) {
				Object.assign(pelatihan, this.originalData[pelatihan.id]);
				const { [pelatihan.id]: removedData, ...remainingData } = this.originalData;
				this.originalData = remainingData;
			}

			// Clear any editing files
			const { [pelatihan.id]: removedFile, ...remainingFiles } = this.editingFiles;
			this.editingFiles = remainingFiles;
		},

		async saveEdit(pelatihan, isAutoSave = false, skipReload = false) {
			try {
				// Validate required fields first - skip alert for auto-save
				if (!pelatihan.pegawai_id || !pelatihan.nama_pelatihan || !pelatihan.jenis_pelatihan_id ||
					!pelatihan.penyelenggara || !pelatihan.tanggal_mulai || !pelatihan.tanggal_selesai || !pelatihan.jp) {
					if (!isAutoSave) {
						alert('Harap lengkapi semua field yang wajib diisi!');
					}
					return;
				}

				const formData = new FormData();

				// Add form fields
				formData.append('pegawai_id', pelatihan.pegawai_id);
				formData.append('nama_pelatihan', pelatihan.nama_pelatihan);
				formData.append('jenis_pelatihan_id', pelatihan.jenis_pelatihan_id);
				formData.append('penyelenggara', pelatihan.penyelenggara);
				formData.append('tanggal_mulai', pelatihan.tanggal_mulai);
				formData.append('tanggal_selesai', pelatihan.tanggal_selesai);
				formData.append('jp', pelatihan.jp);
				formData.append('_method', 'PUT');

				// Add file if selected - ini akan menggantikan file lama
				if (this.editingFiles[pelatihan.id]) {
					formData.append('sertifikat', this.editingFiles[pelatihan.id]);
					// Flag untuk menandai bahwa ini adalah file replacement
					formData.append('replace_file', 'true');
				}

				console.log('Saving edit with data:', {
					id: pelatihan.id,
					pegawai_id: pelatihan.pegawai_id,
					nama_pelatihan: pelatihan.nama_pelatihan,
					jenis_pelatihan_id: pelatihan.jenis_pelatihan_id,
					penyelenggara: pelatihan.penyelenggara,
					tanggal_mulai: pelatihan.tanggal_mulai,
					tanggal_selesai: pelatihan.tanggal_selesai,
					jp: pelatihan.jp
				});

				// Use fetch API for file uploads with method spoofing
				const response = await fetch(route('pelatihan.update', pelatihan.id), {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': this.getCsrfToken()
					}
				});

				if (response.ok) {
					const result = await response.json();
					console.log('Update successful:', result);

					// If caller requests skipReload (batch save), just clear local edit state for this row
					if (skipReload) {
						const { [pelatihan.id]: removed, ...remaining } = this.editingRows;
						this.editingRows = remaining;
						const { [pelatihan.id]: removedData, ...remainingData } = this.originalData;
						this.originalData = remainingData;
						const { [pelatihan.id]: removedFile, ...remainingFiles } = this.editingFiles;
						this.editingFiles = remainingFiles;
						return result;
					}

					// Default: reload page data
					router.reload({
						only: ['pelatihans'],
						onSuccess: () => {
							const { [pelatihan.id]: removed, ...remaining } = this.editingRows;
							this.editingRows = remaining;
							const { [pelatihan.id]: removedData, ...remainingData } = this.originalData;
							this.originalData = remainingData;
							// Remove file from editing files
							const { [pelatihan.id]: removedFile, ...remainingFiles } = this.editingFiles;
							this.editingFiles = remainingFiles;
						}
					});
				} else {
					const errorData = await response.json();
					console.error('Save failed:', errorData);
					if (!isAutoSave) {
						alert('Gagal menyimpan data: ' + (errorData.message || 'Unknown error'));
					}
				}
			} catch (error) {
				console.error('Save failed:', error);
				if (!isAutoSave) {
					alert('Terjadi kesalahan: ' + error.message);
				}
			}
		},

		async saveAll() {
			const ids = Object.keys(this.editingRows);
			if (ids.length === 0) return;

			// collect save promises
			const saves = ids.map(id => {
				const pel = this.pelatihans.data.find(p => p.id == id);
				if (!pel) return Promise.resolve();
				return this.saveEdit(pel, false, true);
			});

			try {
				await Promise.all(saves);
				// After all saved, reload once and clear editing state
				router.reload({
					only: ['pelatihans'], onSuccess: () => {
						this.editingRows = {};
						this.originalData = {};
						this.editingFiles = {};
					}
				});
			} catch (e) {
				console.error('Save all failed:', e);
				alert('Beberapa penyimpanan gagal. Cek console.');
			}
		},

		cancelAll() {
			// Revert all edited rows
			Object.keys(this.originalData).forEach(id => {
				const original = this.originalData[id];
				const pel = this.pelatihans.data.find(p => p.id == id);
				if (pel && original) Object.assign(pel, original);
			});
			this.editingRows = {};
			this.originalData = {};
			this.editingFiles = {};
		},

		startAddNew() {
			this.isAddingNew = true;
			this.newRow = {
				pegawai_id: '',
				nama_pelatihan: '',
				jenis_pelatihan_id: '',
				penyelenggara: '',
				tanggal_mulai: '',
				tanggal_selesai: '',
				jp: '',
				sertifikat_path: ''
			};
		},

		cancelAddNew() {
			this.isAddingNew = false;
		},

		async saveNew() {
			try {
				// Validate required fields first
				if (!this.newRow.pegawai_id || !this.newRow.nama_pelatihan || !this.newRow.jenis_pelatihan_id ||
					!this.newRow.penyelenggara || !this.newRow.tanggal_mulai || !this.newRow.tanggal_selesai || !this.newRow.jp) {
					alert('Harap lengkapi semua field yang wajib diisi!');
					return;
				}

				const formData = new FormData();

				// Add form fields
				formData.append('pegawai_id', this.newRow.pegawai_id);
				formData.append('nama_pelatihan', this.newRow.nama_pelatihan);
				formData.append('jenis_pelatihan_id', this.newRow.jenis_pelatihan_id);
				formData.append('penyelenggara', this.newRow.penyelenggara);
				formData.append('tanggal_mulai', this.newRow.tanggal_mulai);
				formData.append('tanggal_selesai', this.newRow.tanggal_selesai);
				formData.append('jp', this.newRow.jp);

				// Add file if selected
				if (this.newRow.sertifikat) {
					formData.append('sertifikat', this.newRow.sertifikat);
				}

				console.log('Saving new row with data:', {
					pegawai_id: this.newRow.pegawai_id,
					nama_pelatihan: this.newRow.nama_pelatihan,
					jenis_pelatihan_id: this.newRow.jenis_pelatihan_id,
					penyelenggara: this.newRow.penyelenggara,
					tanggal_mulai: this.newRow.tanggal_mulai,
					tanggal_selesai: this.newRow.tanggal_selesai,
					jp: this.newRow.jp
				});

				// Use fetch API for file uploads
				const response = await fetch(route('pelatihan.store'), {
					method: 'POST',
					body: formData,
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': this.getCsrfToken()
					}
				});

				if (response.ok) {
					const result = await response.json();
					console.log('Save successful:', result);

					// Success - reload page data
					router.reload({
						only: ['pelatihans'],
						onSuccess: () => {
							this.isAddingNew = false;
							this.resetNewRow();
						}
					});
				} else {
					const errorData = await response.json();
					console.error('Save failed:', errorData);
					alert('Gagal menyimpan data: ' + (errorData.message || 'Unknown error'));
				}
			} catch (error) {
				console.error('Save failed:', error);
				alert('Terjadi kesalahan: ' + error.message);
			}
		},

		getJenisId(jenisName) {
			if (typeof jenisName === 'number') return jenisName;
			const jenis = this.jenisPelatihan.find(j => j.nama === jenisName);
			return jenis ? jenis.id : jenisName;
		},

		isEditing(pelatihan) {
			return this.editingRows[pelatihan.id] === true;
		},

		// File handling methods
		handleFileSelect(event, rowId) {
			const file = event.target.files[0];
			if (file) {
				if (rowId === 'new') {
					this.newRow.sertifikat = file;
				} else {
					this.editingFiles = { ...this.editingFiles, [rowId]: file };
				}
			}
		},

		handleFileDrop(event, rowId) {
			const file = event.dataTransfer.files[0];
			if (file) {
				if (rowId === 'new') {
					this.newRow.sertifikat = file;
				} else {
					this.editingFiles = { ...this.editingFiles, [rowId]: file };
				}
			}
		},

		resetNewRow() {
			this.newRow = {
				pegawai_id: '',
				nama_pelatihan: '',
				jenis_pelatihan_id: '',
				penyelenggara: '',
				tanggal_mulai: '',
				tanggal_selesai: '',
				jp: '',
				sertifikat: null
			};
			this.newRowPegawai = null;
			this.newRowJenis = null;
		},

		// Helper method to extract filename from path
		getFileName(filePath) {
			if (!filePath) return '';
			return filePath.split('/').pop().split('\\').pop();
		}
	},
	watch: {
		'filters.search'(newVal, oldVal) {
			if (this.searchTimer) clearTimeout(this.searchTimer);
			// debounce 500ms
			this.searchTimer = setTimeout(() => {
				this.submitFilter();
			}, 500);
		},
		newRowPegawai(value) {
			console.log('newRowPegawai changed:', value);
			this.newRow.pegawai_id = value ? value.id : '';
		},
		newRowJenis(value) {
			console.log('newRowJenis changed:', value);
			this.newRow.jenis_pelatihan_id = value ? value.id : '';
		}
	},
	beforeUnmount() {
		if (this.searchTimer) clearTimeout(this.searchTimer);
	}
};
</script>

<style scoped>
.vuetify-input {
	height: 42px;
}

/* Override Vuetify styling to match Tailwind */
:deep(.v-field) {
	border-radius: 6px;
	font-size: 14px;
}

:deep(.v-field--focused) {
	box-shadow: 0 0 0 2px #3B82F6;
}

:deep(.v-field__outline) {
	--v-field-border-width: 1px;
	--v-field-border-opacity: 1;
}

:deep(.v-field__outline__start),
:deep(.v-field__outline__notch):after,
:deep(.v-field__outline__notch):before,
:deep(.v-field__outline__end) {
	border-color: #d1d5db;
	border-width: 1px;
}

:deep(.v-field--focused .v-field__outline__start),
:deep(.v-field--focused .v-field__outline__notch):after,
:deep(.v-field--focused .v-field__outline__notch):before,
:deep(.v-field--focused .v-field__outline__end) {
	border-color: #3B82F6;
	border-width: 1px;
}

/* Inline editing styles */
.editing-row {
	background-color: #fef3cd !important;
	border: 2px solid #fbbf24 !important;
}

.adding-row {
	background-color: #dbeafe !important;
	border: 2px solid #3b82f6 !important;
}

.editable-cell input,
.editable-cell select {
	font-size: 11px;
	padding: 2px 4px;
	border: 1px solid #d1d5db;
	border-radius: 4px;
	width: 100%;
	min-width: 60px;
}

.editable-cell input:focus,
.editable-cell select:focus {
	outline: none;
	border-color: #3b82f6;
	box-shadow: 0 0 0 1px #3b82f6;
}

tr:hover:not(.editing-row):not(.adding-row) {
	background-color: #f9fafb;
	cursor: pointer;
}

/* File upload styles */
.upload-area {
	border: 2px dashed #d1d5db;
	border-radius: 6px;
	padding: 6px;
	text-align: center;
	cursor: pointer;
	background-color: #f9fafb;
	transition: all 0.2s;
	min-height: 36px;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	font-size: 10px;
	color: #6b7280;
	line-height: 1.2;
}

.upload-area:hover {
	border-color: #3b82f6;
	background-color: #eff6ff;
	color: #3b82f6;
}

.upload-area.drag-over {
	border-color: #10b981;
	background-color: #ecfdf5;
	color: #10b981;
}

/* File link and name styles */
.file-link {
	text-decoration: none;
}

.file-link .file-name {
	display: inline-block;
	vertical-align: middle;
}

.max-w-28 {
	max-width: 7rem;
}

/* Mini combobox styles */
.mini-combobox :deep(.v-field) {
	font-size: 11px !important;
	min-height: 28px !important;
}

.mini-combobox :deep(.v-field__input) {
	padding: 2px 4px !important;
	min-height: 24px !important;
}

.mini-combobox :deep(.v-field--variant-outlined .v-field__outline__notch) {
	border-color: #d1d5db !important;
}

.date-jp-container {
	min-width: 120px;
}

.date-inputs {
	display: flex;
	flex-direction: column;
	gap: 2px;
}

.action-buttons {
	display: flex;
	gap: 4px;
	justify-content: center;
}

.action-buttons button {
	padding: 4px;
	border-radius: 4px;
	transition: all 0.2s;
}

.action-buttons button:hover {
	background-color: rgba(0, 0, 0, 0.1);
}

/* File name display styles */
.max-w-20 {
	max-width: 5rem;
}

.truncate {
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
</style>
