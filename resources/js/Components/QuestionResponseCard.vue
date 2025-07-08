<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
      <!-- Question Header -->
      <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-800">
            Question {{ questionNumber }}: {{ question.question }}
          </h3>
          <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                :class="response.is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
            {{ response.is_correct ? 'Correct' : 'Incorrect' }}
          </span>
        </div>
        <p v-if="question.description" class="text-sm text-gray-600 mt-1">
          {{ question.description }}
        </p>
      </div>
  
      <!-- Response Content -->
      <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Student Response -->
          <div class="col-span-2">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Student Response</h4>
            <div class="bg-gray-50 p-3 rounded-md">
              <template v-if="question.type === 'multiple_choice'">
                <p class="font-medium">{{ getOptionText(response.answer) }}</p>
                <p v-if="!response.is_correct" class="text-sm text-gray-600 mt-1">
                  Correct answer: {{ getCorrectAnswersText() }}
                </p>
              </template>
              <template v-else-if="question.type === 'fill_in_blank'">
                <div v-for="(answer, index) in (Array.isArray(response.answer) ? response.answer : [response.answer])"  
                     :key="index" class="mb-1">
                  <span class="inline-block px-2 py-1 bg-blue-50 text-blue-800 rounded text-sm">
                    {{ answer }}
                  </span>
                </div>
              </template>
              <template v-else>
                <p class="whitespace-pre-wrap">{{ response.answer }}</p>
              </template>
            </div>
          </div>
  
          <!-- Performance Metrics -->
          <div>
            <h4 class="text-sm font-medium text-gray-500 mb-2">Performance</h4>
            <div class="space-y-3">
              <div>
                <p class="text-xs text-gray-500">Points Earned</p>
                <p class="font-medium">
                  {{ response.points_earned }} / {{ question.points }}
                </p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Time Spent</p>
                <p class="font-medium">{{ formatTime(response.time_spent) }}</p>
              </div>
              <div v-if="response.feedback">
                <p class="text-xs text-gray-500">Feedback</p>
                <p class="text-sm">{{ response.feedback }}</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Additional Details -->
        <div v-if="showDetails" class="mt-4 pt-4 border-t border-gray-200">
          <h4 class="text-sm font-medium text-gray-500 mb-2">Details</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-gray-500">Question Type</p>
              <p class="text-sm">{{ questionTypeLabel(question.type) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500">Answered At</p>
              <p class="text-sm">{{ formatDateTime(response.created_at) }}</p>
            </div>
            <div v-if="response.grading_data">
              <p class="text-xs text-gray-500">Grading Data</p>
              <pre class="text-xs bg-gray-50 p-2 rounded">{{ JSON.stringify(response.grading_data, null, 2) }}</pre>
            </div>
          </div>
        </div>
  
        <!-- Toggle Button -->
        <button @click="showDetails = !showDetails" 
                class="mt-3 text-sm text-blue-600 hover:text-blue-800 focus:outline-none">
          {{ showDetails ? 'Hide Details' : 'Show Details' }}
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const props = defineProps({
  question: {
    type: Object,
    required: true,
    default: () => ({
      question: '',
      description: '',
      type: '',
      points: 0,
      options: [],
      correct_answers: []
    })
  },
  response: {
    type: Object,
    required: true,
    default: () => ({
      answer: '',
      is_correct: false,
      points_earned: 0,
      time_spent: 0,
      feedback: '',
      grading_data: null,
      created_at: ''
    })
  },
  questionNumber: {
    type: Number,
    required: true
  }
});

const showDetails = ref(false);
  
  const questionTypeLabel = (type) => {
    const types = {
      'multiple_choice': 'Multiple Choice',
      'true_false': 'True/False',
      'short_answer': 'Short Answer',
      'essay': 'Essay',
      'fill_in_blank': 'Fill in Blank',
      'matching': 'Matching',
      'ordering': 'Ordering'
    };
    return types[type] || type;
  };
  
  const formatTime = (seconds) => {
    if (!seconds) return 'N/A';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}m ${secs}s`;
  };
  
  const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
  };
  
  const getOptionText = (option) => {
  if (!option) return 'No answer provided';
  if (typeof option === 'string') return option;
  if (typeof option === 'object') {
    return option.text || option.value || JSON.stringify(option);
  }
  return String(option);
};
  
const getCorrectAnswersText = () => {
  try {
    if (!props.question?.options || !props.question?.correct_answers) {
      return 'Correct answer not available';
    }
    
    return props.question.correct_answers
      .map(index => {
        const option = props.question.options[index];
        return option ? getOptionText(option) : `Option ${index}`;
      })
      .filter(Boolean)
      .join(', ');
  } catch (e) {
    console.error('Error getting correct answers:', e);
    return 'Correct answer not available';
  }
};
  </script>
  
  <style scoped>
  pre {
    white-space: pre-wrap;
    word-wrap: break-word;
  }
  </style>