<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import MultipleChoiceEditor from '@/Components/Questions/MultipleChoiceEditor.vue';
import TrueFalseEditor from '@/Components/Questions/TrueFalseEditor.vue';
import ShortAnswerEditor from '@/Components/Questions/ShortAnswerEditor.vue';
import EssayEditor from '@/Components/Questions/EssayEditor.vue';
import FillInTheBlankEditor from '@/Components/Questions/FillInTheBlankEditor.vue';
import MatchingEditor from '@/Components/Questions/MatchingEditor.vue';
import OrderingEditor from '@/Components/Questions/OrderingEditor.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  quiz: Object || null,
  questionTypes: Object,
  pool: Object || null,
  question: {
    type: Object,
    required: true
  },
});

const form = useForm({
  is_pool: props.pool?.id || null,
  type: props.question.type || 'multiple_choice',
  question: props.question.question || '',
  description: props.question.description || '',
  image: null,
  points: props.question.points || 1,
  time_limit: props.question.time_limit || null,
  is_required: props.question.is_required || false,
  options: props.question.options || [''],
  correct_answers: props.question.correct_answers || [],
  settings: {
    rubric: props.question.settings?.rubric || [],
    distractors: props.question.settings?.distractors || [],
    ...props.question.settings
  },
});

const forceRerender = ref(0);
const imagePreview = ref(props.question.image || null);
const removeImage = ref(false);
const originalImageUrl = ref(props.question.image || null);

const editing = computed(() => true);
const hasExistingImage = computed(() => !!originalImageUrl.value);

function handleImageChange(event) {
  const file = event.target.files[0];
  if (file) {
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
      alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
      event.target.value = '';
      return;
    }

    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
      alert('Image size must be less than 5MB');
      event.target.value = '';
      return;
    }

    form.image = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
    
    removeImage.value = false;
  }
}

function removeCurrentImage() {
  form.image = null;
  imagePreview.value = null;
  removeImage.value = true;
  
  const fileInput = imageInput.value;
  if (fileInput) {
    fileInput.value = '';
  }
}

function resetToOriginalImage() {
  form.image = null;
  imagePreview.value = originalImageUrl.value;
  removeImage.value = false;
  
  const fileInput = imageInput.value;
  if (fileInput) {
    fileInput.value = '';
  }
}

function submitForm() {
  if (removeImage.value) {
    form.remove_image = true;
  }

  
  if (form.is_pool === null) {
    form.put(route('examiner.quizzes.questions.update', {
    quiz: props.quiz.id, 
    question: props.question.id
    }), {
      preserveScroll: true,
      onSuccess: () => {
        router.visit(route('examiner.quizzes.questions.index', props.quiz.id));
      },
      onError: (errors) => {
        console.log('Validation errors:', errors);
      }
    });
  }else{
    form.put(route('examiner.pools.questions.update', {pool: props.pool.id, 
    question: props.question.id}))
  }


}


function cancel() {
  if (form.is_pool === null) {
    router.visit(route('examiner.quizzes.questions.index', props.quiz.id));
  }else{
    router.visit(route('examiner.question-pools.manage-questions', props.pool.id));
  }
  
}

onMounted(() => {
  if (props.question.settings) {
    form.settings = {
      rubric: props.question.settings.rubric || [],
      distractors: props.question.settings.distractors || [],
      ...props.question.settings
    };
  }
});

watch(() => form.type, (newType, oldType) => {
  if (newType !== oldType) {
    forceRerender.value++
  }
});

const imageInput = ref(null);
</script>

<template>
  <AppLayout title="Edit Question">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Question for {{ quiz.title }}
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            
            <!-- Breadcrumb or back link -->
            <div  v-if = "props.pool === null" class="mb-6">
              <Link 
                :href="route('examiner.quizzes.questions.index', quiz.id)" 
                class="inline-flex items-center text-green-600 hover:text-green-800 text-sm font-medium transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                ← Back to Questions
              </Link>
            </div>
            <div  v-if = "props.pool.lenght > 0" class="mb-6">
              <Link 
                :href="route('examiner.question-pools.manage-questions', props.pool.id)" 
                class="inline-flex items-center text-green-600 hover:text-green-800 text-sm font-medium transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                ← Back to Pool
              </Link>
            </div>

            <form @submit.prevent="submitForm">
              
              <div class="grid grid-cols-1 gap-6">
                <!-- Question Type -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Question Type</label>
                  <select 
                    v-model="form.type" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                  >
                    <option v-for="(label, value) in questionTypes" :key="value" :value="value">{{ label }}</option>
                  </select>
                  <InputError :message="form.errors.type" class="mt-2" />
                </div>

                <!-- Question Text -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Question Text</label>
                  <textarea 
                    v-model="form.question" 
                    rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                  ></textarea>
                  <InputError :message="form.errors.question" class="mt-2" />
                </div>

                <!-- Description -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
                  <textarea 
                    v-model="form.description" 
                    rows="2" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                  ></textarea>
                  <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <!-- Image Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Question Image (optional)</label>
                  <div class="mt-1">
                    <!-- Current/Preview Image -->
                    <div v-if="imagePreview" class="mb-4">
                      <div class="relative inline-block">
                        <img :src="imagePreview" alt="Question image" class="max-w-xs max-h-48 rounded-lg border border-gray-300 shadow-sm">
                        <button
                          type="button"
                          @click="removeCurrentImage"
                          class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                      <p class="text-sm text-gray-500 mt-2">
                        {{ form.image ? 'New image preview' : 'Current image' }}
                      </p>
                      
                      <!-- Reset to original button if we have an original image and user uploaded a new one -->
                      <button
                        v-if="hasExistingImage && form.image"
                        type="button"
                        @click="resetToOriginalImage"
                        class="mt-2 text-xs text-green-600 hover:text-green-800 transition-colors duration-200"
                      >
                        Reset to original image
                      </button>
                    </div>

                    <!-- File Input -->
                    <input
                      ref="imageInput"
                      type="file"
                      accept="image/*"
                      @change="handleImageChange"
                      class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-colors duration-200"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                      Supported formats: JPEG, PNG, GIF, WebP. Maximum size: 5MB.
                      {{ hasExistingImage ? 'Leave empty to keep current image.' : '' }}
                    </p>
                  </div>
                  <InputError :message="form.errors.image" class="mt-2" />
                </div>

                <!-- Points -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Points</label>
                  <input 
                    v-model="form.points" 
                    type="number" 
                    min="0" 
                    step="0.5" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                  >
                  <InputError :message="form.errors.points" class="mt-2" />
                </div>

                <!-- Time Limit -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Time Limit (seconds, optional)</label>
                  <input 
                    v-model="form.time_limit" 
                    type="number" 
                    min="0" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                  >
                  <InputError :message="form.errors.time_limit" class="mt-2" />
                </div>

                <!-- Required -->
                <div>
                  <label class="inline-flex items-center">
                    <input 
                      v-model="form.is_required" 
                      type="checkbox" 
                      class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                    >
                    <span class="ml-2 text-sm text-gray-600">Required Question</span>
                  </label>
                  <InputError :message="form.errors.is_required" class="mt-2" />
                </div>

                <!-- Dynamic fields based on question type -->
                <MultipleChoiceEditor 
                  v-if="form.type === 'multiple_choice'" 
                  v-model="form" 
                  :key="'multiple_choice-' + forceRerender" 
                />
                <TrueFalseEditor 
                  v-if="form.type === 'true_false'" 
                  v-model="form" 
                  :key="'true_false-' + forceRerender" 
                />
                <ShortAnswerEditor 
                  v-if="form.type === 'short_answer'" 
                  v-model="form" 
                  :key="'short_answer-' + forceRerender" 
                />
                <EssayEditor 
                  v-if="form.type === 'essay'" 
                  v-model="form" 
                  :key="'essay-' + forceRerender" 
                />
                <FillInTheBlankEditor 
                  v-if="form.type === 'fill_in_the_blank'" 
                  v-model="form" 
                  :key="'fill_in_the_blank-' + forceRerender" 
                />
                <MatchingEditor 
                  v-if="form.type === 'matching'" 
                  v-model="form" 
                  :key="'matching-' + forceRerender" 
                />
                <OrderingEditor 
                  v-if="form.type === 'ordering'" 
                  v-model="form" 
                  :key="'ordering-' + forceRerender" 
                />

                <!-- Display any nested validation errors -->
                <div v-if="form.errors.options">
                  <InputError :message="form.errors.options" class="mt-2" />
                </div>
                
                <div v-if="form.errors.correct_answers">
                  <InputError :message="form.errors.correct_answers" class="mt-2" />
                </div>

                <div v-if="form.errors.settings">
                  <InputError :message="form.errors.settings" class="mt-2" />
                </div>

                <div class="flex justify-end pt-4">
                  <button 
                    type="button" 
                    @click="cancel" 
                    class="mr-3 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                  >
                    Cancel
                  </button>
                  <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200 flex items-center"
                  >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-if="form.processing">Updating...</span>
                    <span v-else>Update Question</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

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

/* Green-themed checkbox */
input[type="checkbox"]:checked {
    background-color: #10B981;
    border-color: #10B981;
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