<template>
  <AppLayout :title="`Edit Certificate Template: ${template.name}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Edit Certificate Template: {{ template.name }}
        </h2>
        <Link 
          :href="route('examiner.certificate-templates.index')"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors duration-150"
        >
          <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Templates
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="mb-4">
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $page.props.flash.success }}
          </div>
        </div>
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
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 gap-6">
                <!-- Basic Information -->
                <div class="space-y-6">
                  <h3 class="text-lg font-medium">Template Information</h3>
                  
                  <div>
                    <InputLabel for="name" value="Template Name *" />
                    <TextInput
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                      required
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                  </div>

                  <div>
                    <InputLabel for="description" value="Description" />
                    <Textarea
                      id="description"
                      v-model="form.description"
                      class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                      rows="3"
                    />
                    <InputError class="mt-2" :message="form.errors.description" />
                  </div>

                  <div class="flex items-center">
                    <Checkbox id="is_default" v-model:checked="form.is_default" />
                    <InputLabel for="is_default" value="Set as default template" class="ml-2" />
                    <InputError class="mt-2" :message="form.errors.is_default" />
                  </div>
                </div>

                <!-- Background Image -->
                <div class="space-y-6">
                  <h3 class="text-lg font-medium">Design</h3>
                  
                  <div>
                    <InputLabel for="background_image" value="Background Image" />
                    <p class="text-sm text-gray-500 mb-2">
                      Recommended size: 3508×2480 pixels (A4 landscape at 300dpi)
                    </p>
                    
                    <!-- Current Image Preview -->
                    <div v-if="currentBackground" class="mb-4">
                      <p class="text-sm font-medium text-gray-700 mb-1">Current Background:</p>
                      <img 
                        :src="currentBackground" 
                        class="max-w-full h-48 object-contain border border-gray-200 rounded-lg shadow-sm" 
                        alt="Current background"
                      >
                      <button
                        type="button"
                        @click="removeBackgroundImage"
                        class="mt-2 inline-flex items-center text-sm text-red-600 hover:text-red-800 transition-colors duration-150"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Remove Background Image
                      </button>
                    </div>
                    
                    <!-- New Image Upload -->
                    <input
                      id="background_image"
                      type="file"
                      class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-green-50 file:text-green-700
                            hover:file:bg-green-100
                            transition-colors duration-200"
                      accept="image/jpeg,image/png,image/jpg"
                      @change="handleFileChange"
                    />
                    <InputError class="mt-2" :message="form.errors.background_image" />
                    
                    <!-- New Image Preview -->
                    <div v-if="previewImage" class="mt-4">
                      <p class="text-sm font-medium text-gray-700 mb-1">New Background Preview:</p>
                      <img :src="previewImage" class="max-w-full h-48 object-contain border border-gray-200 rounded-lg shadow-sm" alt="Preview">
                    </div>
                  </div>
                </div>

                <!-- Template Content -->
                <div class="space-y-6">
                  <h3 class="text-lg font-medium">Template Content</h3>
                  
                  <div>
                    <InputLabel value="HTML Content *" />
                    <RichTextEditor
                      v-model="form.content"
                      class="mt-1"
                      :error="form.errors.content"
                    />
                    <InputError class="mt-2" :message="form.errors.content" />
                  </div>

                  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-2">Available Variables:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                      <div v-for="(varDesc, varName) in templateVariables" :key="varName" class="flex items-start">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-mono mr-2">{{ varName }}</span>
                        <span class="text-gray-600">{{ varDesc }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Template Preview -->
                  <div class="mt-4 p-4 bg-gray-100 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-2">Landscape Template Preview:</h4>
                    <div class="certificate-preview" v-html="form.content"></div>
                  </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-6">
                  <button
                    type="button"
                    @click="previewTemplate"
                    :disabled="form.processing || !form.content"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing || !form.content }"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview Full Template
                  </button>

                  <div class="space-x-2">
                    <button
                      type="button"
                      @click="confirmDelete"
                      :disabled="form.processing"
                      :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                      class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition-colors duration-200"
                    >
                      <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Delete Template
                    </button>
                    <button 
                      type="submit" 
                      :disabled="form.processing"
                      class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200"
                    >
                      <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span v-if="form.processing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Updating...
                      </span>
                      <span v-else>Update Template</span>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <Modal :show="showPreview" @close="showPreview = false" max-width="5xl">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Template Preview: {{ form.name }}
        </h2>
        <div class="certificate-preview-large" v-html="previewContent"></div>
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
        Are you sure you want to delete "{{ form.name }}"?
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
          Delete Template
        </button>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Checkbox from '@/Components/Checkbox.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
  template: {
    type: Object,
    required: true
  },
  organization: Object
});

const currentLogoUrl = computed(() => {
  return props.organization?.logo 
      ? `/storage/${props.organization.logo}`
      : '/images/default-organization-logo.png';
});

const currentSealUrl = computed(() => {
  return props.organization?.certificate_seal 
      ? `/storage/${props.organization.certificate_seal}`
      : '/images/default-seal.png';
});

const templateVariables = {
  '{{user.name}}': 'Full name of the certificate recipient',
  '{{user.email}}': 'Email of the recipient',
  '{{quiz.title}}': 'Title of the quiz',
  '{{quiz.description}}': 'Description of the quiz',
  '{{certificate.number}}': 'Certificate serial number',
  '{{certificate.issued_at}}': 'Issue date (formatted)',
  '{{certificate.expires_at}}': 'Expiry date (formatted)',
  '{{score}}': 'Percentage score achieved',
  '{{date}}': 'Current date (formatted)',
  '{{organization.name}}': 'Organization name',
  '{{organization.logo}}': 'Organization logo URL',
  '{{organization.seal}}': 'Organization seal/stamp URL',
};

const form = useForm({
  name: props.template.name,
  description: props.template.description,
  content: props.template.content,
  background_image: null,
  is_default: props.template.is_default,
});

const currentBackground = computed(() => {
  return props.template.settings?.background_image 
    ? `/storage/${props.template.settings.background_image}`
    : null;
});

const previewImage = ref(null);
const showPreview = ref(false);
const previewContent = ref('');
const showDeleteModal = ref(false);

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.background_image = file;
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeBackgroundImage = () => {
  form.background_image = 'REMOVE';
  previewImage.value = null;
};

const previewTemplate = () => {
  previewContent.value = form.content;
  showPreview.value = true;
};

const submit = () => {
  form.put(route('examiner.certificate-templates.update', props.template.id), {
    preserveScroll: true,
    onSuccess: () => {
      if (form.background_image !== 'REMOVE') {
        form.background_image = null;
        previewImage.value = null;
      }
    }
  });
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const deleteTemplate = () => {
  router.delete(route('examiner.certificate-templates.destroy', props.template.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('examiner.certificate-templates.index'));
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

/* Green-themed file input */
.file\:bg-green-50::file-selector-button {
    background-color: #ecfdf5;
}

.file\:text-green-700::file-selector-button {
    color: #065f46;
}

.hover\:file\:bg-green-100:hover::file-selector-button {
    background-color: #d1fae5;
}

/* Green-themed variables */
.bg-green-100 {
    background-color: #ecfdf5;
}

.text-green-800 {
    color: #065f46;
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