<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-2xl text-gray-800">
          Manage Questions in Pool:
          <span class="text-indigo-600">{{ pool.name }}</span>
        </h2>
        <Link
          :href="route('examiner.question-pools.index')"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
        >
          Back to Pools
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <!-- Current Questions Section -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-semibold text-gray-900">Current Questions</h3>
              <span class="px-3 py-1 text-sm bg-indigo-100 text-indigo-800 rounded-full">
                {{ pool.questions.length }} questions
              </span>
            </div>

            <div class="overflow-x-auto">
              <table
                v-if="pool.questions.length > 0"
                class="min-w-full divide-y divide-gray-200 text-sm"
              >
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Question</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Type</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Points</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                  <tr
                    v-for="question in pool.questions"
                    :key="question.id"
                    class="hover:bg-gray-50 transition"
                  >
                    <td class="px-6 py-4 max-w-md whitespace-normal">
                      <div class="text-gray-800 font-medium line-clamp-2">
                        {{ question.question }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs font-semibold leading-5 rounded-full"
                        :class="{
                          'bg-blue-100 text-blue-800': question.type === 'multiple_choice',
                          'bg-green-100 text-green-800': question.type === 'true_false',
                          'bg-purple-100 text-purple-800': question.type === 'short_answer'
                        }"
                      >
                        {{ question.type }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                      {{ question.points }} pts
                    </td>
                    <td class="px-6 py-4">
                      <button
                        @click="removeQuestion(question)"
                        class="text-red-600 hover:text-red-800 transition"
                        title="Remove from pool"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>

              <div v-else class="text-center py-12">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-gray-800 font-medium">No questions in this pool</h3>
                <p class="text-sm text-gray-500">Add questions from the available list below.</p>
              </div>
            </div>
          </div>

          <!-- Add Questions Section -->
          <div v-if="availableQuestions.length > 0" class="p-6">
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-gray-900 mb-1">Add Questions</h3>
              <p class="text-sm text-gray-500">Select multiple questions to add to this pool</p>
            </div>

            <div class="space-y-4">
              <div class="relative">
                <select
                  v-model="selectedQuestions"
                  multiple
                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm max-h-64 overflow-auto"
                >
                  <option
                    v-for="question in availableQuestions"
                    :key="question.id"
                    :value="question.id"
                    class="py-2 px-2"
                  >
                    {{ question.question }} – {{ question.type }} • {{ question.points }} pts
                  </option>
                </select>
              </div>

              <button
                @click="addQuestions"
                :disabled="selectedQuestions.length === 0"
                :class="[
                  'btn-primary inline-flex items-center px-4 py-2 rounded-md',
                  selectedQuestions.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-600'
                ]"
              >
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Selected Questions
                <span v-if="selectedQuestions.length > 0" class="ml-2 bg-white bg-opacity-20 px-2 py-0.5 rounded-full text-xs">
                  {{ selectedQuestions.length }}
                </span>
              </button>
            </div>
          </div>

          <div v-else class="p-6 text-center">
            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-gray-800 font-medium">No available questions</h3>
            <p class="text-sm text-gray-500">All questions are already in this pool or none exist yet.</p>
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
  if (confirm('Are you sure you want to remove this question from the pool?')) {
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

select[multiple] option {
  padding: 8px 12px;
  border-bottom: 1px solid #f3f4f6;
}

select[multiple] option:last-child {
  border-bottom: none;
}
</style>