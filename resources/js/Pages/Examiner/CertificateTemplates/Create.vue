<template>
  <AppLayout title="Create Certificate Template">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Create New Certificate Template
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
                      autofocus
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
                      placeholder="Brief description of this template..."
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
                    <p class="text-sm text-gray-500 mb-2">Recommended size: 3508×2480 pixels (A4 at 300dpi)</p>
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
                    
                    <div v-if="previewImage" class="mt-4">
                      <p class="text-sm font-medium text-gray-700 mb-1">Preview:</p>
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
                </div>

                <!-- Preview & Submit -->
                <div class="flex justify-between items-center pt-6">
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
                    Preview Template
                  </button>

                  <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200"
                  >
                    <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span v-if="form.processing" class="flex items-center">
                      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Creating...
                    </span>
                    <span v-else>Create Template</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <Modal :show="showPreview" @close="showPreview = false" max-width="4xl">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Template Preview
        </h2>
        <div class="relative" style="width: 29.7cm; height: 21cm; margin: 0 auto;">
          <!-- Background Image Container -->
          <div 
            v-if="previewImage" 
            class="absolute inset-0 z-0"
            :style="{
              backgroundImage: `url(${previewImage})`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
              backgroundRepeat: 'no-repeat',
              opacity: 0.15
            }">
          </div>
          
          <!-- Certificate Content -->
          <div 
            class="relative z-10 h-full overflow-hidden"
            v-html="previewContent">
          </div>
        </div>
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
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Checkbox from '@/Components/Checkbox.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import Modal from '@/Components/Modal.vue';

const page = usePage();
const props = defineProps({
  user: Object,
  organization: Object, 
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
};

const landscapeTemplateExample = `
<div class="certificate-landscape" 
   style="width: 29.7cm; height: 21cm; margin: 0 auto; position: relative; font-family: 'Times New Roman', serif; background: #fdfdfd; ; box-shadow: 0 0 20px rgba(0,0,0,0.1);">

<!-- Background Watermark -->
<div v-if="backgroundImage" class="background-image" 
     style="background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.15; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;">
</div>

<!-- Certificate Content -->
<div class="content" style="position: relative; z-index: 10; padding: 2cm; height: 100%; text-align: center;">

  <!-- Logo -->
  <div class="logo" style="display: flex; justify-content: center; margin-bottom: 1cm;">
    <img src="${currentLogoUrl.value}" style="max-height: 90px;" alt="Organization Logo">
  </div>

  <!-- Main Title -->
  <h1 style="font-size: 40pt; color: #1a365d; margin-bottom: 0.3cm; letter-spacing: 3px; text-transform: uppercase; font-weight: bold;">
    Certificate of Achievement
  </h1>
  <hr style="width: 40%; margin: 0.5cm auto 1.5cm auto; border: 1px solid #cbd5e0;">

  <!-- Certification Text -->
  <p style="font-size: 14pt; color: #4a5568; margin-bottom: 0.5cm;">This is proudly presented to</p>

  <!-- Recipient -->
  <h2 style="font-size: 30pt; color: #2b6cb0; margin-bottom: 0.8cm; border-bottom: 3px solid #2b6cb0; display: inline-block; padding: 0 1cm 0.3cm 1cm; font-weight: bold;">
    {{user.name}}
  </h2>

  <!-- Achievement Statement -->
  <p style="font-size: 14pt; color: #4a5568; margin-bottom: 0.5cm;">for successfully completing</p>

  <!-- Course Title -->
  <h3 style="font-size: 22pt; color: #1a365d; margin-bottom: 1cm; font-weight: 600;">
    {{quiz.title}}
  </h3>

  <!-- Details -->
  <div style="font-size: 11pt; color: #2d3748; margin-bottom: 2cm; line-height: 1.6;">
    <p>Completed on <strong>{{date}}</strong></p>
    <p>Final Score: <strong>{{score}}%</strong></p>
    <p>Certificate ID: <strong>{{certificate.number}}</strong></p>
  </div>

  <!-- Footer (Signatures + Seal) -->
  <div style="position: absolute; bottom: 2cm; left: 2cm; right: 2cm; display: flex; justify-content: space-between; align-items: flex-end;">
    
    <!-- Instructor Signature -->
    <div style="width: 40%; text-align: center;">
      <div style="border-top: 1px solid #4a5568; width: 70%; margin: 0 auto; padding-top: 0.3cm;"></div>
      <p style="margin-top: 0.2cm; font-size: 10pt; color: #4a5568;">Instructor / Supervisor</p>
    </div>

    <!-- Seal -->
    <div style="width: 100px; text-align: center;">
      <img src="${currentSealUrl.value}" style="width: 100px; opacity: 0.85;" alt="Organization Seal">
    </div>

    <!-- Date Signature -->
    <div style="width: 40%; text-align: center;">
      <div style="border-top: 1px solid #4a5568; width: 70%; margin: 0 auto; padding-top: 0.3cm;"></div>
      <p style="margin-top: 0.2cm; font-size: 10pt; color: #4a5568;">Date</p>
    </div>
  </div>
</div>
</div>
`;

const form = useForm({
  name: '',
  description: '',
  content: landscapeTemplateExample,
  background_image: null,
  is_default: false,
});

const previewImage = ref(null);
const showPreview = ref(false);
const previewContent = ref('');

const previewBgStyle = computed(() => {
  if (previewImage.value) {
    return {
      backgroundImage: `url(${previewImage.value})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat'
    };
  }
  return {};
});

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
  } else {
    previewImage.value = null;
  }
};

watch(
  () => form.content,
  (newContent) => {
    if (showPreview.value) {
      previewContent.value = processTemplateContent(newContent);
    }
  }
);

const processTemplateContent = (content) => {
  let processed = content;
  
  // Replace variables
  processed = processed
    .replace(/\{\{currentLogoUrl\}\}/g, currentLogoUrl.value)
    .replace(/\{\{currentSealUrl\}\}/g, currentSealUrl.value);
  
  // Remove Vue-specific directives that don't work in innerHTML
  processed = processed
    .replace(/v-if="[^"]*"/g, '')
    .replace(/:style="[^"]*"/g, '');
  
  return processed;
};

const previewTemplate = () => {
  let processedContent = form.content;
  
  // Replace template variables
  processedContent = processedContent
    .replace(/\{\{currentLogoUrl\}\}/g, currentLogoUrl.value)
    .replace(/\{\{currentSealUrl\}\}/g, currentSealUrl.value);
  
  // Clean up Vue directives
  processedContent = processedContent
    .replace(/v-if="[^"]*"/g, '')
    .replace(/:style="[^"]*"/g, '');
  
  previewContent.value = processedContent;
  showPreview.value = true;
};

const submit = () => {
  form.post(route('examiner.certificate-templates.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    },
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