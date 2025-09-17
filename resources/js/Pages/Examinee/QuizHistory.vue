<template>
  <AppLayout title="My Quiz History">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">My Quiz History</h1>
              <p class="mt-1 text-sm text-gray-600">View all your completed quiz attempts</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
              <div class="relative">
                <select 
                  v-model="filters.status"
                  @change="filterAttempts"
                  class="appearance-none bg-white border border-gray-200 rounded-lg pl-3.5 pr-9 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition-all duration-200 w-full sm:w-auto"
                >
                  <option value="all">All Statuses</option>
                  <option value="passed">Passed</option>
                  <option value="failed">Failed</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400">
                  <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
              <div class="relative">
                <select 
                  v-model="filters.sort"
                  @change="filterAttempts"
                  class="appearance-none bg-white border border-gray-200 rounded-lg pl-3.5 pr-9 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition-all duration-200 w-full sm:w-auto"
                >
                  <option value="newest">Newest First</option>
                  <option value="oldest">Oldest First</option>
                  <option value="highest">Highest Score</option>
                  <option value="lowest">Lowest Score</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400">
                  <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quiz Attempts Table -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quiz</th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Organization</th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Time Spent</th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Completed</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="attempt in filteredAttempts" :key="attempt.id" class="hover:bg-gray-50/70 transition-colors duration-150 group">
                  <td class="px-4 py-4">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-9 w-9">
                        <div class="h-9 w-9 rounded-md flex items-center justify-center" :class="attempt.is_passed ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="attempt.is_passed ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                          </svg>
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900 group-hover:text-green-700 transition-colors">
                          {{ attempt.quiz.title }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                          Attempt #{{ attempt.attempt_number }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4 hidden sm:table-cell">
                    <div class="text-sm text-gray-700">{{ attempt.quiz.organization.name }}</div>
                  </td>
                  <td class="px-4 py-4">
                    <span 
                      class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-md border" 
                      :class="attempt.is_passed ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="attempt.is_passed ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                      </svg>
                      {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ attempt.score }}/{{ attempt.max_score }}
                      <span class="text-gray-500 ml-1">({{ attempt.percentage }}%)</span>
                    </div>
                    <div class="mt-1.5 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                      <div class="h-full bg-gradient-to-r transition-all duration-500 ease-out" :class="attempt.is_passed ? 'from-green-500 to-green-600' : 'from-red-500 to-red-600'" :style="`width: ${Math.min(attempt.percentage, 100)}%`"></div>
                    </div>
                  </td>
                  <td class="px-4 py-4 text-sm text-gray-600 hidden md:table-cell">
                    {{ formatTime(attempt.time_spent) }}
                  </td>
                  <td class="px-4 py-4 text-sm text-gray-600 hidden md:table-cell">
                    {{ formatDate(attempt.completed_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="filteredAttempts.length === 0" class="px-6 py-16 text-center">
            <div class="mx-auto max-w-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="mt-4 text-lg font-semibold text-gray-900">No quiz attempts found</h3>
              <p class="mt-1 text-sm text-gray-500">You haven't completed any quizzes yet.</p>
              <div class="mt-5">
                <Link 
                  :href="route('examinee.quizzes.index')" 
                  class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Browse Quizzes
                </Link>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="attempts.meta && attempts.meta.total > 0" class="px-4 py-3 flex items-center justify-between border-t border-gray-100 bg-gray-50/50 rounded-b-xl">
            <div class="text-xs text-gray-600">
              Showing <span class="font-medium">{{ attempts.meta.from }}</span> to <span class="font-medium">{{ attempts.meta.to }}</span> of <span class="font-medium">{{ attempts.meta.total }}</span>
            </div>
            <nav class="inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
              <Link 
                v-for="(link, index) in attempts.meta.links" 
                :key="index"
                :href="link.url || '#'"
                :class="{
                  'bg-green-600 border-green-600 text-white z-10 hover:bg-green-700': link.active,
                  'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900': !link.active && link.url,
                  'bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed': !link.url
                }"
                class="relative inline-flex items-center px-3 py-1.5 border text-xs font-medium transition-colors duration-200 first:rounded-l-lg last:rounded-r-lg"
                preserve-scroll
                v-html="link.label"
              />
            </nav>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { pickBy } from 'lodash';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  attempts: Object,
  filters: Object,
});

const filters = ref({
  status: props.filters.status || 'all',
  sort: props.filters.sort || 'newest',
});

const filteredAttempts = computed(() => {
  return props.attempts.data.filter(attempt => {
    // Filter by status
    if (filters.value.status === 'passed') return attempt.is_passed;
    if (filters.value.status === 'failed') return !attempt.is_passed;
    return true;
  });
});

const filterAttempts = () => {
  router.get(route('examinee.history'), pickBy(filters.value), {
    preserveState: true,
    replace: true,
  });
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatTime = (seconds) => {
  if (!seconds) return '0m';
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  
  let result = '';
  if (hours > 0) result += `${hours}h `;
  result += `${minutes}m`;
  return result;
};
</script>

<style scoped>
/* Green-themed styling */
.bg-green-50 {
    background-color: #ecfdf5;
}

.border-green-500 {
    border-color: #10B981;
}

.text-green-600 {
    color: #10B981;
}

.text-green-700 {
    color: #065f46;
}

/* Focus ring styling */
.focus\:ring-green-500:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
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