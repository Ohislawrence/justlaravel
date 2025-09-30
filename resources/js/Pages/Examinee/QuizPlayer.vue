<template>
  <Head>
      <title>{{ quiz.title }}</title>
  </Head>
  <!-- Timer and Header -->
  <div class="bg-white text-gray-800 py-3 px-4 shadow-sm rounded-b-lg border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
      <h1 class="text-xl font-bold text-gray-900 truncate w-full sm:w-auto">{{ quiz.title }}</h1>
      <div class="flex items-center justify-between w-full sm:w-auto gap-3">
        <div v-if="timeLimit" class="flex items-center bg-gray-100 px-3 py-1.5 rounded-full text-sm">
          <ClockIcon class="h-4 w-4 mr-1 text-gray-600" />
          <span :class="{ 'text-red-600 font-medium': timeRemaining <= 300, 'animate-pulse': timeRemaining <= 60 }">
            {{ formattedTimeRemaining }}
            <span v-if="remainingTime !== null" class="text-xs text-gray-500 ml-1">
              (resumed)
            </span>
          </span>
        </div>
        <button
          @click="confirmSubmit"
          class="bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 focus:ring-offset-2 text-white px-4 py-1.5 rounded-md text-sm font-medium transition-colors shadow-sm focus:outline-none focus:ring-2"
          :disabled="submitting"
        >
          <span v-if="submitting">Submitting...</span>
          <span v-else>Submit</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Main Quiz Area -->
  <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6">
    
    <!-- Progress Bar -->
    <div class="mb-6">
      <div class="flex justify-between text-sm text-gray-600 mb-1">
        <span>Question {{ currentQuestionIndex + 1 }} of {{ questions.length }}</span>
        <span class="font-medium text-emerald-600">{{ progressPercentage }}% Complete</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div
          class="bg-emerald-600 h-2.5 rounded-full transition-all duration-500 ease-out"
          :style="{ width: `${progressPercentage}%` }"
        ></div>
      </div>
    </div>

    <!-- Current Question -->
    <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300 p-6 mb-6 border border-gray-200">
      <div class="flex items-start mb-5">
        <span class="flex-shrink-0 bg-emerald-100 text-emerald-800 rounded-full w-9 h-9 flex items-center justify-center mr-4 font-bold text-sm">
          {{ currentQuestionIndex + 1 }}
        </span>
        <div class="min-w-0 flex-1">
          <h3 class="text-lg font-semibold text-gray-900 leading-relaxed">
            {{ currentQuestion?.question || 'Loading question...' }}
          </h3>
          <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
            <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">
              {{ questionTypeLabel(currentQuestion?.type) }}
            </span>
            <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-medium">
              {{ currentQuestion?.points || 0 }} pt{{ (currentQuestion?.points || 0) !== 1 ? 's' : '' }}
            </span>
            <span v-if="currentQuestion?.time_limit" class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full font-medium">
              {{ currentQuestion.time_limit }} min limit
            </span>
            <span v-if="currentQuestion?.is_required" class="bg-rose-100 text-rose-700 px-2.5 py-1 rounded-full font-medium text-xs">
              Required
            </span>
          </div>
          <!-- Question Description -->
          <p v-if="currentQuestion?.description" class="mt-4 text-sm text-gray-600">
            {{ currentQuestion.description }}
          </p>
        </div>
      </div>

      <!-- Question Image -->
      <div v-if="currentQuestion?.image" class="mb-6 flex justify-center">
        <div class="max-w-2xl w-full">
          <img
            :src="currentQuestion.image"
            :alt="`Question ${currentQuestionIndex + 1} image`"
            class="w-full h-auto rounded-lg border border-gray-200 shadow-sm cursor-zoom-in transition-transform hover:scale-[1.02]"
            @click="openImageModal"
            @error="handleImageError"
            loading="lazy"
          />
          <p class="text-xs text-gray-500 text-center mt-2">
            Click to enlarge
          </p>
        </div>
      </div>

      <!-- Question Component -->
      <div v-if="currentQuestion">
        <component
          v-if="getQuestionComponent(currentQuestion.type)"
          :is="getQuestionComponent(currentQuestion.type)"
          :question="currentQuestion"
          :answer="answers[currentQuestionIndex]"
          @update="updateAnswer"
          class="mt-2"
        />
        <div v-else class="p-4 bg-amber-50 border border-amber-200 rounded-md">
          <div class="flex items-start">
            <svg class="h-5 w-5 text-amber-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="text-amber-800 font-medium">
                Unsupported Question Type
              </p>
              <p class="text-sm text-amber-700 mt-1">
                The question type "{{ currentQuestion.type }}" is not currently supported in this quiz interface.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="flex justify-center py-10">
        <div class="flex items-center text-gray-600">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Loading question...</span>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
      <button
        @click="prevQuestion"
        :disabled="currentQuestionIndex === 0"
        class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed text-gray-800 px-5 py-2 rounded-md font-medium transition-colors flex items-center justify-center"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Previous
      </button>

      <!-- Question Navigation Dots -->
      <div class="flex space-x-1.5 justify-center">
        <button
          v-for="(question, index) in questions"
          :key="question.id"
          @click="goToQuestion(index)"
          :class="[
            'w-3 h-3 rounded-full transition-colors focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-emerald-500',
            index === currentQuestionIndex ? 'bg-emerald-600 scale-125' :
            answers[index] !== null && answers[index] !== undefined && answers[index] !== '' ? 'bg-emerald-500' : 'bg-gray-300'
          ]"
          :title="`Go to Question ${index + 1}${answers[index] !== null && answers[index] !== undefined && answers[index] !== '' ? ' (Answered)' : ''}`"
          :aria-label="`Go to Question ${index + 1}`"
        ></button>
      </div>

      <div class="flex space-x-2 w-full sm:w-auto">
        <button
          v-if="currentQuestionIndex < questions.length - 1"
          @click="nextQuestion"
          class="w-full bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 text-white px-5 py-2 rounded-md font-medium transition-colors flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm"
        >
          Next
          <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
        <button
          v-else-if="currentQuestionIndex === questions.length - 1"
          @click="confirmSubmit"
          class="w-full bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 text-white px-5 py-2 rounded-md font-medium transition-colors flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 shadow-sm"
          :disabled="submitting"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span v-if="submitting">Submitting...</span>
          <span v-else>Submit</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Image Modal -->
  <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" @click="closeImageModal">
    <div class="relative max-w-6xl max-h-[90vh] w-full h-full flex items-center justify-center">
      <img
        :src="currentQuestion?.image"
        :alt="`Question ${currentQuestionIndex + 1} image - Full size`"
        class="max-w-full max-h-full object-contain rounded-lg shadow-xl border border-gray-700"
        @click.stop
      />
      <button
        @click="closeImageModal"
        class="absolute top-4 right-4 bg-black bg-opacity-60 text-white rounded-full p-2 hover:bg-opacity-80 transition-opacity focus:outline-none focus:ring-2 focus:ring-white"
        aria-label="Close image"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <div class="absolute bottom-4 left-4 bg-black bg-opacity-60 text-white px-3 py-1.5 rounded-md text-sm font-medium">
        Question {{ currentQuestionIndex + 1 }} Image
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div v-if="showSubmitModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl transform transition-all">
      <div class="px-6 pt-6 pb-4">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 mb-4">
          <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-bold text-center text-gray-900">Submit</h3>
        <div class="mt-4 text-center">
          <p class="text-sm text-gray-600 mb-5">
            Are you sure you want to submit your quiz? This action cannot be undone.
          </p>
          <div v-if="unansweredQuestions > 0" class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-5">
            <p class="text-amber-800 text-sm font-semibold flex items-center justify-center">
              <svg class="w-5 h-5 mr-1.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              {{ unansweredQuestions }} Unanswered Question{{ unansweredQuestions !== 1 ? 's' : '' }}
            </p>
            <p class="text-xs text-amber-700 mt-1">You can go back to review them.</p>
          </div>
          <div class="text-left bg-gray-50 rounded-lg p-3 text-xs text-gray-600 space-y-1">
            <p class="font-medium text-gray-700">Summary:</p>
            <p>• Answered: <span class="font-medium">{{ questions.length - unansweredQuestions }}</span> / {{ questions.length }}</p>
            <p v-if="timeLimit">• Time remaining: <span class="font-medium">{{ formattedTimeRemaining }}</span></p>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 rounded-b-xl">
        <button
          @click="showSubmitModal = false"
          type="button"
          class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:text-sm"
        >
          Cancel
        </button>
        <button
          @click="submitQuiz"
          :disabled="submitting"
          type="button"
          class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:text-sm disabled:opacity-75"
        >
          <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ submitting ? 'Submitting...' : 'Yes, Submit' }}
        </button>
      </div>
    </div>
  </div>
<div v-if="!props.quiz.is_public">
  <Proctoring
      ref="proctoringRef"
      :quiz-attempt-id="attempt.id"
      :is-active="true"
      :prevent-cheating="true"
      @violation-detected="handleViolationDetected"
      @fingerprint-generated="handleFingerprintGenerated"
      @proctoring-data-update="handleProctoringDataUpdate"
      @cheating-prevented="handleCheatingPrevented"
    />
</div>  
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import { ClockIcon } from '@heroicons/vue/24/outline';
import MCQQuestion from '@/Components/Questions/MCQ.vue';
import TrueFalseQuestion from '@/Components/Questions/TrueFalse.vue';
import ShortAnswerQuestion from '@/Components/Questions/ShortAnswer.vue';
import MatchingQuestion from '@/Components/Questions/Matching.vue';
import Essay from '@/Components/Questions/Essay.vue';
import FillInTheBlank from '@/Components/Questions/FillInTheBlank.vue';
import Ordering from '@/Components/Questions/Ordering.vue';
import Proctoring from '@/Components/Proctoring.vue';

const props = defineProps({
  quiz: {
    type: Object,
    required: true
  },
  attempt: {
    type: Object,
    required: true
  },
  questions: {
    type: Array,
    required: true,
    default: () => []
  },
  timeLimit: {
    type: Number,
    default: null
  },
  remainingTime: { 
    type: Number,
    default: null
  },
  attemptID: Number,
  existingAnswers: {
    type: Object,
    default: () => ({})
  }
});




const proctoringRef = ref(null)
const proctoringData = ref({
  violation_count: 0,
  fingerprint: null,
  fingerprint_components: null,
  fingerprint_recorded_at: null,
  proctoring_data: {}
})

const questionStartTime = ref(null);
const questionTimers = ref({});
const totalTimeSpent = ref(0);
const questionTimeSpent = ref({});


// Add these for error handling
const showSubmissionError = ref(false);
const submissionError = ref('');

const currentQuestionId = computed(() => {
  return currentQuestion.value?.id;
});


// Question type mapping
const questionTypeLabel = (type) => {
  if (!type) return 'Unknown';
  const types = {
    'multiple_choice': 'Multiple Choice',
    'true_false': 'True/False',
    'short_answer': 'Short Answer',
    'matching': 'Matching',
    'ordering': 'Ordering',
    'fill_in_the_blank': 'Fill In The Blank',
    'essay': 'Essay'
  };
  return types[type] || type;
};

// Question components mapping
const questionComponents = {
  multiple_choice: MCQQuestion,
  true_false: TrueFalseQuestion,
  short_answer: ShortAnswerQuestion,
  matching: MatchingQuestion,
  ordering: Ordering,
  fill_in_the_blank: FillInTheBlank,
  essay: Essay
};

// Reactive state
const currentQuestionIndex = ref(0);
const answers = ref([]);
const showSubmitModal = ref(false);
const showImageModal = ref(false);
const submitting = ref(false);
const timeRemaining = ref(props.remainingTime ? props.remainingTime : (props.timeLimit ? props.timeLimit * 60 : 0));
const timer = ref(null);

const lastSaveTime = ref(Date.now());
const isSaving = ref(false);

// Computed properties
const currentQuestion = computed(() => {
  if (!props.questions || !props.questions.length || currentQuestionIndex.value >= props.questions.length) {
    return null;
  }
  return props.questions[currentQuestionIndex.value];
});

const progressPercentage = computed(() => {
  if (!props.questions || props.questions.length === 0) return 0;
  return Math.round(((currentQuestionIndex.value + 1) / props.questions.length) * 100);
});

const formattedTimeRemaining = computed(() => {
  const minutes = Math.floor(timeRemaining.value / 60);
  const seconds = timeRemaining.value % 60;
  return `${minutes}:${seconds.toString().padStart(2, '0')}`;
});

const unansweredQuestions = computed(() =>
  answers.value.filter(answer => answer === null || answer === undefined || answer === '').length
);

const getQuestionComponent = (type) => {
  if (!type) {
    console.error('Question type is undefined');
    return null;
  }
  const component = questionComponents[type];
  if (!component) {
    console.error(`No component found for question type: ${type}`);
    return null;
  }
  return component;
};

// Helper function to convert ISO datetime to MySQL format
const convertToMySQLDateTime = (isoString) => {
  if (!isoString) return null;
  try {
    const date = new Date(isoString);
    return date.toISOString().slice(0, 19).replace('T', ' ');
  } catch (error) {
    console.error('Error converting datetime:', error);
    return null;
  }
};

// Proctoring event handlers
const handleViolationDetected = (violation) => {
  console.log('Violation detected:', violation);
  proctoringData.value.violation_count = violation.count;
};

const handleFingerprintGenerated = (fingerprintData) => {
  console.log('Fingerprint generated:', fingerprintData);
  // Update proctoring data with MySQL-compatible datetime
  proctoringData.value.fingerprint = fingerprintData.fingerprint;
  proctoringData.value.fingerprint_components = fingerprintData.components;
  proctoringData.value.fingerprint_recorded_at = convertToMySQLDateTime(fingerprintData.recorded_at);
};

const handleProctoringDataUpdate = (data) => {
  console.log('Proctoring data update:', data);
  // Update proctoring data based on the type
  switch (data.type) {
    case 'violation':
      proctoringData.value.violation_count = data.data.count;
      break;
    case 'violations_batch':
      // Handle batch of violations
      break;
    case 'fingerprint':
      // Update fingerprint data with MySQL-compatible datetime
      proctoringData.value.fingerprint = data.data.fingerprint;
      proctoringData.value.fingerprint_components = data.data.fingerprint_components;
      proctoringData.value.fingerprint_recorded_at = convertToMySQLDateTime(data.data.fingerprint_recorded_at);
      break;
    case 'final_data':
      // Update all proctoring data before submission with MySQL-compatible datetime
      proctoringData.value.violation_count = data.data.violation_count;
      proctoringData.value.fingerprint = data.data.fingerprint;
      proctoringData.value.fingerprint_components = data.data.fingerprint_components;
      proctoringData.value.fingerprint_recorded_at = convertToMySQLDateTime(data.data.fingerprint_recorded_at);
      proctoringData.value.proctoring_data = data.data.proctoring_data;
      break;
  }
};

const handleCheatingPrevented = (type) => {
  console.log('Cheating prevented:', type);
};

// Initialize component
onMounted(() => {
  console.log('Quiz component mounted');

  if (props.questions && props.questions.length > 0) {
    answers.value = props.questions.map(question => {
      return props.existingAnswers[question.id] !== undefined 
        ? props.existingAnswers[question.id] 
        : null;
    });
  }
  
  // Initialize answers array
  if (props.questions && props.questions.length > 0) {
    answers.value = new Array(props.questions.length).fill(null);
  }
  
  // Start timer with remaining time instead of full time
  if (props.timeLimit && props.timeLimit > 0) {
    startTimer();
  }
  
  // Start tracking time for the first question
  startQuestionTimer();
  
  // If we have remaining time, update totalTimeSpent
  if (props.remainingTime !== null && props.timeLimit) {
    const elapsedTime = (props.timeLimit * 60) - props.remainingTime;
    totalTimeSpent.value = Math.max(0, elapsedTime);
  }
});

function startQuestionTimer() {
  if (currentQuestionId.value) {
    questionStartTime.value = Date.now();
    
    // Start a timer to periodically save progress
    questionTimers.value[currentQuestionId.value] = setInterval(() => {
      saveProgress();
    }, 10000); 
  }
}

// Update the stopQuestionTimer function to return time spent
function stopQuestionTimer() {
  if (currentQuestionId.value && questionStartTime.value) {
    const timeSpent = Math.floor((Date.now() - questionStartTime.value) / 1000);
    
    // Store time spent for this question
    if (!questionTimeSpent.value[currentQuestionId.value]) {
      questionTimeSpent.value[currentQuestionId.value] = 0;
    }
    questionTimeSpent.value[currentQuestionId.value] += timeSpent;
    
    // Clear the interval timer
    if (questionTimers.value[currentQuestionId.value]) {
      clearInterval(questionTimers.value[currentQuestionId.value]);
      delete questionTimers.value[currentQuestionId.value];
    }
    
    return timeSpent;
  }
  return 0;
}

async function saveProgress() {
  if (!currentQuestionId.value || !questionStartTime.value || isSaving.value) return;
  
  const now = Date.now();
  const timeSpent = Math.floor((now - questionStartTime.value) / 1000);
  const timeSinceLastSave = Math.floor((now - lastSaveTime.value) / 1000);
  
  if (timeSinceLastSave < 2) return;
  
  isSaving.value = true;
  
  try {
    // Get CSRF token properly
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
      throw new Error('CSRF token not found');
    }

    const response = await fetch(route('api.quiz.save-progress', { 
      quiz: props.quiz.id,
      attempt: props.attempt.id 
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        question_id: currentQuestionId.value,
        answer: answers.value[currentQuestionIndex.value],
        time_spent: timeSpent,
        total_time_spent: totalTimeSpent.value + timeSpent
      }),
      credentials: 'include' // Important for sending cookies
    });
    
    if (!response.ok) {
      // Check if it's specifically a 401 error
      if (response.status === 401) {
        throw new Error('Authentication failed. Please refresh the page.');
      }
      throw new Error(`Save failed: ${response.status}`);
    }
    
    const data = await response.json();
    if (!data.success) {
      throw new Error('Save failed');
    }
    
    questionStartTime.value = now;
    lastSaveTime.value = now;
    totalTimeSpent.value += timeSpent;
  } catch (error) {
    console.error('Failed to save progress:', error);
    // Don't show error to user for auto-save functionality
  } finally {
    isSaving.value = false;
  }
}

const periodicSaver = ref(null);


// Cleanup
onUnmounted(() => {
  // Stop all question timers
  Object.values(questionTimers.value).forEach(timerId => {
    clearInterval(timerId);
  });
  
  window.removeEventListener('beforeunload', handleBeforeUnload);
  document.removeEventListener('visibilitychange', handleVisibilityChange);
  
  if (timer.value) {
    clearInterval(timer.value);
  }

  if (periodicSaver.value) {
    clearInterval(periodicSaver.value);
  }
});

const handleVisibilityChange = () => {
  if (document.visibilityState === 'visible') {
    saveProgress();
  }
};

document.addEventListener('visibilitychange', handleVisibilityChange);

// Timer functions
function startTimer() {
  timer.value = setInterval(() => {
    timeRemaining.value--;
    if (timeRemaining.value <= 0) {
      clearInterval(timer.value);
      alert('Time is up! Your quiz will be submitted automatically.');
      submitQuiz();
    }
  }, 1000);
}

// Navigation functions
function nextQuestion() {
  const timeSpent = stopQuestionTimer();
  if (timeSpent > 0) {
    totalTimeSpent.value += timeSpent;
  }
  
  if (currentQuestionIndex.value < props.questions.length - 1) {
    currentQuestionIndex.value++;
    startQuestionTimer();
  }
}

async function saveProgressImmediate() {
  if (!currentQuestionId.value || !questionStartTime.value || isSaving.value) return;
  
  const now = Date.now();
  const timeSpent = Math.floor((now - questionStartTime.value) / 1000);
  
  if (timeSpent <= 0) return;
  
  isSaving.value = true;
  
  try {
    const response = await fetch(route('api.quiz.save-progress', { 
      quiz: props.quiz.id,
      attempt: props.attempt.id 
    }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        question_id: currentQuestionId.value,
        answer: answers.value[currentQuestionIndex.value],
        time_spent: timeSpent,
        total_time_spent: totalTimeSpent.value + timeSpent
      }),
      credentials: 'include'
    });
    
    if (!response.ok) {
      throw new Error('Save failed');
    }
    
    const data = await response.json();
    if (!data.success) {
      throw new Error('Save failed');
    }
    
    totalTimeSpent.value += timeSpent;
  } catch (error) {
    console.error('Failed to save progress:', error);
  } finally {
    isSaving.value = false;
  }
}

function prevQuestion() {
  const timeSpent = stopQuestionTimer();
  if (timeSpent > 0) {
    totalTimeSpent.value += timeSpent;
  }
  
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--;
    startQuestionTimer();
  }
}

function goToQuestion(index) {
  const timeSpent = stopQuestionTimer();
  if (timeSpent > 0) {
    totalTimeSpent.value += timeSpent;
  }
  
  if (index >= 0 && index < props.questions.length) {
    currentQuestionIndex.value = index;
    startQuestionTimer();
  }
}

// Image handling
function openImageModal() {
  showImageModal.value = true;
}

function closeImageModal() {
  showImageModal.value = false;
}

function handleImageError(event) {
  console.error('Failed to load question image:', event.target.src);
  event.target.style.display = 'none';
  const container = event.target.parentElement;
  if (container) {
    const errorMsg = document.createElement('div');
    errorMsg.className = 'text-center text-gray-500 py-4';
    errorMsg.textContent = 'Image failed to load.';
    container.appendChild(errorMsg);
  }
}

// Answer handling
function updateAnswer(value) {
  console.log('Updating answer for question', currentQuestionIndex.value, 'with value:', value);
  answers.value[currentQuestionIndex.value] = value;
}

const handleBeforeUnload = (event) => {
  const timeSpent = stopQuestionTimer();
  if (timeSpent > 0) {
    // Use sendBeacon for reliable delivery on page close
    const data = new Blob([JSON.stringify({
      question_id: currentQuestionId.value,
      answer: answers.value[currentQuestionIndex.value],
      time_spent: timeSpent,
      total_time_spent: totalTimeSpent.value + timeSpent,
      is_closing: true
    })], { type: 'application/json' });
    
    navigator.sendBeacon(
      route('api.quiz.save-progress', { 
        quiz: props.quiz.id,
        attempt: props.attempt.id 
      }),
      data
    );
  }
};

// Add beforeunload listener
window.addEventListener('beforeunload', handleBeforeUnload);


// Submission functions
function confirmSubmit() {
  showSubmitModal.value = true;
}

const submitQuiz = async () => {
  if (submitting.value) return;
  
  submitting.value = true;
  showSubmitModal.value = false;

  const finalTimeSpent = stopQuestionTimer();
  if (finalTimeSpent > 0) {
    totalTimeSpent.value += finalTimeSpent;
    if (currentQuestionId.value) {
      if (!questionTimeSpent.value[currentQuestionId.value]) {
        questionTimeSpent.value[currentQuestionId.value] = 0;
      }
      questionTimeSpent.value[currentQuestionId.value] += finalTimeSpent;
    }
  }

  // Calculate total time spent including any previous time
  const totalElapsedTime = totalTimeSpent.value;
  
  try {
    // Validate we have required data
    if (!props.attempt?.id || !props.quiz?.id) {
      throw new Error('Missing quiz or attempt information');
    }

    // Submit proctoring violations first
    if (proctoringRef.value) {
      await proctoringRef.value.submitViolations();
      
      // Get the latest proctoring data after submitting violations
      const latestProctoringData = proctoringRef.value.getProctoringData();
      if (latestProctoringData) {
        proctoringData.value.violation_count = latestProctoringData.violation_count;
        proctoringData.value.fingerprint = latestProctoringData.fingerprint;
        proctoringData.value.fingerprint_components = latestProctoringData.fingerprint_components;
        proctoringData.value.fingerprint_recorded_at = convertToMySQLDateTime(latestProctoringData.fingerprint_recorded_at);
        proctoringData.value.proctoring_data = latestProctoringData.violations;
      }
    }

    // Prepare answers
    const formattedAnswers = props.questions.reduce((acc, question, index) => {
      const answer = answers.value[index];
      const questionId = question.id;
      
      if (answer !== null && answer !== undefined && answer !== '' && questionId) {
        switch (question.type) {
          case 'ordering':
            acc[questionId] = {
              answer: Array.isArray(answer) ? answer : [answer],
              time_spent: questionTimeSpent.value[questionId] || 0
            };
            break;
          case 'fill_in_the_blank':
            acc[questionId] = {
              answer: typeof answer === 'string' ? answer.trim() : answer,
              time_spent: questionTimeSpent.value[questionId] || 0
            };
            break;
          case 'matching':
            if (typeof answer === 'object' && answer !== null) {
              acc[questionId] = {
                answer: Object.keys(answer).length > 0 ? answer : null,
                time_spent: questionTimeSpent.value[questionId] || 0
              };
            } else {
              acc[questionId] = {
                answer: null,
                time_spent: questionTimeSpent.value[questionId] || 0
              };
            }
            break;
          default:
            acc[questionId] = {
              answer: answer,
              time_spent: questionTimeSpent.value[questionId] || 0
            };
        }
      } else {
        // Include time spent even for unanswered questions
        acc[questionId] = {
          answer: null,
          time_spent: questionTimeSpent.value[questionId] || 0
        };
      }
      return acc;
    }, {});

    // Prepare submission data with proper proctoring structure
    const submissionData = {
      attempt_id: props.attempt.id,
      answers: formattedAnswers,
      time_spent: totalTimeSpent.value,
      violation_count: proctoringData.value.violation_count || 0,
      fingerprint: proctoringData.value.fingerprint || null,
      fingerprint_components: proctoringData.value.fingerprint_components || null,
      fingerprint_recorded_at: proctoringData.value.fingerprint_recorded_at || null,
      proctoring_data: proctoringData.value.proctoring_data || {}
    };

    //console.debug('Submission payload:', submissionData);
    
    // Submit with proper error handling
    if(props.quiz.is_public){
      const response = await router.post(
      route('quiz.submit.guest', { quiz: props.quiz.id }), 
      submissionData, 
      {
        onError: (errors) => {
          if (errors.message) {
            throw new Error(errors.message);
          }
          throw new Error('Submission failed. Please try again.');
        }
      }
    );
    }else{
      const response = await router.post(
      route('examinee.submit', { quiz: props.quiz.id }), 
      submissionData, 
      {
        onError: (errors) => {
          if (errors.message) {
            throw new Error(errors.message);
          }
          throw new Error('Submission failed. Please try again.');
        }
      }
    );
    }
    

    return response;

  } catch (error) {
    console.error('Submission error:', error);
    showSubmissionError.value = true;
    submissionError.value = error.message || 'Failed to submit. Please try again.';
    submitting.value = false;
    throw error;
  }
};

// Clean up the event listener
onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange);
});
</script>

<style scoped>
.violation-counter {
  position: fixed;
  top: 50px;
  left: 10px;
  background: rgba(255, 193, 7, 0.1);
  color: #ffc107;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 12px;
  border: 1px solid #ffc107;
}
</style>