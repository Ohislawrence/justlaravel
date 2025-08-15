<template>
  <AppLayout title="All Pools">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Question Pools
      </h2>
      <p class="text-sm text-gray-600 mt-1">
            {{ pools.data.length }} pools total • {{ totalQuestions }} questions across all pools
          </p>
    </template>

    <div class="py-12"> 
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200"> 

            <!-- Search and Create Button Row - Matching Quizzes -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
              
              <input
                v-model="searchTerm" 
                @input="handleSearchInput" 
                type="text"
                placeholder="Search pools..." 
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
              <!-- Create Button - Moved inside card and styled like Quizzes -->
              <button
                @click = "createPool"
                class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
              >
                Create New Pool
            </button>
            </div>

            <!-- Empty State - Updated to match Quizzes style and placement -->
            <div v-if="pools.data.length === 0" class="text-center py-12">
              <div class="text-gray-500">
                <i class="fas fa-question-circle text-4xl mb-4"></i> <!-- Using a relevant icon -->
                <p class="text-lg mb-2">No question pools found</p>
                <p class="text-sm">
                  Get started by creating your first question pool.
                </p>
              </div>
            </div>

            <!-- Pools List - Table Design (Already matched from previous step) -->
            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Pool Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Usage
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Questions
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Last Updated
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="pool in pools.data" :key="pool.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ pool.name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ pool.description || 'No description' }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex flex-col gap-1">
                        <span v-if="pool.quiz" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                          </svg>
                          Used in {{ pool.quizzes_count }} {{ pool.quizzes_count === 1 ? 'quiz' : 'quizzes' }}
                        </span>
                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                          <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                          </svg>
                          Not assigned
                        </span>
                        <span v-if="pool.is_global" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                          </svg>
                          Global Pool
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-900">
                          {{ pool.questions_count }}
                        </span>
                        <span class="ml-1 text-xs text-gray-500">
                          {{ pool.questions_count === 1 ? 'question' : 'questions' }}
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(pool.updated_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <!-- Action Buttons - Styled like Quizzes -->
                      <div class="flex justify-end items-center gap-2">
                        <Link
                          :href="route('examiner.question-pools.manage-questions', pool.id)"
                          class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                          title="Manage questions"
                        >
                          Manage
                        </Link>
                        <Link
                          :href="route('examiner.question-pools.edit', pool.id)"
                          class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                          title="Edit pool"
                        >
                          Edit
                        </Link>
                        <button
                          @click="confirmDelete(pool)"
                          class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                          title="Delete pool"
                        >
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination - Updated padding/margin to match Quizzes -->
            <Pagination :links="pools.links" class="mt-6" />

          </div> <!-- Close p-6 div -->
        </div> <!-- Close card div -->
      </div>
    </div>
    <!-- Modal -->
    <FeatureLimitModal 
      v-model="showModal"
      :featureName="modalFeature"
      :message="modalMessage"
    />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue';
import { computed, ref } from 'vue'; 
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue';

const props = defineProps({
  pools: Object,
  PoolLimit: Number,
  currentPoolCount: Number,
});


const searchTerm = ref('');

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

function createPool() {
  const canAddPool = props.currentPoolCount < props.gPoolLimit

  if (!canAddPool) {
    openFeatureModal(
      'Add Pool',
      'You have reached the question pool limit. Upgrade your plan to add more question pools.'
    )
    return
  }

  router.visit(route('examiner.question-pools.create'))
}


const handleSearchInput = (event) => {
  const value = event.target.value;
  searchTerm.value = value;

};

const totalQuestions = computed(() => {
  return props.pools.data.reduce((sum, pool) => sum + pool.questions_count, 0);
});

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const confirmDelete = (pool) => {
  if (confirm(`Are you sure you want to delete "${pool.name}" pool? This action cannot be undone.`)) {
    if (true) {
      router.delete(route('examiner.question-pools.destroy', pool.id));
    } else {
      console.warn('Inertia global object not found. Delete functionality might not work as expected.');
    }
  }
};
</script>

<style scoped>
/* Scoped styles remain unchanged */
.btn-primary {
  @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}
.btn-icon {
  @apply p-2 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
}
.btn-icon-danger {
  @apply p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500;
}
</style>