<template>
  <AppLayout>
    <div class="p-3 sm:p-4 md:p-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3 sm:gap-0">
      <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Data Pelatihan</h2>
      <Link
        :href="route('pelatihan.create')"
        class="w-full sm:w-auto bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center text-sm"
      >
        <i class="fas fa-plus mr-1 sm:mr-2"></i>Tambah Pelatihan
      </Link>
    </div>

    <!-- Filters -->
    <form @submit.prevent="submitFilter" class="bg-gray-50 p-3 sm:p-4 rounded-lg mb-4 md:mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
          <input
            type="text"
            v-model="filters.search"
            placeholder="Nama pelatihan atau pegawai..."
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelatihan</label>
          <select
            v-model="filters.jenis"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Semua Jenis</option>
            <option v-for="jenis in jenisPelatihan" :key="jenis" :value="jenis">
              {{ jenis }}
            </option>
          </select>
        </div>

        <div class="flex items-end gap-2">
          <button
            type="submit"
            class="flex-1 sm:flex-none bg-blue-600 text-white px-3 sm:px-4 py-2 text-sm rounded-md hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-search mr-1 sm:mr-2"></i>
            <span class="hidden sm:inline">Filter</span>
            <span class="sm:hidden">Cari</span>
          </button>
          <button
            type="button"
            @click="resetFilter"
            class="flex-1 sm:flex-none bg-gray-500 text-white px-3 sm:px-4 py-2 text-sm rounded-md hover:bg-gray-600 transition-colors text-center"
          >
            Reset
          </button>
        </div>
      </div>
    </form>

    <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
      {{ $page.props.flash.success }}
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border-compact card-compact">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Pegawai
              </th>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Pelatihan
              </th>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Jenis
              </th>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tanggal
              </th>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                JP
              </th>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Sertifikat
              </th>
              <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="pelatihans.data.length === 0">
              <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-database text-4xl mb-4"></i>
                <p>Tidak ada data pelatihan</p>
              </td>
            </tr>
            <tr v-for="pelatihan in pelatihans.data" :key="pelatihan.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ pelatihan.pegawai.nama_lengkap }}</div>
                <div class="text-sm text-gray-500">{{ pelatihan.pegawai.nip || 'Tidak Ada NIP' }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">{{ pelatihan.nama_pelatihan }}</div>
                <div class="text-sm text-gray-500">{{ pelatihan.penyelenggara }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getJenisColor(pelatihan.jenis_pelatihan)"
                >
                  {{ pelatihan.jenis_pelatihan }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ pelatihan.tanggal_mulai }} - {{ pelatihan.tanggal_selesai }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                {{ pelatihan.jp }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusColor(pelatihan.status)"
                >
                  {{ formatStatus(pelatihan.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <a
                  v-if="pelatihan.sertifikat_path"
                  :href="`/storage/${pelatihan.sertifikat_path}`"
                  target="_blank"
                  class="text-blue-600 hover:text-blue-800"
                >
                  <i class="fas fa-file-pdf"></i> Lihat
                </a>
                <span v-else class="text-gray-400">Tidak ada</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <Link
                    :href="route('pelatihan.show', pelatihan.id)"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    <i class="fas fa-eye"></i>
                  </Link>
                  <Link
                    :href="route('pelatihan.edit', pelatihan.id)"
                    class="text-yellow-600 hover:text-yellow-800"
                  >
                    <i class="fas fa-edit"></i>
                  </Link>
                  <button
                    @click="deleteItem(pelatihan.id)"
                    class="text-red-600 hover:text-red-800"
                  >
                    <i class="fas fa-trash"></i>
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
        <button
          v-for="link in pelatihans.links"
          :key="link.label"
          @click="changePage(link.url)"
          :disabled="!link.url"
          class="px-3 py-2 text-sm border rounded"
          :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-600'"
          v-html="link.label"
        ></button>
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
  },
  data() {
    return {
      filters: {
        search: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('search') || '' : '',
        jenis: this.$page.url.split('?')[1] ? new URLSearchParams(this.$page.url.split('?')[1]).get('jenis') || '' : '',
      }
    };
  },
  methods: {
    getJenisColor(jenis) {
      const colorMap = {
        'Diklat Struktural': 'bg-blue-100 text-blue-800',
        'Diklat Fungsional': 'bg-green-100 text-green-800',
        'Diklat Teknis': 'bg-purple-100 text-purple-800',
        'Workshop': 'bg-orange-100 text-orange-800'
      };
      return colorMap[jenis] || 'bg-gray-100 text-gray-800';
    },
    getStatusColor(status) {
      const colorMap = {
        'selesai': 'bg-green-100 text-green-800',
        'sedang_berjalan': 'bg-yellow-100 text-yellow-800',
        'akan_datang': 'bg-gray-100 text-gray-800'
      };
      return colorMap[status] || 'bg-gray-100 text-gray-800';
    },
    formatStatus(status) {
      return status.replace('_', ' ').split(' ').map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
      ).join(' ');
    },
    submitFilter() {
      router.get(route('pelatihan.index'), this.filters, { preserveState: true });
    },
    resetFilter() {
      this.filters = { search: '', jenis: '' };
      router.get(route('pelatihan.index'));
    },
    changePage(url) {
      if (url) {
        router.visit(url);
      }
    },
    deleteItem(id) {
      if (confirm('Yakin ingin menghapus?')) {
        router.delete(route('pelatihan.destroy', id));
      }
    }
  }
};
</script>
