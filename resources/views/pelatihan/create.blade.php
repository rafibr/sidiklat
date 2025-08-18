@extends('layout.app')

@section('title', 'Tambah Pelatihan - SIMPEG Auto SPA')

@section('content')
<div class="p-6">
	<div class="flex items-center mb-6">
		<a href="{{ route('pelatihan.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
			<i class="fas fa-arrow-left mr-2"></i>Kembali
		</a>
		<h2 class="text-2xl font-bold text-gray-800">Tambah Data Pelatihan</h2>
	</div>

	<div class="max-w-4xl bg-white rounded-lg shadow-lg p-6">
		<form action="{{ route('pelatihan.store') }}" method="POST" enctype="multipart/form-data">
			@csrf

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<!-- Pegawai -->
				<div class="md:col-span-2">
					<label for="pegawai_id" class="block text-sm font-medium text-gray-700 mb-2">
						Pegawai <span class="text-red-500">*</span>
					</label>
					<select name="pegawai_id" id="pegawai_id" required
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('pegawai_id') border-red-300 @enderror">
						<option value="">Pilih Pegawai</option>
						@foreach($pegawais as $pegawai)
						<option value="{{ $pegawai->id }}" {{ old('pegawai_id')==$pegawai->id ? 'selected' : '' }}>
							{{ $pegawai->nama_lengkap }} - {{ $pegawai->nip ?? 'Tidak Ada NIP' }}
						</option>
						@endforeach
					</select>
					@error('pegawai_id')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Nama Pelatihan -->
				<div class="md:col-span-2">
					<label for="nama_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">
						Nama Pelatihan <span class="text-red-500">*</span>
					</label>
					<input type="text" name="nama_pelatihan" id="nama_pelatihan" required
						value="{{ old('nama_pelatihan') }}"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('nama_pelatihan') border-red-300 @enderror">
					@error('nama_pelatihan')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Jenis Pelatihan -->
				<div>
					<label for="jenis_pelatihan" class="block text-sm font-medium text-gray-700 mb-2">
						Jenis Pelatihan <span class="text-red-500">*</span>
					</label>
					<select name="jenis_pelatihan" id="jenis_pelatihan" required
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('jenis_pelatihan') border-red-300 @enderror">
						<option value="">Pilih Jenis</option>
						@foreach($jenisPelatihan as $jenis)
						<option value="{{ $jenis }}" {{ old('jenis_pelatihan')==$jenis ? 'selected' : '' }}>
							{{ $jenis }}
						</option>
						@endforeach
					</select>
					@error('jenis_pelatihan')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Penyelenggara -->
				<div>
					<label for="penyelenggara" class="block text-sm font-medium text-gray-700 mb-2">
						Penyelenggara <span class="text-red-500">*</span>
					</label>
					<input type="text" name="penyelenggara" id="penyelenggara" required
						value="{{ old('penyelenggara') }}"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('penyelenggara') border-red-300 @enderror">
					@error('penyelenggara')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Tempat -->
				<div>
					<label for="tempat" class="block text-sm font-medium text-gray-700 mb-2">
						Tempat Pelaksanaan
					</label>
					<input type="text" name="tempat" id="tempat" value="{{ old('tempat') }}"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('tempat') border-red-300 @enderror">
					@error('tempat')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Tanggal Mulai -->
				<div>
					<label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">
						Tanggal Mulai <span class="text-red-500">*</span>
					</label>
					<input type="date" name="tanggal_mulai" id="tanggal_mulai" required
						value="{{ old('tanggal_mulai') }}"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('tanggal_mulai') border-red-300 @enderror">
					@error('tanggal_mulai')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Tanggal Selesai -->
				<div>
					<label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
						Tanggal Selesai <span class="text-red-500">*</span>
					</label>
					<input type="date" name="tanggal_selesai" id="tanggal_selesai" required
						value="{{ old('tanggal_selesai') }}"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('tanggal_selesai') border-red-300 @enderror">
					@error('tanggal_selesai')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- JP -->
				<div>
					<label for="jp" class="block text-sm font-medium text-gray-700 mb-2">
						Jam Pelajaran (JP) <span class="text-red-500">*</span>
					</label>
					<input type="number" name="jp" id="jp" required min="1" value="{{ old('jp') }}"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('jp') border-red-300 @enderror">
					@error('jp')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Status -->
				<div>
					<label for="status" class="block text-sm font-medium text-gray-700 mb-2">
						Status <span class="text-red-500">*</span>
					</label>
					<select name="status" id="status" required
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror">
						<option value="">Pilih Status</option>
						<option value="akan_datang" {{ old('status')=='akan_datang' ? 'selected' : '' }}>Akan Datang
						</option>
						<option value="sedang_berjalan" {{ old('status')=='sedang_berjalan' ? 'selected' : '' }}>Sedang
							Berjalan</option>
						<option value="selesai" {{ old('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
					</select>
					@error('status')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Sertifikat -->
				<div class="md:col-span-2">
					<label for="sertifikat" class="block text-sm font-medium text-gray-700 mb-2">
						File Sertifikat
					</label>
					<input type="file" name="sertifikat" id="sertifikat" accept=".pdf,.jpg,.jpeg,.png"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('sertifikat') border-red-300 @enderror">
					<p class="mt-1 text-xs text-gray-500">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB</p>
					@error('sertifikat')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>

				<!-- Deskripsi -->
				<div class="md:col-span-2">
					<label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
						Deskripsi
					</label>
					<textarea name="deskripsi" id="deskripsi" rows="3"
						class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
					@error('deskripsi')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
			</div>

			<!-- Submit Buttons -->
			<div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
				<a href="{{ route('pelatihan.index') }}"
					class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
					Batal
				</a>
				<button type="submit"
					class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
					<i class="fas fa-save mr-2"></i>Simpan
				</button>
			</div>
		</form>
	</div>
</div>
@endsection