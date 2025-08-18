@extends('layout.app')

@section('title', 'Detail Pelatihan')

@section('content')
<div class="container mx-auto px-4 py-8">
	<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
		<div class="flex items-center justify-between mb-6">
			<h1 class="text-3xl font-bold text-gray-800">Detail Pelatihan</h1>
			<div class="flex space-x-2">
				<a href="{{ route('pelatihan.edit', $pelatihan) }}"
					class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200">
					<i class="fas fa-edit mr-2"></i>Edit
				</a>
				<a href="{{ route('pelatihan.index') }}"
					class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
					<i class="fas fa-arrow-left mr-2"></i>Kembali
				</a>
			</div>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<!-- Informasi Pegawai -->
			<div class="bg-gray-50 p-6 rounded-lg">
				<h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pegawai</h3>
				<div class="space-y-3">
					<div>
						<label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
						<p class="text-gray-800">{{ $pelatihan->pegawai->nama_lengkap }}</p>
					</div>
					<div>
						<label class="text-sm font-medium text-gray-600">NIP</label>
						<p class="text-gray-800">{{ $pelatihan->pegawai->nip }}</p>
					</div>
					<div>
						<label class="text-sm font-medium text-gray-600">Pangkat/Golongan</label>
						<p class="text-gray-800">{{ $pelatihan->pegawai->pangkat_golongan }}</p>
					</div>
					<div>
						<label class="text-sm font-medium text-gray-600">Unit Kerja</label>
						<p class="text-gray-800">{{ $pelatihan->pegawai->unit_kerja }}</p>
					</div>
				</div>
			</div>

			<!-- Informasi Pelatihan -->
			<div class="bg-gray-50 p-6 rounded-lg">
				<h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pelatihan</h3>
				<div class="space-y-3">
					<div>
						<label class="text-sm font-medium text-gray-600">Nama Pelatihan</label>
						<p class="text-gray-800">{{ $pelatihan->nama_pelatihan }}</p>
					</div>
					<div>
						<label class="text-sm font-medium text-gray-600">Jenis Pelatihan</label>
						<span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
							{{ $pelatihan->jenis_pelatihan }}
						</span>
					</div>
					<div>
						<label class="text-sm font-medium text-gray-600">Penyelenggara</label>
						<p class="text-gray-800">{{ $pelatihan->penyelenggara }}</p>
					</div>
					@if($pelatihan->tempat)
					<div>
						<label class="text-sm font-medium text-gray-600">Tempat</label>
						<p class="text-gray-800">{{ $pelatihan->tempat }}</p>
					</div>
					@endif
					<div>
						<label class="text-sm font-medium text-gray-600">Status</label>
						@if($pelatihan->status == 'selesai')
						<span class="inline-block bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
							<i class="fas fa-check-circle mr-1"></i>Selesai
						</span>
						@elseif($pelatihan->status == 'sedang_berjalan')
						<span class="inline-block bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">
							<i class="fas fa-clock mr-1"></i>Sedang Berjalan
						</span>
						@else
						<span class="inline-block bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded-full">
							<i class="fas fa-calendar-alt mr-1"></i>Akan Datang
						</span>
						@endif
					</div>
				</div>
			</div>
		</div>

		<!-- Detail Waktu dan JP -->
		<div class="mt-6 bg-gray-50 p-6 rounded-lg">
			<h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Waktu dan JP</h3>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
				<div>
					<label class="text-sm font-medium text-gray-600">Tanggal Mulai</label>
					<p class="text-gray-800">{{ $pelatihan->tanggal_mulai }}</p>
				</div>
				<div>
					<label class="text-sm font-medium text-gray-600">Tanggal Selesai</label>
					<p class="text-gray-800">{{ $pelatihan->tanggal_selesai }}</p>
				</div>
				<div>
					<label class="text-sm font-medium text-gray-600">Jam Pelajaran (JP)</label>
					<p class="text-gray-800 font-semibold">{{ $pelatihan->jp }} JP</p>
				</div>
			</div>
		</div>

		<!-- Deskripsi -->
		@if($pelatihan->deskripsi)
		<div class="mt-6 bg-gray-50 p-6 rounded-lg">
			<h3 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi</h3>
			<p class="text-gray-700 leading-relaxed">{{ $pelatihan->deskripsi }}</p>
		</div>
		@endif

		<!-- Sertifikat -->
		@if($pelatihan->sertifikat_path)
		<div class="mt-6 bg-gray-50 p-6 rounded-lg">
			<h3 class="text-lg font-semibold text-gray-800 mb-4">Sertifikat</h3>
			<div class="flex items-center space-x-4">
				<a href="{{ Storage::url($pelatihan->sertifikat_path) }}" target="_blank"
					class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
					<i class="fas fa-download mr-2"></i>Lihat/Unduh Sertifikat
				</a>
			</div>
		</div>
		@endif

		<!-- Actions -->
		<div class="mt-8 flex justify-between items-center pt-6 border-t">
			<div class="text-sm text-gray-500">
				Terakhir diupdate: {{ $pelatihan->updated_at->format('d F Y H:i') }}
			</div>
			<div class="flex space-x-2">
				<form action="{{ route('pelatihan.destroy', $pelatihan) }}" method="POST" class="inline"
					onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelatihan ini?')">
					@csrf
					@method('DELETE')
					<button type="submit"
						class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
						<i class="fas fa-trash mr-2"></i>Hapus
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection