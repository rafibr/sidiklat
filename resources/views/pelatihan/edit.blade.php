@extends('layout.app')

@section('title', 'Edit Pelatihan')

@section('content')
<div class="container mx-auto px-4 py-8">
	<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
		<div class="flex items-center justify-between mb-6">
			<h1 class="text-3xl font-bold text-gray-800">Edit Pelatihan</h1>
			<a href="{{ route('pelatihan.index') }}"
				class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
				<i class="fas fa-arrow-left mr-2"></i>Kembali
			</a>
		</div>

		<form action="{{ route('pelatihan.update', $pelatihan) }}" method="POST" enctype="multipart/form-data"
			class="space-y-6">
			@csrf
			@method('PUT')

			<!-- Pegawai -->
			<div>
				<label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-2">Pegawai</label>
				<select name="pegawai_id" id="pegawai_id"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pegawai_id') border-red-500 @enderror"
					required>
					<option value="">Pilih Pegawai</option>
					@foreach($pegawais as $pegawai)
					<option value="{{ $pegawai->id }}" {{ old('pegawai_id', $pelatihan->pegawai_id) == $pegawai->id ?
						'selected' : '' }}>
						{{ $pegawai->nama_lengkap }} - {{ $pegawai->pangkat_golongan }}
					</option>
					@endforeach
				</select>
				@error('pegawai_id')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Nama Pelatihan -->
			<div>
				<label for="nama_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">Nama Pelatihan</label>
				<input type="text" name="nama_pelatihan" id="nama_pelatihan"
					value="{{ old('nama_pelatihan', $pelatihan->nama_pelatihan) }}"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_pelatihan') border-red-500 @enderror"
					required>
				@error('nama_pelatihan')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Jenis Pelatihan -->
			<div>
				<label for="jenis_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">Jenis
					Pelatihan</label>
				<select name="jenis_pelatihan" id="jenis_pelatihan"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jenis_pelatihan') border-red-500 @enderror"
					required>
					<option value="">Pilih Jenis Pelatihan</option>
					@foreach($jenisPelatihan as $jenis)
					<option value="{{ $jenis }}" {{ old('jenis_pelatihan', $pelatihan->jenis_pelatihan) == $jenis ?
						'selected' : '' }}>{{ $jenis }}</option>
					@endforeach
				</select>
				@error('jenis_pelatihan')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Penyelenggara -->
			<div>
				<label for="penyelenggara" class="block text-sm font-medium text-gray-700 mb-2">Penyelenggara</label>
				<input type="text" name="penyelenggara" id="penyelenggara"
					value="{{ old('penyelenggara', $pelatihan->penyelenggara) }}"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('penyelenggara') border-red-500 @enderror"
					required>
				@error('penyelenggara')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Tempat -->
			<div>
				<label for="tempat" class="block text-sm font-medium text-gray-700 mb-2">Tempat</label>
				<input type="text" name="tempat" id="tempat" value="{{ old('tempat', $pelatihan->tempat) }}"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tempat') border-red-500 @enderror">
				@error('tempat')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<!-- Tanggal Mulai -->
				<div>
					<label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
						Mulai</label>
					<input type="text" name="tanggal_mulai" id="tanggal_mulai"
						value="{{ old('tanggal_mulai', $pelatihan->tanggal_mulai) }}"
						placeholder="Contoh: 15 Oktober 2024"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_mulai') border-red-500 @enderror"
						required>
					@error('tanggal_mulai')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Tanggal Selesai -->
				<div>
					<label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
						Selesai</label>
					<input type="text" name="tanggal_selesai" id="tanggal_selesai"
						value="{{ old('tanggal_selesai', $pelatihan->tanggal_selesai) }}"
						placeholder="Contoh: 20 Oktober 2024"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_selesai') border-red-500 @enderror"
						required>
					@error('tanggal_selesai')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<!-- JP -->
				<div>
					<label for="jp" class="block text-sm font-medium text-gray-700 mb-2">Jam Pelajaran (JP)</label>
					<input type="number" name="jp" id="jp" value="{{ old('jp', $pelatihan->jp) }}" min="1"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jp') border-red-500 @enderror"
						required>
					@error('jp')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Status -->
				<div>
					<label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
					<select name="status" id="status"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
						required>
						<option value="">Pilih Status</option>
						<option value="selesai" {{ old('status', $pelatihan->status) == 'selesai' ? 'selected' : ''
							}}>Selesai</option>
						<option value="sedang_berjalan" {{ old('status', $pelatihan->status) == 'sedang_berjalan' ?
							'selected' : '' }}>Sedang Berjalan</option>
						<option value="akan_datang" {{ old('status', $pelatihan->status) == 'akan_datang' ? 'selected' :
							'' }}>Akan Datang</option>
					</select>
					@error('status')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
			</div>

			<!-- Deskripsi -->
			<div>
				<label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
				<textarea name="deskripsi" id="deskripsi" rows="4"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror"
					placeholder="Masukkan deskripsi pelatihan (opsional)">{{ old('deskripsi', $pelatihan->deskripsi) }}</textarea>
				@error('deskripsi')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Sertifikat -->
			<div>
				<label for="sertifikat" class="block text-sm font-medium text-gray-700 mb-2">Sertifikat</label>
				@if($pelatihan->sertifikat_path)
				<div class="mb-2">
					<p class="text-sm text-gray-600">Sertifikat saat ini:</p>
					<a href="{{ Storage::url($pelatihan->sertifikat_path) }}" target="_blank"
						class="text-blue-600 hover:text-blue-800 underline">Lihat Sertifikat</a>
				</div>
				@endif
				<input type="file" name="sertifikat" id="sertifikat" accept=".pdf,.jpg,.jpeg,.png"
					class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sertifikat') border-red-500 @enderror">
				<p class="mt-1 text-sm text-gray-500">Upload file PDF, JPG, JPEG, atau PNG (maksimal 2MB). Kosongkan
					jika tidak ingin mengganti.</p>
				@error('sertifikat')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<div class="flex justify-end space-x-4">
				<button type="button" onclick="history.back()"
					class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
					Batal
				</button>
				<button type="submit"
					class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
					<i class="fas fa-save mr-2"></i>Update Pelatihan
				</button>
			</div>
		</form>
	</div>
</div>
@endsection