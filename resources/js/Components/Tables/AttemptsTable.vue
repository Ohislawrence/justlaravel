<template>
  <div class="overflow-x-auto">
    <div class="flex justify-between items-center mb-4">
      <div class="relative max-w-xs">
        <label for="search" class="sr-only">Search</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
          </div>
          <input
            v-model="searchQuery"
            type="text"
            name="search"
            id="search"
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Search attempts..."
          />
        </div>
      </div>
      <div class="flex space-x-2">
        <select
          v-model="statusFilter"
          class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
        >
          <option value="all">All Statuses</option>
          <option value="passed">Passed</option>
          <option value="failed">Failed</option>
        </select>
        <select
          v-model="sortField"
          class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
        >
          <option value="completed_at">Date</option>
          <option value="percentage">Score</option>
          <option value="time_spent">Duration</option>
        </select>
        <button
          @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
          class="p-2 rounded-md border border-gray-300 hover:bg-gray-50"
        >
          <ArrowUpIcon v-if="sortDirection === 'asc'" class="h-5 w-5 text-gray-400" />
          <ArrowDownIcon v-else class="h-5 w-5 text-gray-400" />
        </button>
      </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            User
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Status
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Score
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Time Spent
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Date
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Actions
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="attempt in filteredAttempts" :key="attempt.id">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-10 w-10">
                <img class="h-10 w-10 rounded-full" :src="attempt.user.avatar_url || '/images/default-avatar.png'" alt="" />
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ attempt.user.name }}
                </div>
                <div class="text-sm text-gray-500">
                  {{ attempt.user.email }}
                </div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                  :class="attempt.is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
              {{ attempt.is_passed ? 'Passed' : 'Failed' }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <div class="flex items-center">
              <div class="w-16 bg-gray-200 rounded-full h-2.5 mr-2">
                <div class="bg-blue-600 h-2.5 rounded-full" 
                     :style="`width: ${attempt.percentage}%`"></div>
              </div>
              {{ Math.round(attempt.percentage) }}%
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ formatTime(attempt.time_spent) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ formatDate(attempt.completed_at) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <Link 
              v-if="attempt.quiz_id"
              :href="route('examiner.quiz.attempts.show', { quiz: attempt.quiz_id, attempt: attempt.id })" 
              class="text-blue-600 hover:text-blue-900 mr-3"
            >
              Details
            </Link>
            <span v-else class="text-gray-400">N/A</span>
            <button 
              v-if="attempt.is_passed && attempt.quiz_id"
              @click="downloadCertificate(attempt)"
              class="text-green-600 hover:text-green-900"
            >
              Certificate
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="filteredAttempts.length === 0" class="text-center py-8 text-gray-500">
      No attempts match your search criteria
    </div>

    <Pagination v-if="attempts.last_page > 1" :links="attempts.links" class="mt-4" />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
  MagnifyingGlassIcon, 
  ArrowUpIcon, 
  ArrowDownIcon 
} from '@heroicons/vue/24/outline';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  attempts: {
    type: Object,
    required: true,
    validator: (value) => {
      return Array.isArray(value?.data) && 
             value.data.every(attempt => 'id' in attempt && 'user' in attempt);
    }
  },
  quizId: {
    type: [Number, String],
    required: true
  }
});

const searchQuery = ref('');
const statusFilter = ref('all');
const sortField = ref('completed_at');
const sortDirection = ref('desc');

const filteredAttempts = computed(() => {
  if (!props.attempts?.data) return [];
  
  let items = [...props.attempts.data];

  // Apply search filter (fixed missing parenthesis)
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    items = items.filter(attempt => {
      const userName = attempt.user?.name?.toLowerCase() || '';
      const userEmail = attempt.user?.email?.toLowerCase() || '';
      return userName.includes(query) || userEmail.includes(query);
    });
  }

  // Apply status filter
  if (statusFilter.value !== 'all') {
    items = items.filter(attempt => 
      statusFilter.value === 'passed' ? attempt.is_passed : !attempt.is_passed
    );
  }

  // Apply sorting
  items.sort((a, b) => {
    let fieldA = a[sortField.value];
    let fieldB = b[sortField.value];

    if (sortField.value === 'completed_at') {
      fieldA = new Date(fieldA);
      fieldB = new Date(fieldB);
    }

    if (fieldA < fieldB) {
      return sortDirection.value === 'asc' ? -1 : 1;
    }
    if (fieldA > fieldB) {
      return sortDirection.value === 'asc' ? 1 : -1;
    }
    return 0;
  });

  return items;
});

const formatTime = (seconds) => {
  if (!seconds) return 'N/A';
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}m ${secs}s`;
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
};

const downloadCertificate = (attempt) => {
  try {
    window.open(
      route('examiner.certificates.download', { 
        quiz: attempt.quiz_id, 
        attempt: attempt.id 
      }), 
      '_blank'
    );
  } catch (error) {
    console.error('Failed to generate certificate URL:', error);
  }
};
</script>

<style scoped>
/* Smooth transitions for hover effects */
a, button {
  transition: color 0.15s ease-in-out;
}

/* Custom styling for the progress bar */
.bg-blue-600 {
  transition: width 0.5s ease-in-out;
}

/* Responsive table adjustments */
@media (max-width: 640px) {
  table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
}
</style>