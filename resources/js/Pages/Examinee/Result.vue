<template>
  <PublicLayout title="Quiz Results">
    <div v-if="attempt" class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Survey Thank You Page -->
        <div v-if="quizType === 'survey'" class="bg-white rounded-lg shadow-md p-12 text-center mb-4">
          <svg class="mx-auto h-12 w-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="mt-2 text-lg font-medium text-gray-900">Thank You!</h3>
          <p class="mt-1 text-sm text-gray-500">We appreciate your feedback.</p>
          <p v-if="quiz?.survey_thank_you_message" class="mt-4 text-gray-700">
            {{ quiz.survey_thank_you_message }}
          </p>
          <button
            @click="closeTab"
            class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
          >
            Close Page
          </button>
        </div>

        

        <!-- Exam Completion Message -->
        <div v-else-if="quizType === 'exam' && !showScores && !reviewQuestions" class="bg-white rounded-lg shadow-md p-12 text-center mb-4">
          <svg class="mx-auto h-12 w-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="mt-2 text-lg font-medium text-gray-900">Exam Submitted</h3>
          <p class="mt-1 text-sm text-gray-500">Your exam has been successfully submitted.</p>
          <p class="mt-4 text-sm text-gray-600">
            Results will be available after grading is complete.
          </p>
          <button
            @click="closeTab"
            class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
          >
            Close Page
          </button>
        </div>

        <!-- Exam Completion Message -->
        <div v-else-if="quizType === 'test'" class="bg-white rounded-lg shadow-md p-12 text-center mb-4">
          <svg class="mx-auto h-12 w-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="mt-2 text-lg font-medium text-gray-900">Test Submitted</h3>
          <p class="mt-1 text-sm text-gray-500">Your test has been successfully submitted.</p>
          <p class="mt-4 text-sm text-gray-600">
          </p>
          <button
            @click="closeTab"
            class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
          >
            Close Page
          </button>
        </div>

        <!--Scores Summary Card -->
          <div v-if="showScores" class="space-y-10 mb-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                  <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Test Completed</h1>
                    <p class="text-gray-600 mt-1">Result summary for {{ quiz?.title }}</p>
                  </div>
                  <span
                    :class="[
                      'px-3 py-1.5 rounded-full text-sm font-semibold',
                      percentage >= quiz.passing_score ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'
                    ]"
                  >
                    {{ percentage >= quiz.passing_score ? 'Passed' : 'Failed' }}
                  </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 text-center">
                  <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Points</p>
                    <p class="text-xl font-bold text-gray-900">{{ score }}<span class="text-base font-normal text-gray-600">/{{ totalPoints }}</span></p>
                  </div>
                  <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Percentage</p>
                    <p class="text-xl font-bold text-gray-900">{{ percentage }}%</p>
                  </div>
                  <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Correct</p>
                    <p class="text-xl font-bold text-gray-900">{{ correctCount }}<span class="text-base font-normal text-gray-600">/{{ questions.length }}</span></p>
                  </div>
                  <div v-if="completionTime" class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Time Taken</p>
                    <p class="text-xl font-bold text-gray-900">{{ formatTime(completionTime) }}</p>
                  </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div
                    class="bg-emerald-600 h-3 rounded-full transition-all duration-700 ease-out"
                    :style="{ width: `${percentage}%` }"
                  ></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                  <span>0%</span>
                  <span>100%</span>
                </div>
              </div>
            </div>
          </div>
          </div>

        <!-- Test Results (default) -->
        
        <div v-if="reviewQuestions && quizType !== 'survey'" class="max-w-4xl mx-auto sm:px-6 lg:px-8">
          <!-- Individual Question Results -->
          <div if="quizType === 'practice'" class="space-y-10">
            <div
              v-for="(question, index) in questions"
              :key="question.id"
              class="bg-white overflow-hidden shadow hover:shadow-md transition-shadow duration-300 sm:rounded-lg"
            >
              <div class="p-6">
                <div class="flex flex-wrap justify-between items-start gap-3 mb-5">
                  <div class="flex items-center flex-wrap gap-2">
                    <h2 class="text-lg font-semibold text-gray-900">
                      Question {{ index + 1 }}
                    </h2>
                    <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">
                      {{ questionTypeLabel(question.type) }}
                    </span>
                    <span class="text-sm text-gray-600">
                      ({{ question.points }} point{{ question.points !== 1 ? 's' : '' }})
                    </span>
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                    <span
                      v-if="getResponse(question.id) === null"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      <svg class="w-3 h-3 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      Not answered
                    </span>
                    <span
                      v-else
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        isCorrect(question.id)
                          ? 'bg-emerald-100 text-emerald-800'
                          : 'bg-rose-100 text-rose-800'
                      ]"
                    >
                      <svg
                        :class="[
                          'w-3 h-3 mr-1',
                          isCorrect(question.id) ? 'text-emerald-500' : 'text-rose-500'
                        ]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <template v-if="isCorrect(question.id)">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </template>
                        <template v-else>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </template>
                      </svg>
                      {{ isCorrect(question.id) ? 'Correct' : 'Incorrect' }}
                    </span>
                  </div>
                </div>

                <!-- Question Text -->
                <div class="mb-6 prose max-w-none text-gray-800" v-html="formatQuestionText(question)"></div>

                <!-- Question-specific result display -->
                <div v-if="question.type === 'multiple_choice'">
                  <MultipleChoiceResult
                    :question="question"
                    :response="getResponse(question.id)"
                  />
                </div>

                <div v-else-if="question.type === 'true_false'">
                  <TrueFalseResult
                    :question="question"
                    :response="getResponse(question.id)"
                  />
                </div>

                <div v-else-if="question.type === 'short_answer'">
                  <ShortAnswerResult
                    :question="question"
                    :response="getResponse(question.id)"
                  />
                </div>

                <div v-else-if="question.type === 'essay'">
                  <EssayResult
                    :question="question"
                    :response="getResponse(question.id)"
                  />
                </div>

                <div v-else-if="question.type === 'matching'">
                  <MatchingResult
                    :question="question"
                    :response="getResponse(question.id)"
                  />
                </div>

                <div v-else-if="question.type === 'ordering'">
                  <OrderingResult
                    :question="question"
                    :response="getResponse(question.id)"
                  />
                </div>

                <div v-else-if="question.type === 'fill_in_blank'">
                  <FillInTheBlankResult
                    :question="question"
                    :response="getResponse(question.id)"
                  />
                </div>
                <div v-else>
                  <p class="text-amber-600 text-sm italic">Unable to display result for this question type.</p>
                </div>

                <!-- Feedback and explanations -->
                <div v-if="question.explanation" class="mt-5 p-4 bg-blue-50 rounded-lg border border-blue-100">
                  <h3 class="flex items-center font-medium text-blue-800 mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Explanation
                  </h3>
                  <p class="text-blue-700 text-sm">{{ question.explanation }}</p>
                </div>

                <div
                  v-if="getFeedback(question.id)"
                  class="mt-4 p-4 rounded-lg border"
                  :class="{
                    'bg-emerald-50 border-emerald-100 text-emerald-700': isCorrect(question.id),
                    'bg-rose-50 border-rose-100 text-rose-700': !isCorrect(question.id)
                  }"
                >
                  <h3 class="flex items-center font-medium mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Feedback
                  </h3>
                  <p class="text-sm">{{ getFeedback(question.id) }}</p>
                </div>
              </div>
            </div>
          </div>
          </div>

        <!-- Results Not Found Fallback -->
        <div v-if="!attempt" class="bg-white rounded-lg shadow-md p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="mt-2 text-lg font-medium text-gray-900">Results Not Found</h3>
          <p class="mt-1 text-sm text-gray-500">Unable to load test results.</p>
        </div>
      </div>
    
  </PublicLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import MultipleChoiceResult from '@/Components/ResultComponents/MultipleChoiceResult.vue';
import TrueFalseResult from '@/Components/ResultComponents/TrueFalseResult.vue';
import ShortAnswerResult from '@/Components/ResultComponents/ShortAnswerResult.vue';
import EssayResult from '@/Components/ResultComponents/EssayResult.vue';
import MatchingResult from '@/Components/ResultComponents/MatchingResult.vue';
import OrderingResult from '@/Components/ResultComponents/OrderingResult.vue';
import FillInTheBlankResult from '@/Components/ResultComponents/FillInTheBlankResult.vue';

const props = defineProps({
  attempt: {
    type: Object,
    required: true
  },
  questions: {
    type: Array,
    required: true,
    default: () => []
  },
  quiz: {
    type: Object,
    required: true
  },
  
  responses: {
    type: Array,
    required: true,
    default: () => []
  },
  
});

const showResults = ref(false);

// Determine quiz type
const quizType = computed(() => props.quiz.quiz_type || 'exam');

const settings = computed(() => props.quiz.settings || {});
const showScores = computed(() => settings.value.show_scores);
const reviewQuestions = computed(() => settings.value.review_questions);

// --- Computed Properties ---
const score = computed(() => {
  const rawScore = props.attempt?.score;
  // Handle multiple possible types safely
  if (rawScore === null || rawScore === undefined) return 0;
  return Number(rawScore) || 0;
});

const totalPoints = computed(() => {
  if (!props.questions || !Array.isArray(props.questions)) return 0;
  
  return props.questions.reduce((sum, q) => {
    const points = q?.points;
    // Convert to number safely
    const numericPoints = Number(points) || 0;
    return sum + numericPoints;
  }, 0);
});

const percentage = computed(() => {
  if (totalPoints.value <= 0) return 0;
  
  const calculated = (score.value / totalPoints.value) * 100;
  // Handle edge cases and ensure integer
  return Math.round(calculated) || 0;
});
const correctCount = computed(() => props.responses.filter(r => r?.is_correct).length);
const completionTime = computed(() => props.attempt?.time_spent || 0);

// --- Helper Methods ---
const getResponse = (questionId) => {
    // Ensure response.answer is correctly accessed
    const response = props.responses.find(r => r?.question_id === questionId);
    return response?.answer ?? null;
};

const getFeedback = (questionId) => {
  const response = props.responses.find(r => r?.question_id === questionId);
  // Check both possible locations for feedback
  return response?.grading_data?.feedback || response?.feedback || null;
};

const isCorrect = (questionId) => {
    const response = props.responses.find(r => r?.question_id === questionId);
    return response ? response.is_correct : false;
};

const formatQuestionText = (question) => {
  if (!question?.question) return '';
  if (question.type === 'fill_in_the_blank') {
    // Use a more distinct placeholder for blanks in results
    return question.question.replace(/\[blank\]/gi, '<span class="inline-block min-w-[60px] px-2 py-0.5 mx-1 text-center align-middle bg-gray-100 border-b-2 border-dashed border-gray-400 rounded-sm text-gray-600 text-sm">____</span>');
  }
  return question.question;
};

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}m ${secs}s`;
};

const questionTypeLabel = (type) => {
  if (!type) return 'Unknown';
  const types = {
    'multiple_choice': 'MCQ',
    'true_false': 'True/False',
    'short_answer': 'Short Answer',
    'matching': 'Matching',
    'ordering': 'Ordering',
    'fill_in_the_blank': 'Fill in the Blank',
    'essay': 'Essay',
    'fill_the_blank': 'Fill in the Blank',
  };
  return types[type] || type;
};

const closeTab = () => {
  if (confirm('Are you sure you want to leave?')) {
    window.close();
    window.location.href = '/'; 
  }
};

const downloadResults = () => {
  // Basic implementation for downloading results
  alert('Download functionality would be implemented here.');
  console.log('Downloading results...');
  // Example: Could trigger an API call to generate and download a PDF
  // router.get(route('examinee.results.download', { attempt: props.attempt.id }));
};
</script>

<style scoped>
/* Scoped styles for question text formatting */
.prose :deep(p) {
  margin-bottom: 1em;
  line-height: 1.6;
}

/* Adjust prose for better list spacing if needed */
.prose :deep(ul),
.prose :deep(ol) {
  margin-top: 0.5em;
  margin-bottom: 1em;
  padding-left: 1.5em;
}

.prose :deep(li) {
  margin-bottom: 0.25em;
}
</style>