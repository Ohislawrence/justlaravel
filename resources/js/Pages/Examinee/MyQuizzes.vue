<template>
    <AppLayout title="My Quizzes">
      <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <!-- Header and Filters -->
          <div v-if="ongoingQuizzes.length > 0" class="mb-10">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Continue Quiz</h2>
          </div>
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div 
              v-for="quiz in ongoingQuizzes" 
              :key="quiz.id"
              class="bg-white overflow-hidden rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200"
            >
              <div class="px-5 py-6">
                <!-- Quiz Header -->
                <div class="flex items-start justify-between">
                  <div class="min-w-0 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      {{ quiz.title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 truncate">
                      {{ quiz.organization.name }}
                    </p>
                  </div>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ml-3 flex-shrink-0 whitespace-nowrap bg-yellow-50 text-yellow-700 border border-yellow-100">
                    In Progress
                  </span>
                </div>

                <!-- Quiz Details -->
                <div class="mt-5 space-y-3">
                  <div class="flex items-center text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                      Time spent: {{ formatTime(quiz.attempt.time_spent) }}
                    </span>
                  </div>

                  <div class="flex items-center text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>
                      Started {{ formatDateTime(quiz.attempt.started_at) }}
                    </span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6">
                  <Link 
                    :href="route('examinee.attempt', { quiz: quiz.id, attempt: quiz.attempt.id })"
                    class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                  >
                    Continue Quiz
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
          <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div>
                <h1 class="text-3xl font-bold text-gray-900">My Quizzes</h1>
                <p class="mt-2 text-gray-600">All quizzes assigned to you</p>
              </div>
              <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative">
                  <select 
                    v-model="filters.status"
                    @change="filterQuizzes"
                    class="appearance-none bg-white border border-gray-200 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all duration-200 w-full sm:w-auto"
                  >
                    <option value="all">All Statuses</option>
                    <option value="available">Available</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="expired">Expired</option>
                    <option value="completed">Completed</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="relative">
                  <select 
                    v-model="filters.organization"
                    @change="filterQuizzes"
                    class="appearance-none bg-white border border-gray-200 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all duration-200 w-full sm:w-auto"
                  >
                    <option value="all">All Organizations</option>
                    <option v-for="org in organizations" :key="org.id" :value="org.id">
                      {{ org.name }}
                    </option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Quiz Cards -->
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div 
              v-for="quiz in filteredQuizzes" 
              :key="quiz.id"
              class="bg-white overflow-hidden rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200"
            >
              <div class="px-5 py-6">
                <!-- Quiz Header -->
                <div class="flex items-start justify-between">
                  <div class="min-w-0 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      {{ quiz.title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 truncate">
                      {{ quiz.organization.name }}
                    </p>
                  </div>
                  <span 
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ml-3 flex-shrink-0 whitespace-nowrap"
                    :class="{
                      'bg-blue-50 text-blue-700 border border-blue-100': quiz.status === 'available',
                      'bg-gray-50 text-gray-700 border border-gray-100': quiz.status === 'upcoming',
                      'bg-red-50 text-red-700 border border-red-100': quiz.status === 'expired',
                      'bg-green-50 text-green-700 border border-green-100': quiz.status === 'completed'
                    }"
                  >
                    {{ quiz.statusDisplay }}
                  </span>
                </div>
  
                <!-- Quiz Details -->
                <div class="mt-5 space-y-3">
                  <div class="flex items-center text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                      {{ quiz.time_limit ? `${quiz.time_limit} min` : 'No time limit' }}
                      <template v-if="quiz.attempts_remaining !== null">
                        • {{ quiz.attempts_remaining }} {{ quiz.attempts_remaining === 1 ? 'attempt' : 'attempts' }} left
                      </template>
                    </span>
                  </div>
  
                  <div class="flex items-center text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span v-if="quiz.starts_at && quiz.ends_at">
                      {{ formatDate(quiz.starts_at) }} - {{ formatDate(quiz.ends_at) }}
                    </span>
                    <span v-else-if="quiz.starts_at">
                      Starts {{ formatDate(quiz.starts_at) }}
                    </span>
                    <span v-else-if="quiz.ends_at">
                      Ends {{ formatDate(quiz.ends_at) }}
                    </span>
                    <span v-else>
                      Always available
                    </span>
                  </div>
  
                  <div v-if="quiz.best_attempt" class="flex items-center text-sm text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                      Best score: {{ quiz.best_attempt.percentage }}%
                      <span v-if="quiz.best_attempt.is_passed" class="text-green-600 font-medium">(Passed)</span>
                      <span v-else class="text-red-600 font-medium">(Failed)</span>
                    </span>
                  </div>
                </div>
  

                <!-- Action Buttons -->
                <div class="mt-6">
                  <div v-if="quiz.status === 'available'">
                    <Link 
                      :href="route('quiz.show', {quiz : quiz.id, token: quiz.share_token})"
                      class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                    >
                      Take Quiz
                    </Link>
                  </div>
                  <div v-else-if="quiz.status === 'completed'  && quiz.attempts_remaining > 0">
                    <Link 
                      :href="route('quiz.show',  {quiz : quiz.id, token: quiz.share_token})"
                      class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                    >
                      Retake Quiz
                    </Link>
                  </div>
                  <div v-else-if="quiz.status === 'completed'">
                    <Link 
                      :href="route('examinee.quiz.results', {quiz : quiz.id, attemptId : quiz.best_attempt.id})"
                      class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                      View Results
                    </Link>
                  </div>
                  <div v-else>
                    <button 
                      disabled
                      class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-gray-500 bg-gray-100 cursor-not-allowed"
                    >
                      <span v-if="quiz.status === 'upcoming'">Not Available Yet</span>
                      <span v-else>Quiz Expired</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Empty State -->
          <div v-if="filteredQuizzes.length === 0" class="bg-white text-center rounded-xl shadow-sm border border-gray-100 px-6 py-16">
            <div class="mx-auto max-w-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <h3 class="mt-4 text-lg font-medium text-gray-900">No quizzes found</h3>
              <p class="mt-2 text-gray-500">You don't have any quizzes matching your current filters.</p>
              <div class="mt-6">
                <button 
                  @click="resetFilters"
                  type="button"
                  class="inline-flex items-center px-4 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                >
                  Reset Filters
                </button>
              </div>
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
    quizzes: Array,
    organizations: Array,
    filters: Object,
    ongoingQuizzes: {
      type: Array,
      default: () => []
    },
  });
  
  const filters = ref({
    status: props.filters.status || 'all',
    organization: props.filters.organization || 'all',
  });
  
  const filteredQuizzes = computed(() => {
    return props.quizzes.filter(quiz => {
      // Filter by status
      if (filters.value.status !== 'all' && quiz.status !== filters.value.status) {
        return false;
      }
      
      // Filter by organization
      if (filters.value.organization !== 'all' && quiz.organization.id !== parseInt(filters.value.organization)) {
        return false;
      }
      
      return true;
    });
  });
  

  const formatTime = (seconds) => {
  if (!seconds) return '0:00';
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const formatDateTime = (dateString) => {
  if (!dateString) return '';
  const options = { 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  return new Date(dateString).toLocaleDateString(undefined, options);
};


  const filterQuizzes = () => {
    router.get(route('examinee.quizzes.index'), pickBy(filters.value), {
      preserveState: true,
      replace: true,
    });
  };
  
  const resetFilters = () => {
    filters.value = {
      status: 'all',
      organization: 'all',
    };
    filterQuizzes();
  };
  
  const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric',
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
  };
  </script>