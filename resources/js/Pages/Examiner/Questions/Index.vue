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
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-6">
              <h3 class="text-lg font-medium">Manage Questions</h3>
              <button 
                @click="createQuestion" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
              >
                Add New Question
            </button>
            </div>

            <div class="mb-8">
              <h4 class="font-medium mb-2">Question Pools</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="pool in props.pools" :key="pool.id" class="border rounded p-4">
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pools</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="question in props.questions" :key="question.id">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <PoolSelector 
                        :pools="props.availablePools" 
                        :question-id="question.id"
                      />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link :href="route('examiner.quizzes.questions.edit', {quiz: props.quiz.id, question: question.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</Link>
                      <button @click="confirmDelete(question)" class="text-red-600 hover:text-red-900">Delete</button>
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

  
  