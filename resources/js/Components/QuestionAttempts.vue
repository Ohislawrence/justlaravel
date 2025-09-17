<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
      <h3 class="text-lg font-medium mb-4">Question Review</h3>
      
      <!-- Questions Section -->
      <div v-if="responses && responses.length > 0" class="space-y-6">
        <div v-for="(response, index) in responses" :key="response.id" 
             class="border rounded-lg p-4 transition-all duration-200"
             :class="getQuestionBorderClass(response)">
          
          <!-- Question Header -->
          <div class="flex justify-between items-start mb-3">
            <div class="flex-1">
              <h4 class="font-medium">
                {{ index + 1 }}. <span v-html="response.question.question"></span>
              </h4>
              <div class="flex flex-wrap gap-2 text-sm text-gray-500 mt-1">
                <span class="bg-gray-100 px-2 py-1 rounded">
                  {{ questionTypeLabel(response.question.type) }}
                </span>
                <span class="bg-green-100 px-2 py-1 rounded">
                  {{ response.question.points }} points
                </span>
                <span :class="getCorrectnessClass(response)">
                  {{ response.is_correct ? '✓ Correct' : '✗ Incorrect' }}
                </span>
                <!-- Time Spent Flag -->
                <span v-if="response.time_spent > 0" class="bg-blue-100 text-blue-700 px-2 py-1 rounded">
                  ⏱️ {{ formatTimeSpent(response.time_spent) }}
                </span>
              </div>
            </div>
          </div>
          
          <!-- Question Content -->
          <div class="mt-4">
            <!-- Multiple Choice -->
            <div v-if="response.question.type === 'multiple_choice'" class="space-y-2">
              <p class="font-medium text-sm text-gray-700 mb-2">Options:</p>
              <div v-for="(option, optIndex) in response.question.options" :key="optIndex" 
                   class="p-2 rounded border flex items-center"
                   :class="getOptionClasses(response, option)">
                <span class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3 text-xs font-medium">
                  {{ String.fromCharCode(65 + optIndex) }}
                </span>
                <span v-html="option"></span>
                <span v-if="isCorrectAnswer(response.question, option)" class="ml-auto text-green-600 font-medium">
                  ✓ Correct
                </span>
              </div>
            </div>
            
            <!-- True/False -->
            <div v-else-if="response.question.type === 'true_false'" class="space-y-2">
              <p class="font-medium text-sm text-gray-700">Correct Answer:</p>
              <div class="p-2 bg-green-100 text-green-800 rounded border border-green-200">
                {{ response.question.correct_answers && response.question.correct_answers[0] === 'true' ? 'True' : 'False' }}
              </div>
              <p class="font-medium text-sm text-gray-700 mt-3">Your Answer:</p>
              <div class="p-2 rounded border" :class="getResponseClasses(response)">
                {{ response.answer === 'true' ? 'True' : 'False' }}
              </div>
            </div>
            
            <!-- Short Answer -->
            <div v-else-if="response.question.type === 'short_answer'" class="space-y-2">
              <p class="font-medium text-sm text-gray-700">Accepted Answers:</p>
              <div class="p-2 bg-green-100 text-green-800 rounded border border-green-200">
                <ul class="list-disc pl-5 space-y-1">
                  <li v-for="(answer, ansIndex) in response.question.correct_answers" :key="ansIndex">
                    {{ answer }}
                  </li>
                </ul>
              </div>
              <p class="font-medium text-sm text-gray-700 mt-3">Your Answer:</p>
              <div class="p-2 rounded border" :class="getResponseClasses(response)">
                {{ response.answer || 'No answer provided' }}
              </div>
            </div>
            
            <!-- Fill in the Blank -->
            <div v-else-if="response.question.type === 'fill_in_blank'" class="space-y-2">
              <p class="font-medium text-sm text-gray-700">Correct Text:</p>
              <div class="p-2 bg-green-100 text-green-800 rounded border border-green-200">
                <div class="whitespace-pre-line" v-html="formatFillInBlankText(response.question.question, response.question.correct_answers)"></div>
              </div>
              <p class="font-medium text-sm text-gray-700 mt-3">Your Answer:</p>
              <div class="p-2 rounded border" :class="getResponseClasses(response)">
                {{ Array.isArray(response.answer) ? response.answer.join(', ') : response.answer || 'No answer provided' }}
              </div>
            </div>
            
            <!-- Matching -->
            <div v-else-if="response.question.type === 'matching'" class="space-y-2">
              <p class="font-medium text-sm text-gray-700">Correct Matches:</p>
              <div class="p-2 bg-green-100 text-green-800 rounded border border-green-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                  <div v-for="(pair, index) in response.question.options" :key="index" class="flex items-center">
                    <span class="font-medium" v-html="pair.left"></span>
                    <span class="mx-2">→</span>
                    <span class="font-medium" v-html="pair.right"></span>
                  </div>
                </div>
              </div>
              <p class="font-medium text-sm text-gray-700 mt-3">Your Matches:</p>
              <div class="p-2 rounded border" :class="getResponseClasses(response)">
                <div v-if="response.answer" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                  <div v-for="(match, index) in parseAnswer(response.answer)" :key="index" class="flex items-center">
                    <span class="font-medium" v-html="match.left"></span>
                    <span class="mx-2">→</span>
                    <span class="font-medium" v-html="match.right || 'No match'"></span>
                  </div>
                </div>
                <div v-else>No answer provided</div>
              </div>
            </div>
            
            <!-- Ordering -->
            <div v-else-if="response.question.type === 'ordering'" class="space-y-2">
              <p class="font-medium text-sm text-gray-700">Correct Order:</p>
              <div class="p-2 bg-green-100 text-green-800 rounded border border-green-200">
                <ol class="list-decimal pl-5 space-y-1">
                  <li v-for="(item, idx) in response.question.correct_answers" :key="idx">
                    <span v-html="item"></span>
                  </li>
                </ol>
              </div>
              <p class="font-medium text-sm text-gray-700 mt-3">Your Order:</p>
              <div class="p-2 rounded border" :class="getResponseClasses(response)">
                <ol v-if="response.answer" class="list-decimal pl-5 space-y-1">
                  <li v-for="(item, idx) in parseAnswer(response.answer)" :key="idx">
                    <span v-html="item"></span>
                  </li>
                </ol>
                <div v-else>No answer provided</div>
              </div>
            </div>
            
            <!-- Essay -->
            <div v-else-if="response.question.type === 'essay'" class="space-y-2">
              <p class="font-medium text-sm text-gray-700">Your Answer:</p>
              <div class="p-2 rounded border bg-gray-50">
                <div v-if="response.answer" class="whitespace-pre-wrap">
                  {{ response.answer }}
                </div>
                <div v-else class="text-gray-500">No answer provided</div>
              </div>
              <div v-if="response.feedback" class="mt-3 p-3 bg-blue-50 rounded border border-blue-200">
                <p class="font-medium text-sm text-blue-700">Instructor Feedback:</p>
                <p class="mt-1 text-blue-800">{{ response.feedback }}</p>
              </div>
            </div>
          </div>
          
          <!-- Points Earned -->
          <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-600">
              Points earned: 
              <span :class="response.is_correct ? 'text-green-600 font-medium' : 'text-red-600'">
                {{ response.points_earned || 0 }} / {{ response.question.points }}
              </span>
            </div>
            <div v-if="response.feedback && response.question.type !== 'essay'" 
                 class="text-sm text-blue-600 cursor-pointer hover:underline"
                 @click="toggleFeedback(response.id)">
              {{ showFeedback[response.id] ? 'Hide Feedback' : 'View Feedback' }}
            </div>
          </div>
          
          <!-- Additional Feedback (for non-essay questions) -->
          <div v-if="showFeedback[response.id] && response.feedback && response.question.type !== 'essay'" 
               class="mt-3 p-3 bg-blue-50 rounded border border-blue-200">
            <p class="font-medium text-sm text-blue-700">Instructor Feedback:</p>
            <p class="mt-1 text-blue-800">{{ response.feedback }}</p>
          </div>

          <!-- Grading Data Explanation -->
          <div v-if="response.grading_data && typeof response.grading_data === 'string'" class="mt-3 p-3 bg-gray-50 rounded border border-gray-200">
            <p class="font-medium text-sm text-gray-700">Explanation:</p>
            <p class="mt-1 text-gray-800">{{ parseGradingData(response.grading_data)?.explanation }}</p>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-8 text-gray-500">
        <p>No questions available for review.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  responses: {
    type: Array,
    default: () => []
  }
});

const showFeedback = ref({});

const toggleFeedback = (responseId) => {
  showFeedback.value = {
    ...showFeedback.value,
    [responseId]: !showFeedback.value[responseId]
  };
};

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

const formatTimeSpent = (seconds) => {
  if (seconds < 60) {
    return `${seconds} sec`;
  } else {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    if (remainingSeconds === 0) {
      return `${minutes} min`;
    } else {
      return `${minutes} min ${remainingSeconds} sec`;
    }
  }
};

const formatFillInBlankText = (text, answers) => {
  if (!text || !answers) return text;
  const answersCopy = [...answers];
  return text.replace(/\[blank\]/g, () => {
    return `[${answersCopy.shift() || 'answer'}]`;
  });
};

const parseAnswer = (answer) => {
  if (typeof answer === 'string') {
    try {
      return JSON.parse(answer);
    } catch (e) {
      return answer;
    }
  }
  return answer;
};

const parseGradingData = (gradingData) => {
  if (typeof gradingData === 'string') {
    try {
      return JSON.parse(gradingData);
    } catch (e) {
      return { explanation: gradingData };
    }
  }
  return gradingData || {};
};

const isCorrectAnswer = (question, option) => {
  return question.correct_answers && question.correct_answers.includes(option);
};

const getQuestionBorderClass = (response) => {
  return response.is_correct 
    ? 'border-green-200 bg-green-50' 
    : 'border-red-200 bg-red-50';
};

const getCorrectnessClass = (response) => {
  return response.is_correct 
    ? 'bg-green-100 text-green-700 px-2 py-1 rounded' 
    : 'bg-red-100 text-red-700 px-2 py-1 rounded';
};

const getOptionClasses = (response, option) => {
  const userResponse = parseAnswer(response.answer);
  const isCorrect = isCorrectAnswer(response.question, option);
  const isSelected = Array.isArray(userResponse) 
    ? userResponse.includes(option) 
    : userResponse === option;
  
  if (isCorrect) {
    return 'bg-green-100 border-green-300 text-green-800';
  } else if (isSelected && !isCorrect) {
    return 'bg-red-100 border-red-300 text-red-800';
  }
  return 'bg-gray-50 border-gray-200';
};

const getResponseClasses = (response) => {
  return response.is_correct 
    ? 'bg-green-100 border-green-300 text-green-800' 
    : 'bg-red-100 border-red-300 text-red-800';
};
</script>

<style scoped>
/* Consistent styling with the Show.vue component */
.bg-green-100 {
  background-color: #ecfdf5;
}

.bg-red-100 {
  background-color: #fee2e2;
}

.bg-blue-50 {
  background-color: #eff6ff;
}

.bg-blue-100 {
  background-color: #dbeafe;
}

.bg-gray-50 {
  background-color: #f9fafb;
}

.border-green-200 {
  border-color: #a7f3d0;
}

.border-red-200 {
  border-color: #fecaca;
}

.border-blue-200 {
  border-color: #bfdbfe;
}

.border-gray-200 {
  border-color: #e5e7eb;
}

.text-green-800 {
  color: #065f46;
}

.text-red-800 {
  color: #991b1b;
}

.text-blue-700 {
  color: #1e40af;
}

.text-blue-800 {
  color: #1e3a8a;
}

.text-gray-700 {
  color: #374151;
}

.text-gray-800 {
  color: #1f2937;
}

/* Smooth transitions */
.transition-all {
  transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .flex {
    flex-direction: column;
  }
}
</style>