<template>
    <!-- Timer and Header -->
    <div class="bg-gray-800 text-white py-3 px-4">
      <div class="max-w-4xl mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">{{ quiz.title }}</h1>
        <div class="flex items-center space-x-4">
          <div v-if="timeLimit" class="flex items-center bg-gray-700 px-3 py-1 rounded">
            <ClockIcon class="h-5 w-5 mr-1" />
            <span :class="{ 'text-red-300': timeRemaining <= 300, 'animate-pulse': timeRemaining <= 60 }">
              {{ formattedTimeRemaining }}
            </span>
          </div>
          <button 
            @click="confirmSubmit"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm transition-colors"
            :disabled="submitting"
          >
            Submit Quiz
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
          <span>{{ progressPercentage }}% Complete</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div 
            class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
            :style="{ width: `${progressPercentage}%` }"
          ></div>
        </div>
      </div>

      <!-- Current Question -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-start mb-4">
          <span class="flex-shrink-0 bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 font-semibold">
            {{ currentQuestionIndex + 1 }}
          </span>
          <div class="min-w-0 flex-1">
            <h3 class="text-lg font-medium text-gray-900 leading-relaxed">
              {{ currentQuestion?.question || 'Loading question...' }}
            </h3>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
              <span class="bg-gray-100 px-2 py-1 rounded">
                {{ questionTypeLabel(currentQuestion?.type) }}
              </span>
              <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">
                {{ currentQuestion?.points || 0 }} point{{ (currentQuestion?.points || 0) !== 1 ? 's' : '' }}
              </span>
              <span v-if="currentQuestion?.time_limit" class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
                {{ currentQuestion.time_limit }} min limit
              </span>
              <span v-if="currentQuestion?.is_required" class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">
                Required
              </span>
            </div>
            
            <!-- Question Description -->
            <p v-if="currentQuestion?.description" class="mt-3 text-sm text-gray-600 italic">
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
              class="w-full h-auto rounded-lg border border-gray-200 shadow-sm cursor-pointer transition-transform hover:scale-105"
              @click="openImageModal"
              @error="handleImageError"
              loading="lazy"
            />
            <p class="text-xs text-gray-500 text-center mt-2">
              Click image to view full size
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
            class="mt-4"
          />
          <div v-else class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <div>
                <p class="text-yellow-800 font-medium">
                  Question type "{{ currentQuestion.type }}" is not supported yet.
                </p>
                <p class="text-sm text-yellow-700 mt-1">
                  Available types: {{ Object.keys(questionComponents).join(', ') }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="flex justify-center py-8">
          <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading question...</span>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <div class="flex justify-between items-center">
        <button
          @click="prevQuestion"
          :disabled="currentQuestionIndex === 0"
          class="bg-gray-500 hover:bg-gray-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-6 py-2 rounded-md transition-colors flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
          Previous
        </button>
        
        <!-- Question Navigation Dots -->
        <div class="flex space-x-1">
          <button
            v-for="(question, index) in questions"
            :key="question.id"
            @click="goToQuestion(index)"
            :class="[
              'w-3 h-3 rounded-full transition-colors',
              index === currentQuestionIndex ? 'bg-blue-600' : 
              answers[index] !== null && answers[index] !== undefined && answers[index] !== '' ? 'bg-green-500' : 'bg-gray-300'
            ]"
            :title="`Question ${index + 1}${answers[index] !== null && answers[index] !== undefined && answers[index] !== '' ? ' (Answered)' : ''}`"
          ></button>
        </div>
        
        <div class="flex space-x-2">
          <button
            v-if="currentQuestionIndex < questions.length - 1"
            @click="nextQuestion"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors flex items-center"
          >
            Next Question
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
          <button
            v-else-if="currentQuestionIndex === questions.length - 1"
            @click="confirmSubmit"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md transition-colors flex items-center"
            :disabled="submitting"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Submit Quiz
          </button>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" @click="closeImageModal">
      <div class="relative max-w-4xl max-h-full">
        <img 
          :src="currentQuestion?.image" 
          :alt="`Question ${currentQuestionIndex + 1} image - Full size`"
          class="max-w-full max-h-full object-contain rounded-lg"
          @click.stop
        />
        <button
          @click="closeImageModal"
          class="absolute top-4 right-4 bg-black bg-opacity-50 text-white rounded-full p-2 hover:bg-opacity-75 transition-opacity"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded text-sm">
          Question {{ currentQuestionIndex + 1 }} Image
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showSubmitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900">Submit Quiz</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500 mb-4">
              Are you sure you want to submit your quiz? This action cannot be undone.
            </p>
            <div v-if="unansweredQuestions > 0" class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
              <p class="text-yellow-800 text-sm font-medium">
                ⚠️ You have {{ unansweredQuestions }} unanswered question(s).
              </p>
            </div>
            <div class="text-left space-y-2 text-sm text-gray-600">
              <p>• Answered: {{ questions.length - unansweredQuestions }} / {{ questions.length }}</p>
              <p v-if="timeLimit">• Time remaining: {{ formattedTimeRemaining }}</p>
            </div>
          </div>
          <div class="flex justify-center space-x-3 mt-6">
            <button
              @click="showSubmitModal = false"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors"
            >
              Cancel
            </button>
            <button
              @click="submitQuiz"
              :disabled="submitting"
              class="bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white px-4 py-2 rounded transition-colors flex items-center"
            >
              <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ submitting ? 'Submitting...' : 'Submit Quiz' }}
            </button>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { ClockIcon } from '@heroicons/vue/24/outline';

// Import question components
import MCQQuestion from '@/Components/Questions/MCQ.vue';
import TrueFalseQuestion from '@/Components/Questions/TrueFalse.vue';
import ShortAnswerQuestion from '@/Components/Questions/ShortAnswer.vue';
import MatchingQuestion from '@/Components/Questions/Matching.vue';
import Essay from '@/Components/Questions/Essay.vue';
import FillInTheBlank from '@/Components/Questions/FillInTheBlank.vue';
import Ordering from '@/Components/Questions/Ordering.vue';

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
  }
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
const timeRemaining = ref(props.timeLimit ? props.timeLimit * 60 : 0);
const timer = ref(null);

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

// Initialize component
onMounted(() => {
  console.log('Quiz component mounted');
  console.log('Props:', props);
  console.log('Questions:', props.questions);
  
  // Initialize answers array
  if (props.questions && props.questions.length > 0) {
    answers.value = new Array(props.questions.length).fill(null);
  }
  
  // Start timer if there's a time limit
  if (props.timeLimit && props.timeLimit > 0) {
    startTimer();
  }
  
  // Set up proctoring
  setupProctoring();
  
  // Warn before leaving
  window.addEventListener('beforeunload', handleBeforeUnload);
});

// Cleanup
onUnmounted(() => {
  if (timer.value) {
    clearInterval(timer.value);
  }
  window.removeEventListener('beforeunload', handleBeforeUnload);
});

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
  if (currentQuestionIndex.value < props.questions.length - 1) {
    currentQuestionIndex.value++;
  }
}

function prevQuestion() {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--;
  }
}

function goToQuestion(index) {
  if (index >= 0 && index < props.questions.length) {
    currentQuestionIndex.value = index;
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
  // Optionally show a placeholder or error message
}

// Answer handling
function updateAnswer(value) {
  console.log('Updating answer for question', currentQuestionIndex.value, 'with value:', value);
  answers.value[currentQuestionIndex.value] = value;
}

// Submission functions
function confirmSubmit() {
  showSubmitModal.value = true;
}

const submitQuiz = async () => {
  submitting.value = true;
  showSubmitModal.value = false;

  try {
    // Prepare answers in the correct format
    const formattedAnswers = props.questions.reduce((acc, question, index) => {
      const answer = answers.value[index];
      if (answer !== null && answer !== undefined && answer !== '') {
        // For array answers (like matching/ordering), ensure proper format
        if (Array.isArray(answer)) {
          acc[question.id] = answer;
        } 
        // For object answers (if any), stringify if needed
        else if (typeof answer === 'object' && answer !== null) {
          acc[question.id] = JSON.stringify(answer);
        }
        // For simple values
        else {
          acc[question.id] = answer;
        }
      }
      return acc;
    }, {});

    const submissionData = {
      attempt_id: props.attempt.id,
      answers: formattedAnswers,
      time_spent: props.timeLimit ? (props.timeLimit * 60 - timeRemaining.value) : null,
      proctoring_flags: {
        tab_switches: window.tabSwitchCount || 0,
        fullscreen_exits: window.fullscreenExitCount || 0,
      }
    };

    console.log('Submitting quiz with data:', submissionData);

    await router.post(route('examinee.submit', { quiz: props.quiz.id }), submissionData, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });
    
  } catch (error) {
    console.error('Submission failed:', error);
    alert('Submission failed: ' + (error?.message || 'Unknown error'));
    submitting.value = false;
  }
};

// Convert answers array to question_id => answer format
const normalizeAnswers = (answersArray) => {
  if (!props.questions || !answersArray) return {};
  
  return props.questions.reduce((acc, question, index) => {
    const answer = answersArray[index];
    if (answer !== null && answer !== undefined && answer !== '') {
      acc[question.id] = answer;
    }
    return acc;
  }, {});
};

// Proctoring setup
function setupProctoring() {
  // Track tab switches
  window.tabSwitchCount = 0;
  window.addEventListener('blur', () => {
    if (document.visibilityState === 'hidden') {
      window.tabSwitchCount++;
      console.log('Tab switch detected. Count:', window.tabSwitchCount);
    }
  });

  // Track fullscreen exits
  window.fullscreenExitCount = 0;
  document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement) {
      window.fullscreenExitCount++;
      console.log('Fullscreen exit detected. Count:', window.fullscreenExitCount);
    }
  });
}

function handleBeforeUnload(e) {
  if (!submitting.value && unansweredQuestions.value > 0) {
    e.preventDefault();
    e.returnValue = 'You have unsaved answers. Are you sure you want to leave?';
  }
}
</script>