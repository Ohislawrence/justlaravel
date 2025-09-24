<template>
  <PublicLayout title="Start Your Quiz">
    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Quiz Header -->
          <div class="px-6 py-5 sm:px-8 sm:py-6 border-b border-gray-200 bg-green-50">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ quiz.title }}</h1>
            <div class="mt-2 flex flex-wrap items-center text-sm text-gray-600 gap-x-4 gap-y-1">
              <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ totalQuestions }} {{ totalQuestions === 1 ? 'question' : 'questions' }}
              </span>
            </div>
          </div>

          <!-- Quiz Content -->
          <div class="px-6 py-5 sm:px-8 sm:py-6">
            <!-- Instructions -->
            <div class="mb-8">
              <h2 class="text-lg font-semibold text-gray-900 mb-3">From {{ organisation }}</h2>
              <div class="prose max-w-none text-gray-600 bg-gray-50 p-4 rounded-md border border-gray-200">
                <div v-html="quiz.instructions || '<p class=\'text-gray-500 italic\'>No specific instructions provided.</p>'"></div>
              </div>
            </div>

            <!-- Quiz Details Grid -->
            <div v-if="quiz.quiz_type !== 'survey'" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <!-- Timing -->
              <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm">
                <h3 class="text-md font-medium text-gray-900 flex items-center mb-3">
                  <ClockIcon class="h-5 w-5 mr-2 text-green-600" />
                  <span class="font-medium text-green-700">Timing</span>
                </h3>
                <ul class="space-y-2 text-sm text-gray-600">
                  <li class="flex items-start">
                    <ClockIcon class="h-4 w-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0" />
                    <span>{{ quiz.time_limit ? `${quiz.time_limit} minutes` : 'No time limit' }}</span>
                  </li>
                  <li v-if="quiz.starts_at" class="flex items-start">
                    <CalendarIcon class="h-4 w-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0" />
                    <span>Available from: <span class="font-medium">{{ formatDate(quiz.starts_at) }}</span></span>
                  </li>
                  <li v-if="quiz.ends_at" class="flex items-start">
                    <CalendarIcon class="h-4 w-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0" />
                    <span>Available until: <span class="font-medium">{{ formatDate(quiz.ends_at) }}</span></span>
                  </li>
                </ul>
              </div>

              <!-- Grading -->
              <div class="bg-white p-4 rounded-md border border-gray-200 shadow-sm">
                <h3 class="text-md font-medium text-gray-900 flex items-center mb-3">
                  <CheckCircleIcon class="h-5 w-5 mr-2 text-green-600" />
                  <span class="font-medium text-green-700">Grading</span>
                </h3>
                <ul class="space-y-2 text-sm text-gray-600">
                  <li class="flex items-start">
                    <CheckCircleIcon class="h-4 w-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0" />
                    <span>Passing score: <span class="font-medium">{{ quiz.passing_score ? `${quiz.passing_score}%` : 'Not specified' }}</span></span>
                  </li>
                  <li v-if="attemptsRemaining !== null" class="flex items-start">
                    <ArrowPathIcon class="h-4 w-4 mt-0.5 mr-2 text-gray-400 flex-shrink-0" />
                    <span>Attempts remaining: <span class="font-medium">{{ attemptsRemaining }}</span></span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Action Area -->
            <div class="flex flex-col items-center">
              <!-- Auth State -->
              <div v-if="!isAuthenticated" class="text-center mb-6">
                <div v-if="quiz.is_public">
                  <!-- Guest Info Collection Form -->
                  <form v-if="quiz.require_guest_info" @submit.prevent="startGuestQuiz" class="w-full max-w-md space-y-4">
                    <!-- Name Field (if required) -->
                    <div v-if="quiz.guest_info_required === 'name' || quiz.guest_info_required === 'both'">
                      <InputLabel for="guest_name" value="Your Name" class="block text-sm font-medium text-gray-700 mb-1" />
                      <input
                        id="guest_name"
                        v-model="guestForm.name"
                        type="text"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your name"
                      />
                      <InputError v-if="guestForm.errors.name" :message="guestForm.errors.name" class="mt-1" />
                    </div>

                    <!-- Email Field (if required) -->
                    <div v-if="quiz.guest_info_required === 'email' || quiz.guest_info_required === 'both'">
                      <InputLabel for="guest_email" value="Your Email" class="block text-sm font-medium text-gray-700 mb-1" />
                      <input
                        id="guest_email"
                        v-model="guestForm.email"
                        type="email"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your email"
                      />
                      <InputError v-if="guestForm.errors.email" :message="guestForm.errors.email" class="mt-1" />
                    </div>

                    <!-- Submit Button -->
                    <button
                      type="submit"
                      :disabled="guestForm.processing"
                      class="inline-flex items-center px-5 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition ease-in-out duration-150"
                    >
                      <span v-if="guestForm.processing">Starting...</span>
                      <span v-else>Start</span>
                    </button>
                  </form>
                  </div>
                <div v-else>
                <p class="text-gray-700 mb-4">You need to sign in to access this quiz.</p>
                <Link
                  :href="route('login')"
                  class="inline-flex items-center px-5 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition ease-in-out duration-150"
                >
                  Sign In
                </Link>
                </div>
              </div>

              <!-- Assignment Warning -->
              <div v-else-if="!isAssigned" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 w-full max-w-md">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                      <span class="font-medium">Access Denied.</span> This quiz hasn't been assigned to you. Please contact your instructor for access.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Attempts Warning -->
              <div v-else-if="attemptsRemaining !== null && attemptsRemaining <= 0" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 w-full max-w-md">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-red-700">
                      <span class="font-medium">No Attempts Left.</span> You have no attempts remaining for this quiz.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Quiz Not Available Warning -->
              <div v-else-if="!isQuizAvailable" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 w-full max-w-md">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                      <span class="font-medium">Quiz Not Available.</span> 
                      <span v-if="isQuizEarly">This quiz will be available starting {{ formatDate(quiz.starts_at) }}.</span>
                      <span v-if="isQuizExpired">This quiz was available until {{ formatDate(quiz.ends_at) }}.</span>
                    </p>
                  </div>
                </div>
              </div>

              <!-- Start Quiz Button -->
              <div v-else class="mb-2">
                <button
                  @click="startQuiz"
                  :disabled="!canStartQuiz"
                  :class="{
                    'opacity-50 cursor-not-allowed': !canStartQuiz,
                    'inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition ease-in-out duration-150': canStartQuiz
                  }"
                >
                  Start Quiz
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { computed,ref, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import {
  ClockIcon,
  CheckCircleIcon,
  CalendarIcon,
  ArrowPathIcon // Alias for RefreshIcon
} from '@heroicons/vue/24/outline';

// Renamed RefreshIcon to ArrowPathIcon as it's the correct name for Heroicons v2
// import { InformationCircleIcon } from '@heroicons/vue/24/solid'; // Optional for the pool badge

const props = defineProps({
  quiz: Object,
  organisation: String,
  attemptsRemaining: [Number, null],
  isAuthenticated: Boolean,
  isAssigned: Boolean,
  remainingAttempt: Boolean,
});

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString();
};

const startQuiz = () => {
  router.post(route('examinee.start', props.quiz.id), {}, {
    onSuccess: () => {
      //
    }
  });
};

const guestForm = useForm({
  name: '',
  email: ''
});

const startGuestQuiz = () => {
  // If guest info is required, validate and submit the form
  if (props.quiz.require_guest_info) {
    // Validate based on requirements
    const validationRules = {};
    
    if (props.quiz.guest_info_required === 'name' || props.quiz.guest_info_required === 'both') {
      validationRules.name = 'required|string|max:255';
    }
    
    if (props.quiz.guest_info_required === 'email' || props.quiz.guest_info_required === 'both') {
      validationRules.email = 'required|email|max:255';
    }

    guestForm.clearErrors();
    
    // Simple frontend validation
    if (props.quiz.guest_info_required === 'name' || props.quiz.guest_info_required === 'both') {
      if (!guestForm.name.trim()) {
        guestForm.setError('name', 'Name is required');
        return;
      }
    }
    
    if (props.quiz.guest_info_required === 'email' || props.quiz.guest_info_required === 'both') {
      if (!guestForm.email.trim()) {
        guestForm.setError('email', 'Email is required');
        return;
      }
      // Simple email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(guestForm.email)) {
        guestForm.setError('email', 'Please enter a valid email address');
        return;
      }
    }

    // Submit with guest info
    router.post(route('quiz.start.guest', { quiz: props.quiz.id }), {
      guest_name: guestForm.name,
      guest_email: guestForm.email
    });
  } else {
    // Start without guest info
    router.post(route('quiz.start.guest', { quiz: props.quiz.id }));
  }
};


const hasQuestionPools = computed(() => {
  return props.quiz.question_pools && props.quiz.question_pools.length > 0;
});

const poolQuestionsCount = computed(() => {
  if (!hasQuestionPools.value) return 0;

  return props.quiz.question_pools.reduce((total, pool) => {
    // Access pivot data correctly - Laravel typically nests it under 'pivot'
    const questionsToShow = pool.pivot?.questions_to_show ?? pool.questions_count;
    return total + questionsToShow;
  }, 0);
});

const totalQuestions = computed(() => {
  return props.quiz.questions.length + poolQuestionsCount.value;
});

const currentDate = new Date();

const isQuizEarly = computed(() => {
  return props.quiz.starts_at && new Date(props.quiz.starts_at) > currentDate;
});

const isQuizExpired = computed(() => {
  return props.quiz.ends_at && new Date(props.quiz.ends_at) < currentDate;
});

const isQuizAvailable = computed(() => {
  if (props.quiz.starts_at && new Date(props.quiz.starts_at) > currentDate) {
    return false; // Quiz hasn't started yet
  }
  if (props.quiz.ends_at && new Date(props.quiz.ends_at) < currentDate) {
    return false; // Quiz has ended
  }
  if(props.remainingAttempt) {
    return true; //organization do not have attempt quota
  }
  return true; // Quiz is available
});

const canStartQuiz = computed(() => {
  return props.isAuthenticated &&
          props.isAssigned &&
          (props.attemptsRemaining === null || props.attemptsRemaining > 0) &&
          isQuizAvailable.value;
});
</script>

<style scoped>
/* Scoped styles remain unchanged */
</style>