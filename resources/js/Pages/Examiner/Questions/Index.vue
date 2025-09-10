<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import PoolSelector from '@/Components/PoolSelector.vue'
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue'

// Props
const props = defineProps({
  quiz: Object,
  questions: Array,
  pools: Array,
  availablePools: Array,
  questionsLimit: Number,
  currentQuestionsCount: Number,
})

// Methods
function questionTypeLabel(type) {
  const types = {
    multiple_choice: 'Multiple Choice',
    true_false: 'True/False',
    short_answer: 'Short Answer',
    essay: 'Essay',
    fill_in_blank: 'Fill in Blank',
    matching: 'Matching',
    ordering: 'Ordering'
  }
  return types[type] || type
}

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

function createQuestion() {
  const canAddQuestion = props.currentQuestionsCount < props.questionsLimit

  if (!canAddQuestion) {
    openFeatureModal(
      'Add Question',
      'You have reached the question limit. Upgrade your plan to add more questions.'
    )
    return
  }

  router.visit(route('examiner.quizzes.questions.create', props.quiz.id))
}

function confirmDelete(question) {
  if (confirm('Are you sure you want to delete this question?')) {
    router.delete(route('examiner.quizzes.questions.destroy', { 
      quiz: props.quiz.id, 
      question: question.id 
    }))
  }
}
</script>

<template>
  <AppLayout :title="props.quiz.title">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Questions for {{ props.quiz.title }}
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-6">
              <h3 class="text-lg font-medium">Manage Questions</h3>
              <button 
                @click="createQuestion" 
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
              >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Question
              </button>
            </div>

            <div v-if="props.pools.length > 0" class="mb-8">
              <h4 class="font-medium mb-2">Question Pools</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="pool in props.pools" :key="pool.id" class="border rounded-lg p-4 hover:bg-gray-50 transition-all duration-200">
                  <div class="flex justify-between">
                    <h5 class="font-medium">{{ pool.name }}</h5>
                    <span class="text-sm text-gray-500">{{ pool.questions_count }} questions</span>
                  </div>
                  <p class="text-sm text-gray-600 mt-1">{{ pool.description }}</p>
                </div>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="question in props.questions" :key="question.id" class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ question.question }}</div>
                      <div class="text-sm text-gray-500" v-if="question.description">{{ question.description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ questionTypeLabel(question.type) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ question.points }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <span v-if="question.is_required" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                      <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link 
                        :href="route('examiner.quizzes.questions.edit', {quiz: props.quiz.id, question: question.id})" 
                        class="inline-flex items-center text-green-600 hover:text-green-800 mr-3 transition-colors duration-150"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                      </Link>
                      <button 
                        @click="confirmDelete(question)" 
                        class="inline-flex items-center text-red-600 hover:text-red-800 transition-colors duration-150"
                      >
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    <FeatureLimitModal 
      v-model="showModal"
      :featureName="modalFeature"
      :message="modalMessage"
    />
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

.focus\:ring-green-300:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Green-themed required indicator */
.bg-green-100 {
    background-color: #ecfdf5;
}

.text-green-800 {
    color: #065f46;
}

/* Hover effects */
.hover\:bg-gray-50:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
    
    .gap-4 > *:not(:last-child) {
        margin-bottom: 1rem;
    }
}
</style>