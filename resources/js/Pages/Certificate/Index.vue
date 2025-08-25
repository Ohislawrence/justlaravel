<template>
    <AppLayout title="My Certificates">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Certificates
          </h2>
          <div class="flex space-x-2">
            <button 
              @click="exportToCSV" 
              class="btn btn-secondary"
              :disabled="certificates.data.length === 0"
            >
              Export to CSV
            </button>
          </div>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Filters -->
          <div class="bg-white shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <InputLabel for="search" value="Search" />
                  <TextInput
                    id="search"
                    v-model="filters.search"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Quiz name, certificate #..."
                  />
                </div>
                <div>
                  <InputLabel for="quiz_type" value="Quiz Type" />
                  <select
                    id="quiz_type"
                    v-model="filters.quiz_type"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  >
                    <option value="">All Types</option>
                    <option v-for="type in quizTypes" :key="type.value" :value="type.value">
                      {{ type.label }}
                    </option>
                  </select>
                </div>
                <div>
                  <InputLabel for="date_from" value="From Date" />
                  <input
                    id="date_from"
                    v-model="filters.date_from"
                    type="date"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  >
                </div>
                <div>
                  <InputLabel for="date_to" value="To Date" />
                  <input
                    id="date_to"
                    v-model="filters.date_to"
                    type="date"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                  >
                </div>
              </div>
              <div class="mt-4 flex justify-end">
                <button
                  @click="resetFilters"
                  class="btn btn-secondary mr-2"
                >
                  Reset Filters
                </button>
                <button
                  @click="applyFilters"
                  class="btn btn-primary"
                >
                  Apply Filters
                </button>
              </div>
            </div>
          </div>
  
          <!-- Certificates List -->
          <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sort('quiz.title')">
                      Quiz
                      <SortIcon :direction="sortField === 'quiz.title' ? sortDirection : null" />
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sort('issued_at')">
                      Issued Date
                      <SortIcon :direction="sortField === 'issued_at' ? sortDirection : null" />
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sort('metadata.score')">
                      Score
                      <SortIcon :direction="sortField === 'metadata.score' ? sortDirection : null" />
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="certificate in certificates.data" :key="certificate.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">
                          {{ certificate.quiz.title }}
                        </div>
                      </div>
                      <div class="text-xs text-gray-500 mt-1">
                        #{{ certificate.certificate_number }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ formatDate(certificate.issued_at) }}
                      </div>
                      <div v-if="certificate.expires_at" class="text-xs text-gray-500 mt-1">
                        Expires: {{ formatDate(certificate.expires_at) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ certificate.metadata.score }}%
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="statusClass(certificate)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ statusText(certificate) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <Link
                          :href="route('certificates.show', certificate.id)"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          View
                        </Link>
                        <button
                          @click="downloadCertificate(certificate)"
                          class="text-blue-600 hover:text-blue-900"
                        >
                          Download
                        </button>
                        <button
                          @click="shareCertificate(certificate)"
                          class="text-green-600 hover:text-green-900"
                        >
                          Share
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="certificates.data.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                      No certificates found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
  
            <!-- Pagination -->
            <div v-if="certificates.meta && certificates.meta.total > certificates.meta.per_page" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
              <Pagination :links="certificates.links" />
            </div>
          </div>
        </div>
      </div>
  
      <!-- Share Modal -->
      <Modal :show="showShareModal" @close="showShareModal = false" max-width="md">
        <div class="p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Share Certificate
          </h2>
          <div class="mb-4">
            <InputLabel for="shareLink" value="Shareable Link" />
            <div class="mt-1 flex rounded-md shadow-sm">
              <input
                id="shareLink"
                ref="shareLinkInput"
                type="text"
                readonly
                :value="shareLink"
                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
              <button
                @click="copyShareLink"
                class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
              >
                <ClipboardDocumentIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
          <div class="flex justify-end">
            <button
              @click="showShareModal = false"
              class="btn btn-secondary"
            >
              Close
            </button>
          </div>
        </div>
      </Modal>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref, watch, computed } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import TextInput from '@/Components/TextInput.vue';
  import Pagination from '@/Components/Pagination.vue';
  import Modal from '@/Components/Modal.vue';
  import SortIcon from '@/Components/SortIcon.vue';
  import { ClipboardDocumentIcon } from '@heroicons/vue/24/outline';
  
  const props = defineProps({
    certificates: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({
        search: '',
        quiz_type: '',
        date_from: '',
        date_to: ''
      })
    },
    sortField: {
      type: String,
      default: 'issued_at'
    },
    sortDirection: {
      type: String,
      default: 'desc'
    }
  });
  
  const quizTypes = [
    { value: 'test', label: 'Test' },
    { value: 'exam', label: 'Exam' },
    { value: 'practice', label: 'Practice' },
    { value: 'survey', label: 'Survey' }
  ];
  
  const filters = ref({ ...props.filters });
  const sortField = ref(props.sortField);
  const sortDirection = ref(props.sortDirection);
  const showShareModal = ref(false);
  const shareLink = ref('');
  const shareLinkInput = ref(null);
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };
  
  const statusText = (certificate) => {
    if (certificate.expires_at && new Date(certificate.expires_at) < new Date()) {
      return 'Expired';
    }
    return 'Valid';
  };
  
  const statusClass = (certificate) => {
    if (certificate.expires_at && new Date(certificate.expires_at) < new Date()) {
      return 'bg-red-100 text-red-800';
    }
    return 'bg-green-100 text-green-800';
  };
  
  const sort = (field) => {
    if (sortField.value === field) {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortField.value = field;
      sortDirection.value = 'asc';
    }
    router.get(route('certificates.index'), {
      sort_field: sortField.value,
      sort_direction: sortDirection.value,
      ...filters.value
    }, {
      preserveState: true,
      replace: true
    });
  };
  
  const applyFilters = () => {
    router.get(route('certificates.index'), {
      ...filters.value,
      sort_field: sortField.value,
      sort_direction: sortDirection.value
    }, {
      preserveState: true,
      replace: true
    });
  };
  
  const resetFilters = () => {
    filters.value = {
      search: '',
      quiz_type: '',
      date_from: '',
      date_to: ''
    };
    applyFilters();
  };
  
  const downloadCertificate = (certificate) => {
    window.location.href = route('certificates.download', certificate.id);
  };
  
  const shareCertificate = (certificate) => {
    const url = new URL(route('certificates.show', certificate.id), window.location.origin);
    shareLink.value = url.toString();
    showShareModal.value = true;
  };
  
  const copyShareLink = () => {
    shareLinkInput.value.select();
    document.execCommand('copy');
    // Show copied notification
  };
  
  const exportToCSV = () => {
    const headers = ['Certificate Number', 'Quiz', 'Score', 'Issued Date', 'Expiry Date', 'Status', 'Link'];
    const rows = props.certificates.data.map(certificate => [
      certificate.certificate_number,
      certificate.quiz.title,
      `${certificate.metadata.score}%`,
      formatDate(certificate.issued_at),
      certificate.expires_at ? formatDate(certificate.expires_at) : 'Never',
      statusText(certificate),
      new URL(route('certificates.show', certificate.id), window.location.origin).toString()
    ]);
  
    let csvContent = "data:text/csv;charset=utf-8," 
      + headers.join(",") + "\n" 
      + rows.map(row => row.join(",")).join("\n");
  
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "certificates_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };
  
  // Watch for changes in filters with debounce
  let debounceTimer;
  watch(filters, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 500);
  }, { deep: true });
  </script>
  
  <style scoped>
  .cursor-pointer {
    cursor: pointer;
  }
  </style>