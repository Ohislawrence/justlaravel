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
            :placeholder="grouped ? 'Search users...' : 'Search attempts...'"
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
          <option v-if="grouped" value="best_score">Best Score</option>
          <option v-if="grouped" value="avg_score">Avg Score</option>
          <option v-if="grouped" value="attempts_count">Attempts Count</option>
          <option v-if="grouped" value="last_attempt_date">Last Attempt</option>
          <option v-if="!grouped" value="percentage">Score</option>
          <option v-if="!grouped" value="completed_at">Date</option>
          <option v-if="!grouped" value="time_spent">Duration</option>
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
          <th v-if="!grouped" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Attempt #
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            User
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Status
          </th>
          <th v-if="grouped" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Best Score
          </th>
          <th v-if="grouped" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Avg Score
          </th>
          <th v-if="grouped" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Attempts
          </th>
          <th v-if="!grouped" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Score
          </th>
          <th v-if="!grouped" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
        <template v-if="grouped">
          <tr v-for="userAttempt in filteredUserAttempts" :key="userAttempt.user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <div class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full text-white font-semibold uppercase">
                    {{ userAttempt.user.name
                        .split(' ')
                        .map(n => n[0])
                        .join('')
                        .substring(0, 2) }}
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ userAttempt.user.name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ userAttempt.user.email }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                    :class="userAttempt.best_attempt.is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ userAttempt.best_attempt.is_passed ? 'Passed' : 'Failed' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div class="flex items-center">
                <div class="w-16 bg-gray-200 rounded-full h-2.5 mr-2">
                  <div class="bg-blue-600 h-2.5 rounded-full" 
                      :style="`width: ${userAttempt.best_score}%`"></div>
                </div>
                {{ Math.round(userAttempt.best_score) }}%
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div class="flex items-center">
                {{ Math.round(userAttempt.avg_score) }}%
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ userAttempt.attempts_count }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(userAttempt.last_attempt_date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <Link 
                :href="route('examiner.quizzes.analysis.user', { quiz: quizId, user: userAttempt.user.id })"  
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                View All
              </Link>
              <button 
                v-if="userAttempt.best_attempt.is_passed"
                @click="downloadCertificate(userAttempt.best_attempt)"
                class="text-green-600 hover:text-green-900"
              >
                Certificate
              </button>
            </td>
          </tr>
        </template>
        <template v-else>
          <tr v-for="attempt in filteredAttempts" :key="attempt.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ attempt.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <div class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full text-white font-semibold uppercase">
                    {{ attempt.user.name
                        .split(' ')
                        .map(n => n[0])
                        .join('')
                        .substring(0, 2) }}
                  </div>
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
        </template>
      </tbody>
    </table>

    <div v-if="(grouped && filteredUserAttempts.length === 0) || (!grouped && filteredAttempts.length === 0)" class="text-center py-8 text-gray-500">
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
   // validator: (value) => {
     // return Array.isArray(value?.data) && 
       //      value.data.every(attempt => {
               // Ensure each attempt has at least an ID and a user object
       //        return 'id' in attempt && 
        //              'user' in attempt && 
         //             attempt.user !== null;
          //   });
    //}
  },
  quizId: {
    type: [Number, String],
    required: true,
    validator: (value) => {
      return value !== undefined && value !== null;
    }
  },
  grouped: {
    type: Boolean,
    default: true
  }
});

const searchQuery = ref('');
const statusFilter = ref('all');
const sortField = ref('best_score');
const sortDirection = ref('desc');

// Group attempts by user
const groupedAttempts = computed(() => {
  if (!props.attempts?.data) return [];
  
  const userMap = {};
  
  props.attempts.data.forEach(attempt => {
    if (!attempt?.user?.id) return;
    
    const userId = attempt.user.id;
    
    if (!userMap[userId]) {
      userMap[userId] = {
        user: attempt.user,
        attempts: [],
        best_score: 0,
        avg_score: 0,
        attempts_count: 0,
        best_attempt: null,
        last_attempt_date: null
      };
    }
    
    userMap[userId].attempts.push(attempt);
    userMap[userId].attempts_count++;
    
    // Ensure percentage is a number
    const percentage = Number(attempt.percentage) || 0;
    if (percentage > userMap[userId].best_score) {
      userMap[userId].best_score = percentage;
      userMap[userId].best_attempt = attempt;
    }
    
    // Update last attempt date
    const attemptDate = new Date(attempt.completed_at);
    if (!userMap[userId].last_attempt_date || 
        attemptDate > new Date(userMap[userId].last_attempt_date)) {
      userMap[userId].last_attempt_date = attempt.completed_at;
    }
  });
  
  // Calculate average scores with proper number handling
  Object.keys(userMap).forEach(userId => {
    const userAttempts = userMap[userId];
    const totalScore = userAttempts.attempts.reduce((sum, attempt) => {
      return sum + (Number(attempt.percentage) || 0);
    }, 0);
    
    // Ensure we don't divide by zero
    userAttempts.avg_score = userAttempts.attempts_count > 0 
      ? totalScore / userAttempts.attempts_count 
      : 0;
  });
  
  return Object.values(userMap);
});

const filteredUserAttempts = computed(() => {
  let items = [...groupedAttempts.value];

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    items = items.filter(userAttempt => {
      const userName = userAttempt.user?.name?.toLowerCase() || '';
      const userEmail = userAttempt.user?.email?.toLowerCase() || '';
      return userName.includes(query) || userEmail.includes(query);
    });
  }

  // Apply status filter
  if (statusFilter.value !== 'all') {
    items = items.filter(userAttempt => 
      statusFilter.value === 'passed' ? userAttempt.best_attempt.is_passed : !userAttempt.best_attempt.is_passed
    );
  }
  
  // Apply sorting
  items.sort((a, b) => {
    let fieldA, fieldB;
    
    switch (sortField.value) {
      case 'best_score':
        fieldA = a.best_score;
        fieldB = b.best_score;
        break;
      case 'avg_score':
        fieldA = a.avg_score;
        fieldB = b.avg_score;
        break;
      case 'attempts_count':
        fieldA = a.attempts_count;
        fieldB = b.attempts_count;
        break;
      case 'last_attempt_date':
        fieldA = new Date(a.last_attempt_date);
        fieldB = new Date(b.last_attempt_date);
        break;
      default:
        fieldA = a.best_score;
        fieldB = b.best_score;
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

const filteredAttempts = computed(() => {
  if (!props.attempts?.data) return [];
  
  return props.attempts.data
    .filter(attempt => {
      // Skip invalid attempts
      if (!attempt?.user) return false;
      
      // Apply search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        const userName = attempt.user?.name?.toLowerCase() || '';
        const userEmail = attempt.user?.email?.toLowerCase() || '';
        return userName.includes(query) || userEmail.includes(query);
      }
      return true;
    })
    .filter(attempt => {
      // Apply status filter
      if (statusFilter.value === 'all') return true;
      return statusFilter.value === 'passed' 
        ? attempt.is_passed 
        : !attempt.is_passed;
    })
    .sort((a, b) => {
      // Apply sorting
      let fieldA = a[sortField.value];
      let fieldB = b[sortField.value];

      if (sortField.value === 'completed_at') {
        fieldA = new Date(fieldA);
        fieldB = new Date(fieldB);
      }

      if (fieldA < fieldB) return sortDirection.value === 'asc' ? -1 : 1;
      if (fieldA > fieldB) return sortDirection.value === 'asc' ? 1 : -1;
      return 0;
    });
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
  if (!attempt?.quiz_id || !attempt?.id) {
    console.error('Missing required parameters for certificate download');
    return;
  }
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
.bg-blue-600, .bg-blue-400 {
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