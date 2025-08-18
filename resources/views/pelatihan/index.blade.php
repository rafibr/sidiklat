@extends('layout.app')

@section('title', 'Data Pelatihan - SIMPEG Auto SPA')

@section('content')
<div class="p-6">
	<div class="flex flex-col sm:flex-row justify-between items-center mb-6">
		<h2 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Data Pelatihan</h2>
		<a href="{{ route('pelatihan.create') }}"
			class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
			<i class="fas fa-plus mr-2"></i>Tambah Pelatihan
		</a>
	</div>

	<!-- Filters -->
	<form method="GET" class="bg-gray-50 p-4 rounded-lg mb-6">
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
				<input type="text" name="search" value="{{ request('search') }}"
					placeholder="Nama pelatihan atau pegawai..."
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
			</div>

			<div>
				<label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelatihan</label>
				<select name="jenis"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
					<option value="">Semua Jenis</option>
					@foreach($jenisPelatihan as $jenis)
					<option value="{{ $jenis }}" {{ request('jenis')==$jenis ? 'selected' : '' }}>
						{{ $jenis }}
					</option>
					@endforeach
				</select>
			</div>

			<div class="flex items-end">
				<button type="submit"
					class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors mr-2">
					<i class="fas fa-search mr-2"></i>Filter
				</button>
				<a href="{{ route('pelatihan.index') }}"
					class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
					Reset
				</a>
			</div>
		</div>
	</form>

	@if(session('success'))
	<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
		{{ session('success') }}
	</div>
	@endif

	<!-- Table -->
	<div class="bg-white rounded-lg shadow-lg overflow-hidden">
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Pegawai</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Pelatihan</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Tanggal</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JP
						</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Status</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Sertifikat</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
						</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					@forelse($pelatihans as $pelatihan)
					<tr>
						<td class="px-6 py-4 whitespace-nowrap">
							<div class="text-sm font-medium text-gray-900">{{ $pelatihan->pegawai->nama_lengkap }}</div>
							<div class="text-sm text-gray-500">{{ $pelatihan->pegawai->nip ?? 'Tidak Ada NIP' }}</div>
						</td>
						<td class="px-6 py-4">
							<div class="text-sm font-medium text-gray-900">{{ $pelatihan->nama_pelatihan }}</div>
							<div class="text-sm text-gray-500">{{ $pelatihan->penyelenggara }}</div>
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{
                                    $pelatihan->jenis_pelatihan == 'Diklat Struktural' ? 'bg-blue-100 text-blue-800' :
                                    ($pelatihan->jenis_pelatihan == 'Diklat Fungsional' ? 'bg-green-100 text-green-800' :
                                    ($pelatihan->jenis_pelatihan == 'Diklat Teknis' ? 'bg-purple-100 text-purple-800' :
                                    ($pelatihan->jenis_pelatihan == 'Workshop' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')))
                                }}">
								{{ $pelatihan->jenis_pelatihan }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							{{ $pelatihan->tanggal_mulai }} - {{ $pelatihan->tanggal_selesai }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
							{{ $pelatihan->jp }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{
                                    $pelatihan->status == 'selesai' ? 'bg-green-100 text-green-800' :
                                    ($pelatihan->status == 'sedang_berjalan' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                }}">
								{{ ucwords(str_replace('_', ' ', $pelatihan->status)) }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							@if($pelatihan->sertifikat_path)
							<a href="{{ Storage::url($pelatihan->sertifikat_path) }}" target="_blank"
								class="text-blue-600 hover:text-blue-800">
								<i class="fas fa-file-pdf"></i> Lihat
							</a>
							@else
							<span class="text-gray-400">Tidak ada</span>
							@endif
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
							<div class="flex space-x-2">
								<a href="{{ route('pelatihan.show', $pelatihan) }}"
									class="text-blue-600 hover:text-blue-800">
									<i class="fas fa-eye"></i>
								</a>
								<a href="{{ route('pelatihan.edit', $pelatihan) }}"
									class="text-yellow-600 hover:text-yellow-800">
									<i class="fas fa-edit"></i>
								</a>
								<form action="{{ route('pelatihan.destroy', $pelatihan) }}" method="POST" class="inline"
									onsubmit="return confirm('Yakin ingin menghapus?')">
									@csrf
									@method('DELETE')
									<button type="submit" class="text-red-600 hover:text-red-800">
										<i class="fas fa-trash"></i>
									</button>
								</form>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="8" class="px-6 py-12 text-center text-gray-500">
							<i class="fas fa-database text-4xl mb-4"></i>
							<p>Tidak ada data pelatihan</p>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

	<!-- Pagination -->
	<div class="mt-6">
		{{ $pelatihans->appends(request()->query())->links() }}
	</div>
</div>
@endsection