<template>
    <PublicLayout>
      <div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <!-- Quiz Header -->
          <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h1 class="text-2xl font-bold text-gray-900">{{ quiz.title }}</h1>
            <div class="mt-1 flex items-center text-sm text-gray-500">
              <span>{{ quiz.questions.length }} questions</span>
            </div>
          </div>
          
          <!-- Quiz Content -->
          <div class="px-6 py-5">
            <div class="mb-6">
              <h2 class="text-lg font-medium text-gray-900">Instructions form {{ organisation }}</h2>
              <div class="mt-2 prose max-w-none text-gray-600" v-html="quiz.instructions || 'No specific instructions provided.'"></div>
            </div>
  
            <!-- Quiz Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <h3 class="text-md font-medium text-gray-900">Timing</h3>
                <ul class="mt-2 space-y-1 text-sm text-gray-600">
                  <li class="flex items-center">
                    <ClockIcon class="h-5 w-5 mr-2 text-gray-400" />
                    {{ quiz.time_limit ? `${quiz.time_limit} minutes` : 'No time limit' }}
                  </li>
                  <li v-if="quiz.starts_at" class="flex items-center">
                    <CalendarIcon class="h-5 w-5 mr-2 text-gray-400" />
                    Available from: {{ formatDate(quiz.starts_at) }}
                  </li>
                  <li v-if="quiz.ends_at" class="flex items-center">
                    <CalendarIcon class="h-5 w-5 mr-2 text-gray-400" />
                    Available until: {{ formatDate(quiz.ends_at) }}
                  </li>
                </ul>
              </div>
              
              <div>
                <h3 class="text-md font-medium text-gray-900">Grading</h3>
                <ul class="mt-2 space-y-1 text-sm text-gray-600">
                  <li class="flex items-center">
                    <CheckCircleIcon class="h-5 w-5 mr-2 text-gray-400" />
                    Passing score: {{ quiz.passing_score ? `${quiz.passing_score}%` : 'Not specified' }}
                  </li>
                  <li v-if="attemptsRemaining !== null" class="flex items-center">
                    <RefreshIcon class="h-5 w-5 mr-2 text-gray-400" />
                    Attempts remaining: {{ attemptsRemaining }}
                  </li>
                </ul>
              </div>
            </div>
  
            <!-- Auth State -->
            <div v-if="!isAuthenticated" class="auth-prompt">
            <p>You need to sign in to access this quiz</p>
            <Link :href="route('login')" class="btn btn-primary">
                Sign In
            </Link>
            </div>
            
            <div v-else-if="!isAssigned" class="assignment-warning">
            <p>This quiz hasn't been assigned to you</p>
            <p>Please contact your instructor for access</p>
            </div>
            
            <div v-else-if="attemptsRemaining !== null && attemptsRemaining <= 0" class="attempts-warning">
            <p>You have no attempts remaining for this quiz</p>
            </div>
            
            <div v-else>
            <button @click="startQuiz" class="btn btn-primary">
                Start Quiz
            </button>
            </div>
          </div>
        </div>
      </div>
    </PublicLayout>
  </template>
  
  <script setup>
  import { Head, Link, router } from '@inertiajs/vue3';
  import PublicLayout from '@/Layouts/PublicLayout.vue';
  import { computed } from 'vue';
  import { 
  ClockIcon, 
  CheckCircleIcon, 
  CalendarIcon, 
  ArrowPathIcon 
} from '@heroicons/vue/24/outline';
  
  const props = defineProps({
    quiz: Object,
    organisation: String,
    attemptsRemaining: [Number, null],
    isAuthenticated: Boolean,
    isAssigned: Boolean
  });
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
  };
  
  const startQuiz = () => {
    router.post(route('examinee.start', props.quiz.id), {}, {
      onSuccess: () => {
        router.get(route('examinee.attempt', { 
          quiz: props.quiz.id 
        }));
      }
    });
  };

  const canStartQuiz = computed(() => {
    return props.isAuthenticated && 
            props.isAssigned && 
            (props.attemptsRemaining === null || props.attemptsRemaining > 0);
    });

    
  </script>