<template>
  <AppLayout title="Certificate Templates">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Certificate Templates
        </h2>
        <button 
          @click="createCertTemplate"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200"
        >
          <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Create New Template
        </button>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success Message -->
        <div v-if="$page.props.flash.success" class="mb-4">
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $page.props.flash.success }}
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="$page.props.flash.error" class="mb-4">
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $page.props.flash.error }}
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Search and Filter -->
            <div class="mb-6 flex justify-between items-center">
              <div class="w-1/3">
                <TextInput
                  v-model="search"
                  type="text"
                  placeholder="Search templates..."
                  class="w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                />
              </div>
            </div>

            <!-- Templates Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Description
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Default
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Created
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="template in filteredTemplates" :key="template.id" class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">
                          {{ template.name }}
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 truncate max-w-xs">
                        {{ template.description }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span v-if="template.is_default" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Default
                      </span>
                      <button
                        v-else
                        @click="setAsDefault(template)"
                        class="inline-flex items-center text-sm text-green-600 hover:text-green-800 transition-colors duration-150"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Set as Default
                      </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(template.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <Link
                          :href="route('examiner.certificate-templates.edit', template.id)"
                          class="inline-flex items-center text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                        >
                          <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                          Edit
                        </Link>
                        <button
                          @click="previewTemplate(template)"
                          class="inline-flex items-center text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                        >
                          <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                          Preview
                        </button>
                        <button
                          @click="confirmDelete(template)"
                          class="inline-flex items-center text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                        >
                          <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredTemplates.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                      No templates found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="templates.meta && templates.meta.total > templates.meta.per_page" class="mt-4">
              <Pagination :links="templates.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <Modal :show="showPreview" @close="showPreview = false" max-width="4xl">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Preview: {{ previewTemplateData.name }}
        </h2>
        <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm" v-html="previewTemplateData.content"></div>
        <div class="mt-6 flex justify-end">
          <button
            @click="showPreview = false"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
          >
            Close
          </button>
        </div>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Delete Template
      </template>
      <template #content>
        Are you sure you want to delete "{{ templateToDelete?.name }}"?
        This action cannot be undone.
      </template>
      <template #footer>
        <button 
          @click="showDeleteModal = false" 
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
        >
          Cancel
        </button>
        <button 
          @click="deleteTemplate" 
          class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition-colors duration-200 ml-2"
        >
          <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Delete
        </button>
      </template>
    </ConfirmationModal>
    
    <FeatureLimitModal 
      v-model="showModal"
      :featureName="modalFeature"
      :message="modalMessage"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { format } from 'date-fns';
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue'

const props = defineProps({
  templates: {
    type: Object,
    required: true,
    default: () => ({
      data: [],
      links: [],
      meta: {}
    })
  },
  canIssueCertificate: Boolean,
  defaultTemplate: {
    type: Object,
    default: null
  }
});

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

function createCertTemplate() {
  if (props.canIssueCertificate === false) {
    openFeatureModal(
      'Create Certificate Template',
      'Your current plan does not allow the creation of New Certificate Template.'
    )
    return
  }

  router.visit(route('examiner.certificate-templates.create'))
}

const search = ref('');
const showPreview = ref(false);
const previewTemplateData = ref({});
const showDeleteModal = ref(false);
const templateToDelete = ref(null);

const filteredTemplates = computed(() => {
  if (!search.value) {
    return props.templates.data;
  }
  return props.templates.data.filter(template => 
    template.name.toLowerCase().includes(search.value.toLowerCase()) ||
    (template.description && template.description.toLowerCase().includes(search.value.toLowerCase()))
  );
});

const formatDate = (dateString) => {
  return format(new Date(dateString), 'MMM d, yyyy');
};

const previewTemplate = (template) => {
  previewTemplateData.value = template;
  showPreview.value = true;
};

const confirmDelete = (template) => {
  templateToDelete.value = template;
  showDeleteModal.value = true;
};

const deleteTemplate = () => {
  router.delete(route('examiner.certificate-templates.destroy', templateToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
    }
  });
};

const setAsDefault = (template) => {
  router.post(route('examiner.certificate-templates.set-default', template.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['templates', 'defaultTemplate'] });
    }
  });
};
</script>

<style scoped>
/* Enhanced green-themed styling */
.bg-green-600 {
    background-color: #10B981;
}

.bg-green-700:hover {
    background-color: #059669;
}

.focus\:ring-green-500:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Green-themed text */
.text-green-600 {
    color: #10B981;
}

.text-green-800 {
    color: #065f46;
}

/* Green-themed backgrounds */
.bg-green-50 {
    background-color: #ecfdf5;
}

.bg-green-100 {
    background-color: #d1fae5;
}

/* Border styling */
.border-gray-100 {
    border-color: #f3f4f6;
}

/* Transition effects */
.transition-colors {
    transition: all 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .gap-6 > *:not(:last-child) {
        margin-bottom: 1.5rem;
    }
}
</style>