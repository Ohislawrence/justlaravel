<template>
  <PublicLayout>
  <div class="container mx-auto px-4 py-8" v-if="attempt">
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Quiz Results</h1>
      <div class="flex flex-wrap justify-between items-center mb-6">
        <div class="text-lg">
          <span class="font-semibold">Score:</span>
          <span class="ml-2">{{ score }} / {{ totalPoints }}</span>
        </div>
        <div class="text-lg">
          <span class="font-semibold">Percentage:</span>
          <span class="ml-2">{{ percentage }}%</span>
        </div>
        <div class="text-lg">
          <span class="font-semibold">Correct Answers:</span>
          <span class="ml-2">{{ correctCount }} / {{ questions.length }}</span>
        </div>
        <div class="text-lg" v-if="completionTime">
          <span class="font-semibold">Time:</span>
          <span class="ml-2">{{ formatTime(completionTime) }}</span>
        </div>
      </div>
      
      <div class="w-full bg-gray-200 rounded-full h-4 mb-6">
        <div 
          class="bg-blue-600 h-4 rounded-full" 
          :style="{ width: `${percentage}%` }"
        ></div>
      </div>
    </div>

    <div class="space-y-8">
      <div 
        v-for="(question, index) in questions" 
        :key="question.id"
        class="bg-white rounded-lg shadow-md p-6"
        :class="{
          'border-l-4 border-green-500': isCorrect(question.id),
          'border-l-4 border-red-500': !isCorrect(question.id)
        }"
      >
        <div class="flex justify-between items-start mb-4">
          <h2 class="text-xl font-semibold text-gray-800">
            Question {{ index + 1 }}
            <span class="text-sm font-normal ml-2">
              ({{ question.points }} point{{ question.points !== 1 ? 's' : '' }})
            </span>
          </h2>
          <span 
            class="px-3 py-1 rounded-full text-sm font-medium"
            :class="{
              'bg-green-100 text-green-800': isCorrect(question.id),
              'bg-red-100 text-red-800': !isCorrect(question.id)
            }"
          >
            {{ isCorrect(question.id) ? 'Correct' : 'Incorrect' }}
          </span>
        </div>

        <!-- Question Text -->
        <div class="mb-6 prose max-w-none" v-html="formatQuestionText(question)"></div>

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
          <FillInBlankResult 
            :question="question" 
            :response="getResponse(question.id)"
          />
        </div>

        <!-- Feedback and explanations -->
        <div v-if="question.explanation" class="mt-4 p-4 bg-blue-50 rounded-lg">
          <h3 class="font-medium text-blue-800 mb-2">Explanation</h3>
          <p class="text-blue-700">{{ question.explanation }}</p>
        </div>

        <div v-if="getFeedback(question.id)" class="mt-4 p-4 rounded-lg"
          :class="{
            'bg-green-50 text-green-700': isCorrect(question.id),
            'bg-red-50 text-red-700': !isCorrect(question.id)
          }"
        >
          <h3 class="font-medium mb-2">Feedback</h3>
          <p>{{ getFeedback(question.id) }}</p>
        </div>
      </div>
    </div>

    <div class="mt-8 flex justify-between">
      <button 
        @click="$router.go(-1)"
        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
      >
        Back to Quiz
      </button>
      <button 
        @click="downloadResults"
        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
      >
        Download Results
      </button>
    </div>
  </div>
</PublicLayout>
</template>

<script setup>
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import MultipleChoiceResult from '@/Components/ResultComponents/MultipleChoiceResult.vue';
import TrueFalseResult from '@/Components/ResultComponents/TrueFalseResult.vue';
import ShortAnswerResult from '@/Components/ResultComponents/ShortAnswerResult.vue';
import EssayResult from '@/Components/ResultComponents/EssayResult.vue';
import MatchingResult from '@/Components/ResultComponents/MatchingResult.vue';
import OrderingResult from '@/Components/ResultComponents/OrderingResult.vue';
import FillInBlankResult from '@/Components/ResultComponents/FillInBlankResult.vue';

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
  responses: {
    type: Array,
    required: true,
    default: () => []
  }
});

// Computed properties
const score = computed(() => props.attempt?.score || 0);
const totalPoints = computed(() => props.questions.reduce((sum, q) => sum + (q.points || 0), 0));
const percentage = computed(() => Math.round((score.value / (totalPoints.value || 1)) * 100));
const correctCount = computed(() => props.responses.filter(r => r?.is_correct).length);
const completionTime = computed(() => props.attempt?.completion_time || 0);

// Helper methods
const getResponse = (questionId) => {
  const response = props.responses.find(r => r?.question_id === questionId);
  return response?.answer;
};

const getFeedback = (questionId) => {
  const response = props.responses.find(r => r?.question_id === questionId);
  return response?.grading_data?.feedback || response?.feedback;
};

const isCorrect = (questionId) => {
  const response = props.responses.find(r => r?.question_id === questionId);
  return response?.is_correct || false;
};

const formatQuestionText = (question) => {
  if (!question?.question) return '';
  if (question.type === 'fill_in_blank') {
    return question.question.replace(/\[blank\]/gi, '<span class="blank-space">______</span>');
  }
  return question.question;
};

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}m ${secs}s`;
};

const downloadResults = () => {
  // Implementation for downloading results
  console.log('Downloading results...');
};
</script>

<style scoped>
.prose :deep(p) {
  margin-bottom: 1em;
  line-height: 1.6;
}

.prose :deep(.blank-space) {
  display: inline-block;
  min-width: 80px;
  border-bottom: 2px solid #666;
  margin: 0 4px;
  text-align: center;
}
</style>