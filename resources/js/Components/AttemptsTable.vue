<template>
    <div class="overflow-x-auto">
      <div v-if="!attempts.data || attempts.data.length === 0" class="text-center py-8 text-gray-500">
        No attempts recorded yet
      </div>
      
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              User
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Score
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Time Spent
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Completed At
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="attempt in attempts.data" :key="attempt.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <div class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full text-white font-semibold uppercase">
                      {{ $page.props.auth.user.name
                          .split(' ')
                          .map(n => n[0])
                          .join('')
                          .substring(0, 2) }}
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ attempt.user?.name || 'Unknown User' }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ attempt.user?.email || '' }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatPercentage(attempt.percentage) }}%
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                    :class="attempt.is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ attempt.is_passed ? 'Passed' : 'Failed' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatTime(attempt.time_spent) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(attempt.completed_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <Link
                :href="route('examiner.quizzes.attempts.show', { quiz: quiz.id, attempt: attempt.id })"
                class="text-blue-600 hover:text-blue-900 mr-4"
              >
                Details
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
  
      <Pagination v-if="attempts.meta && attempts.meta.last_page > 1" 
                  :links="attempts.links" 
                  class="mt-4" />
    </div>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import Pagination from '@/Components/Pagination.vue';
  import { computed } from 'vue';
  
  const props = defineProps({
    attempts: {
      type: Object,
      required: true,
      default: () => ({
        data: [],
        links: [],
        meta: {}
      }),
      validator: (value) => {
        return value.data && Array.isArray(value.data)
      }
    },
    quizId: {
      type: [Number, String],
      required: true
    },
    quiz: Object,
  });

  const formatPercentage = (value) => {
    if (value === null || value === undefined || isNaN(value)) return 'N/A';
    return `${Number(value).toFixed(1)}%`;
    };
  
  const formatTime = (seconds) => {
    if (!seconds) return 'N/A';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}m ${secs}s`;
  };
  
  const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
  };
  </script>
  
  <style scoped>
  /* Add any custom styles here */
  </style>