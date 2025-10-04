<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detail Pelatihan</h1>
        <div class="flex space-x-2">
          <Link
            :href="route('pelatihan.edit', pelatihan.id)"
            class="bg-amber-600 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition duration-200"
          >
            <i class="fas fa-edit mr-2"></i>Edit
          </Link>
          <Link
            :href="route('pelatihan.index')"
            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200"
          >
            <i class="fas fa-arrow-left mr-2"></i>Kembali
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Pegawai -->
        <div class="bg-gray-50 p-6 rounded-lg">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pegawai</h3>
          <div class="space-y-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
              <p class="text-gray-800">{{ pelatihan.pegawai.nama_lengkap }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">NIP</label>
              <p class="text-gray-800">{{ pelatihan.pegawai.nip || 'Tidak Ada' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Pangkat/Golongan</label>
              <p class="text-gray-800">{{ pelatihan.pegawai.pangkat_golongan }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Unit Kerja</label>
              <p class="text-gray-800">{{ pelatihan.pegawai.unit_kerja }}</p>
            </div>
          </div>
        </div>

        <!-- Informasi Pelatihan -->
        <div class="bg-gray-50 p-6 rounded-lg">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pelatihan</h3>
          <div class="space-y-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Nama Pelatihan</label>
              <p class="text-gray-800">{{ pelatihan.nama_pelatihan }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Jenis Pelatihan</label>
              <span
                class="inline-block text-sm px-3 py-1 rounded-full"
                :class="getJenisColor(pelatihan.jenis_pelatihan)"
              >
                {{ pelatihan.jenis_pelatihan }}
              </span>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Penyelenggara</label>
              <p class="text-gray-800">{{ pelatihan.penyelenggara }}</p>
            </div>
            <div v-if="pelatihan.tempat">
              <label class="text-sm font-medium text-gray-600">Tempat</label>
              <p class="text-gray-800">{{ pelatihan.tempat }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Status</label>
              <span
                class="inline-block text-sm px-3 py-1 rounded-full"
                :class="getStatusColor(pelatihan.status)"
              >
                <i :class="getStatusIcon(pelatihan.status)" class="mr-1"></i>{{ formatStatus(pelatihan.status) }}
              </span>
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
            <p class="text-gray-800">{{ pelatihan.tanggal_mulai }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Tanggal Selesai</label>
            <p class="text-gray-800">{{ pelatihan.tanggal_selesai }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Jam Pelajaran (JP)</label>
            <p class="text-2xl font-bold text-indigo-600">{{ pelatihan.jp }}</p>
          </div>
        </div>
      </div>

      <!-- Deskripsi -->
      <div v-if="pelatihan.deskripsi" class="mt-6 bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi</h3>
        <p class="text-gray-700 whitespace-pre-line">{{ pelatihan.deskripsi }}</p>
      </div>

      <!-- Sertifikat -->
      <div v-if="pelatihan.sertifikat_path" class="mt-6 bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Sertifikat</h3>
        <div class="flex items-center space-x-4">
          <div class="flex-shrink-0">
            <i class="fas fa-certificate text-3xl text-amber-600"></i>
          </div>
          <div class="flex-1">
            <p class="text-gray-700 mb-2">Sertifikat pelatihan tersedia</p>
            <a
              :href="`/storage/${pelatihan.sertifikat_path}`"
              target="_blank"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200"
            >
              <i class="fas fa-download mr-2"></i>
              Lihat/Download Sertifikat
            </a>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-8 flex justify-between">
        <Link
          :href="route('pelatihan.index')"
          class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200"
        >
          <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
        </Link>
        <div class="space-x-2">
          <Link
            :href="route('pelatihan.edit', pelatihan.id)"
            class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-600 transition duration-200"
          >
            <i class="fas fa-edit mr-2"></i>Edit Pelatihan
          </Link>
          <button
            @click="deleteItem"
            class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-600 transition duration-200"
          >
            <i class="fas fa-trash mr-2"></i>Hapus
          </button>
        </div>
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
    pelatihan: Object,
  },
  methods: {
    getJenisColor(jenis) {
      const colorMap = {
        'Diklat Struktural': 'bg-indigo-100 text-indigo-700',
        'Diklat Fungsional': 'bg-emerald-100 text-emerald-700',
        'Diklat Teknis': 'bg-indigo-100 text-indigo-700',
        'Workshop': 'bg-amber-100 text-amber-700'
      };
      return colorMap[jenis] || 'bg-gray-100 text-gray-800';
    },
    getStatusColor(status) {
      const colorMap = {
        'selesai': 'bg-emerald-100 text-emerald-700',
        'sedang_berjalan': 'bg-amber-100 text-amber-700',
        'akan_datang': 'bg-gray-100 text-gray-800'
      };
      return colorMap[status] || 'bg-gray-100 text-gray-800';
    },
    getStatusIcon(status) {
      const iconMap = {
        'selesai': 'fas fa-check-circle',
        'sedang_berjalan': 'fas fa-clock',
        'akan_datang': 'fas fa-calendar-alt'
      };
      return iconMap[status] || 'fas fa-question-circle';
    },
    formatStatus(status) {
      return status.replace('_', ' ').split(' ').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
      ).join(' ');
    },
    deleteItem() {
      if (confirm('Yakin ingin menghapus pelatihan ini?')) {
        router.delete(route('pelatihan.destroy', this.pelatihan.id));
      }
    }
  }
};
</script>
