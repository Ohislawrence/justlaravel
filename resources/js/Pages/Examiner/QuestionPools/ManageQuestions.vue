<template>
  <AppLayout>
    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Question Pool Management</h1>
          <p class="text-sm text-gray-600 mt-1">
            Managing questions for: <span class="font-medium text-indigo-600">{{ pool.name }}</span>
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
          <Link 
            :href="route('examiner.question-pools.ai-generator', { questionPool: pool.id })"
            class="btn-primary inline-flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            Generate with AI
          </Link>
          <Link 
            :href="route('examiner.pools.questions.create', pool.id)"
            class="btn-secondary inline-flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Question
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Current Questions Card -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
          <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 class="text-xl font-semibold text-gray-900">Current Questions</h2>
                <p class="text-sm text-gray-600">Questions currently in this pool</p>
              </div>
              <span class="badge-primary">
                {{ pool.questions.length }} {{ pool.questions.length === 1 ? 'question' : 'questions' }}
              </span>
            </div>
          </div>

          <div class="overflow-hidden">
            <div v-if="pool.questions.length > 0" class="divide-y divide-gray-100">
              <div v-for="question in pool.questions" :key="question.id" class="p-4 hover:bg-gray-50 transition-colors duration-150 group">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-3">
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                          <p class="text-gray-800 font-medium line-clamp-2" v-html="question.question"></p>
                          <span class="badge-question-type" :class="questionTypeClass(question.type)">
                            {{ formatQuestionType(question.type) }}
                          </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                          {{ question.points ?? 0 }} points
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <Link 
                      :href="route('examiner.pools.questions.edit', {pool: pool.id, question: question.id})"
                      class="btn-icon"
                      title="Edit question"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                      </svg>
                    </Link>
                    <button
                      @click="removeQuestion(question)"
                      class="btn-icon-danger"
                      title="Remove from pool"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-12">
              <div class="mx-auto h-24 w-24 text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="mt-4 text-lg font-medium text-gray-900">No questions in this pool</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by adding questions below</p>
              <div class="mt-6">
                <Link 
                  :href="route('examiner.pools.questions.create', pool.id)"
                  class="btn-primary inline-flex items-center gap-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                  </svg>
                  Create New Question
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Add Questions Card -->
        <div v-if="availableQuestions.length > 0" class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
          <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 class="text-xl font-semibold text-gray-900">Available Questions</h2>
                <p class="text-sm text-gray-600">Select questions to add to this pool</p>
              </div>
              <span class="badge-primary">
                {{ availableQuestions.length }} available
              </span>
            </div>
          </div>

          <div class="p-6">
            <div class="space-y-6">
              <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Questions</label>
                <div class="mt-1 space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-2">
                  <div v-for="question in availableQuestions" :key="question.id" class="flex items-start">
                    <div class="flex items-center h-5">
                      <input 
                        :id="`question-${question.id}`" 
                        v-model="selectedQuestions" 
                        :value="question.id" 
                        type="checkbox" 
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      >
                    </div>
                    <div class="ml-3 text-sm">
                      <label :for="`question-${question.id}`" class="font-medium text-gray-700 cursor-pointer">
                        <span class="inline-block mr-2 badge-question-type-sm" :class="questionTypeClass(question.type)">
                          {{ formatQuestionType(question.type) }}
                        </span>
                        <span v-html="question.question"></span>
                      </label>
                      <p class="text-gray-500 mt-1">{{ question.points ?? 0 }} points</p>
                    </div>
                  </div>
                </div>
              </div>

              <button
                @click="addQuestions"
                :disabled="selectedQuestions.length === 0"
                class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2"
                :class="{ 'opacity-50 cursor-not-allowed': selectedQuestions.length === 0 }"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Selected Questions
                <span v-if="selectedQuestions.length > 0" class="badge-count">
                  {{ selectedQuestions.length }}
                </span>
              </button>
            </div>
          </div>
        </div>

        <div v-else class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
          <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <h2 class="text-xl font-semibold text-gray-900">Available Questions</h2>
          </div>
          <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No available questions</h3>
            <p class="mt-1 text-sm text-gray-500">All questions are already in this pool or none exist yet.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  pool: Object,
  availableQuestions: Array,
});

const selectedQuestions = ref([]);

const formatQuestionType = (type) => {
  const types = {
    'multiple_choice': 'MCQ',
    'true_false': 'True/False',
    'short_answer': 'Short Answer',
    'essay': 'Essay',
    'fill_in_blank': 'Fill Blank',
    'matching': 'Matching',
    'ordering': 'Ordering'
  };
  return types[type] || type;
};

const questionTypeClass = (type) => {
  return {
    'multiple_choice': 'bg-blue-100 text-blue-800',
    'true_false': 'bg-green-100 text-green-800',
    'short_answer': 'bg-purple-100 text-purple-800',
    'essay': 'bg-yellow-100 text-yellow-800',
    'fill_in_blank': 'bg-red-100 text-red-800',
    'matching': 'bg-indigo-100 text-indigo-800',
    'ordering': 'bg-pink-100 text-pink-800'
  }[type] || 'bg-gray-100 text-gray-800';
};

const addQuestions = () => {
  router.post(route('examiner.pools.attach-questions', props.pool.id), {
    question_ids: selectedQuestions.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      selectedQuestions.value = [];
    },
  });
};

const removeQuestion = (question) => {
  if (confirm(`Are you sure you want to remove "${question.question}" from this pool?`)) {
    router.delete(route('examiner.pools.detach-question', {
      pool: props.pool.id,
      question: question.id,
    }), {
      preserveScroll: true,
    });
  }
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.badge-primary {
  @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800;
}

.badge-count {
  @apply inline-flex items-center justify-center h-6 w-6 rounded-full bg-white bg-opacity-90 text-indigo-800 text-xs font-medium;
}

.badge-question-type {
  @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.badge-question-type-sm {
  @apply inline-flex items-center px-2 py-0.5 rounded text-xs font-medium;
}

.btn-primary {
  @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.btn-secondary {
  @apply inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.btn-icon {
  @apply p-2 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}

.btn-icon-danger {
  @apply p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500;
}
</style>