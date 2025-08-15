<template>
  <AppLayout title="Examinee Dashboard">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ $page.props.auth.user.name }}</h1>
          <p class="mt-2 text-gray-600">Your learning progress and quiz dashboard</p>
        </div>

          <!-- Stats Cards -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
  
  <!-- Completed Quizzes -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transform hover:scale-[1.02] transition-all duration-200">
    <div class="px-6 py-6 flex items-center">
      <div class="flex-shrink-0 p-3 rounded-lg bg-gradient-to-tr from-indigo-100 to-indigo-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="ml-5 flex-1">
        <dt class="text-xs font-semibold text-gray-500 uppercase">Completed Quizzes</dt>
        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ stats.completed_quizzes }}</dd>
      </div>
    </div>
  </div>

  <!-- Average Score -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transform hover:scale-[1.02] transition-all duration-200">
    <div class="px-6 py-6 flex items-center">
      <div class="flex-shrink-0 p-3 rounded-lg bg-gradient-to-tr from-blue-100 to-blue-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
      </div>
      <div class="ml-5 flex-1">
        <dt class="text-xs font-semibold text-gray-500 uppercase">Average Score</dt>
        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ stats.average_score }}%</dd>
      </div>
    </div>
  </div>

  <!-- Time Spent -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transform hover:scale-[1.02] transition-all duration-200">
    <div class="px-6 py-6 flex items-center">
      <div class="flex-shrink-0 p-3 rounded-lg bg-gradient-to-tr from-green-100 to-green-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <div class="ml-5 flex-1">
        <dt class="text-xs font-semibold text-gray-500 uppercase">Time Spent</dt>
        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ formatTime(stats.time_spent) }}</dd>
      </div>
    </div>
  </div>

  <!-- Available Quizzes -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-lg transform hover:scale-[1.02] transition-all duration-200">
    <div class="px-6 py-6 flex items-center">
      <div class="flex-shrink-0 p-3 rounded-lg bg-gradient-to-tr from-amber-100 to-amber-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <div class="ml-5 flex-1">
        <dt class="text-xs font-semibold text-gray-500 uppercase">Available Quizzes</dt>
        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ stats.available_quizzes }}</dd>
      </div>
    </div>
  </div>

</div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Recent Attempts -->
          <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Recent Quiz Attempts
              </h3>
            </div>
            <div class="divide-y divide-gray-100">
              <template v-for="attempt in recentAttempts" :key="attempt.id">
                <div class="px-5 py-4 hover:bg-gray-50 transition-colors duration-150">
                  <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                      <div class="flex items-center">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          {{ attempt.quiz.title }}
                        </p>
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" 
                              :class="attempt.is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                          {{ attempt.is_passed ? 'Passed' : 'Failed' }}
                        </span>
                      </div>
                      <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-4">
                        <div class="flex items-center text-sm text-gray-500 mt-1 sm:mt-0">
                          <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          Score: {{ attempt.score }} / {{ attempt.max_score }} ({{ attempt.percentage }}%)
                        </div>
                        <div class="flex items-center text-sm text-gray-500 mt-1 sm:mt-0">
                          <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          {{ formatDate(attempt.completed_at) }}
                        </div>
                      </div>
                    </div>
                    
                  </div>
                </div>
              </template>
              <div v-if="recentAttempts.length === 0" class="px-5 py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No quiz attempts yet</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by taking your first quiz</p>
                <div class="mt-4">
                  <Link :href="route('examinee.quizzes.index')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    Browse Available Quizzes
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Available Quizzes -->
          <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Available Quizzes
              </h3>
            </div>
            <div class="divide-y divide-gray-100">
              <template v-for="quiz in availableQuizzes" :key="quiz.id">
                <div class="px-5 py-4 hover:bg-gray-50 transition-colors duration-150">
                  <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                      <div class="flex items-center">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          {{ quiz.title }}
                        </p>
                        <span v-if="quiz.attempts_remaining !== null" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          {{ quiz.attempts_remaining }} attempts left
                        </span>
                      </div>
                      <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-4">
                        <div class="flex items-center text-sm text-gray-500 mt-1 sm:mt-0">
                          <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          <template v-if="quiz.starts_at && quiz.ends_at">
                            {{ formatDate(quiz.starts_at) }} - {{ formatDate(quiz.ends_at) }}
                          </template>
                          <template v-else-if="quiz.starts_at">
                            Starts {{ formatDate(quiz.starts_at) }}
                          </template>
                          <template v-else-if="quiz.ends_at">
                            Ends {{ formatDate(quiz.ends_at) }}
                          </template>
                          <template v-else>
                            Always available
                          </template>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 mt-1 sm:mt-0">
                          <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          {{ quiz.time_limit ? `${quiz.time_limit} min` : 'No time limit' }}
                        </div>
                      </div>
                    </div>
                    
                  </div>
                </div>
              </template>
              <div v-if="availableQuizzes.length === 0" class="px-5 py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No quizzes available</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later or contact your instructor</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  stats: Object,
  recentAttempts: Array,
  availableQuizzes: Array,
});

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