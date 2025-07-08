<script>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { Link, useForm, usePage } from '@inertiajs/vue3';
  import { watch } from 'vue';
  import MultipleChoiceEditor from '@/Components/Questions/MultipleChoiceEditor.vue';
  import TrueFalseEditor from '@/Components/Questions/TrueFalseEditor.vue';
  import ShortAnswerEditor from '@/Components/Questions/ShortAnswerEditor.vue';
  import EssayEditor from '@/Components/Questions/EssayEditor.vue';
  import FillInTheBlankEditor from '@/Components/Questions/FillInTheBlankEditor.vue';
  import MatchingEditor from '@/Components/Questions/MatchingEditor.vue';
  import OrderingEditor from '@/Components/Questions/OrderingEditor.vue';
  import InputError from '@/Components/InputError.vue';

  export default {
    components: {
      AppLayout,
      Link,
      MultipleChoiceEditor,
      TrueFalseEditor,
      ShortAnswerEditor,
      EssayEditor,
      FillInTheBlankEditor,
      MatchingEditor,
      OrderingEditor,
      InputError,
    },
    props: {
      quiz: Object,
      questionTypes: Object,
      question: {
        type: Object,
        required: true
      },
    },
    data() {
      return {
        form: useForm({
          type: this.question.type || 'multiple_choice',
          question: this.question.question || '',
          description: this.question.description || '',
          image: null, // For new image upload
          points: this.question.points || 1,
          time_limit: this.question.time_limit || null,
          is_required: this.question.is_required || false,
          options: this.question.options || null,
          correct_answers: this.question.correct_answers || [],
          settings: {
            rubric: this.question.settings?.rubric || [],
            distractors: this.question.settings?.distractors || [],
            ...this.question.settings
          },
        }),
        imagePreview: this.question.image || null, // For displaying current/preview image
        removeImage: false, // Flag to remove existing image
        originalImageUrl: this.question.image || null, // Store original image URL
      };
    },
    computed: {
      editing() {
        return true; // Always true for edit page
      },
      hasExistingImage() {
        return !!this.originalImageUrl;
      },
    },
    methods: {
      handleImageChange(event) {
        const file = event.target.files[0];
        if (file) {
          // Validate file type
          const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
          if (!allowedTypes.includes(file.type)) {
            alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
            event.target.value = '';
            return;
          }

          // Validate file size (5MB limit)
          const maxSize = 5 * 1024 * 1024; // 5MB in bytes
          if (file.size > maxSize) {
            alert('Image size must be less than 5MB');
            event.target.value = '';
            return;
          }

          this.form.image = file;
          
          // Create preview
          const reader = new FileReader();
          reader.onload = (e) => {
            this.imagePreview = e.target.result;
          };
          reader.readAsDataURL(file);
          
          this.removeImage = false;
        }
      },
      removeCurrentImage() {
        this.form.image = null;
        this.imagePreview = null;
        this.removeImage = true;
        // Clear the file input
        const fileInput = this.$refs.imageInput;
        if (fileInput) {
          fileInput.value = '';
        }
      },
      resetToOriginalImage() {
        this.form.image = null;
        this.imagePreview = this.originalImageUrl;
        this.removeImage = false;
        // Clear the file input
        const fileInput = this.$refs.imageInput;
        if (fileInput) {
          fileInput.value = '';
        }
      },
      submitForm() {
        // Add remove_image flag to form data if needed
        if (this.removeImage) {
          this.form.remove_image = true;
        }

        this.form.put(route('examiner.quizzes.questions.update', {
          quiz: this.quiz.id, 
          question: this.question.id
        }), {
          preserveScroll: true,
          onSuccess: () => {
            // Optionally redirect to questions index or show success message
            this.$inertia.visit(route('examiner.quizzes.questions.index', this.quiz.id));
          },
          onError: (errors) => {
            console.log('Validation errors:', errors);
          }
        });
      },
      cancel() {
        this.$inertia.visit(route('examiner.quizzes.questions.index', this.quiz.id));
      },
    },
    mounted() {
      // Ensure we have the correct initial state for existing questions
      if (this.question.settings) {
        this.form.settings = {
          rubric: this.question.settings.rubric || [],
          distractors: this.question.settings.distractors || [],
          ...this.question.settings
        };
      }
    }
  };
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
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              
              <!-- Breadcrumb or back link -->
              <div class="mb-6">
                <Link 
                  :href="route('examiner.quizzes.questions.index', quiz.id)" 
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  ← Back to Questions
                </Link>
              </div>

              <form @submit.prevent="submitForm">
                
                <div class="grid grid-cols-1 gap-6">
                  <!-- Question Type -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Question Type</label>
                    <select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                      <option v-for="(label, value) in questionTypes" :key="value" :value="value">{{ label }}</option>
                    </select>
                    <InputError :message="form.errors.type" class="mt-2" />
                  </div>
  
                  <!-- Question Text -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Question Text</label>
                    <textarea v-model="form.question" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <InputError :message="form.errors.question" class="mt-2" />
                  </div>
  
                  <!-- Description -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
                    <textarea v-model="form.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                  </div>

                  <!-- Image Upload -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Question Image (optional)</label>
                    <div class="mt-1">
                      <!-- Current/Preview Image -->
                      <div v-if="imagePreview" class="mb-4">
                        <div class="relative inline-block">
                          <img :src="imagePreview" alt="Question image" class="max-w-xs max-h-48 rounded-lg border border-gray-300">
                          <button
                            type="button"
                            @click="removeCurrentImage"
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
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
                          class="mt-2 text-xs text-blue-600 hover:text-blue-800"
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
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
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
                    <input v-model="form.points" type="number" min="0" step="0.5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <InputError :message="form.errors.points" class="mt-2" />
                  </div>
  
                  <!-- Time Limit -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Time Limit (seconds, optional)</label>
                    <input v-model="form.time_limit" type="number" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <InputError :message="form.errors.time_limit" class="mt-2" />
                  </div>
  
                  <!-- Required -->
                  <div>
                    <label class="inline-flex items-center">
                      <input v-model="form.is_required" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                      <span class="ml-2 text-sm text-gray-600">Required Question</span>
                    </label>
                    <InputError :message="form.errors.is_required" class="mt-2" />
                  </div>
  
                  <!-- Dynamic fields based on question type -->
                  <div v-if="form.type === 'multiple_choice'">
                    <MultipleChoiceEditor v-model="form" />
                  </div>
  
                  <div v-if="form.type === 'true_false'">
                    <TrueFalseEditor v-model="form" />
                  </div>

                  <div v-if="form.type === 'short_answer'">
                    <ShortAnswerEditor v-model="form" />
                  </div>

                  <div v-if="form.type === 'essay'">
                    <EssayEditor v-model="form" />
                  </div>

                  <div v-if="form.type === 'fill_in_blank'">
                    <FillInTheBlankEditor v-model="form" />
                  </div>

                  <div v-if="form.type === 'matching'">
                    <MatchingEditor v-model="form" />
                  </div>

                  <div v-if="form.type === 'ordering'">
                    <OrderingEditor v-model="form" />
                  </div>

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
  
                  <div class="flex justify-end">
                    <button type="button" @click="cancel" class="mr-3 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                      Cancel
                    </button>
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                      <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Update Question
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