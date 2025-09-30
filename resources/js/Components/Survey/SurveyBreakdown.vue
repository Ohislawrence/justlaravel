<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
    <div class="p-6">
      <h3 class="text-lg font-medium mb-4">Survey Response Breakdown</h3>
      <div class="space-y-6">
        <div v-for="question in questionStats" :key="question.id" class="border-b pb-4 last:border-b-0">
          <!-- Question Header -->
          <div class="flex justify-between items-start mb-3">
            <div>
              <h4 class="font-medium text-gray-900">{{ question.question || 'Question' }}</h4>
              <div class="flex items-center space-x-2 mt-1">
                <span class="text-xs px-2 py-1 bg-gray-100 rounded-full">{{ questionTypeLabel(question.type) }}</span>
                <span class="text-xs text-gray-500">{{ question.total_responses || 0 }} responses</span>
                <span v-if="question.points" class="text-xs text-gray-500">{{ question.points }} points</span>
              </div>
            </div>
            <div class="text-right">
              <span class="text-sm font-medium" :class="getDifficultyColor(question.difficulty)">
                {{ question.difficulty || 'N/A' }}
              </span>
              <div class="text-xs text-gray-500">Difficulty</div>
            </div>
          </div>
          
        <!-- Multiple Choice Questions -->
        <div v-if="question.type === 'multiple_choice'" class="space-y-3">
        
        <div class="space-y-2">
            <div v-for="(option, index) in getQuestionOptions(question)" :key="index" class="flex items-center justify-between">
            <div class="flex items-center flex-1">
                <span class="text-sm text-gray-700">{{ option.option }}</span>
                <span v-if="option.is_correct" class="ml-2 text-xs text-green-600 font-medium">{{ option.percentage }}</span>
            </div>
            <div class="flex items-center space-x-2 w-48">
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                <div 
                    class="h-2 rounded-full transition-all duration-500" 
                    :class="option.is_correct ? 'bg-green-500' : 'bg-blue-500'"
                    :style="{ width: `${getOptionPercentage(question, option)}%` }"
                ></div>
                </div>
                <span class="text-xs text-gray-600 w-12">{{ getOptionPercentage(question, option) }}%</span>
                <span class="text-xs text-gray-500 w-8 text-right">({{ option.count }})</span>
            </div>
            </div>
        </div>
        <div class="pt-2 border-t border-gray-100">
            <div class="flex justify-between text-xs text-gray-500">
            <span>Correct answers are highlighted in green</span>
            <span>{{ getCorrectPercentage(question) }}% answered correctly</span>
            </div>
        </div>
        </div>

        <!-- True/False Questions -->
        <div v-else-if="question.type === 'true_false'" class="space-y-3">
        
        
        <!-- Show message if no options data -->
        <div v-if="!getQuestionOptions(question).length" class="bg-yellow-50 p-3 rounded border border-yellow-200">
            <p class="text-sm text-yellow-700">No response data available for this True/False question</p>
        </div>
        
        <div v-else class="space-y-2">
            <div v-for="(option, index) in getQuestionOptions(question)" :key="index" class="flex items-center justify-between">
            <div class="flex items-center flex-1">
                <span class="text-sm text-gray-700 font-medium">{{ option.option }}</span>
                <span v-if="option.is_correct" class="ml-2 text-xs text-green-600 font-medium">✓ Correct Answer</span>
            </div>
            <div class="flex items-center space-x-2 w-48">
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                <div 
                    class="h-2 rounded-full transition-all duration-500" 
                    :class="option.is_correct ? 'bg-green-500' : 'bg-red-400'"
                    :style="{ width: `${getOptionPercentage(question, option)}%` }"
                ></div>
                </div>
                <span class="text-xs text-gray-600 w-12">{{ getOptionPercentage(question, option) }}%</span>
                <span class="text-xs text-gray-500 w-8 text-right">({{ option.count }})</span>
            </div>
            </div>
        </div>
        
        <div v-if="getQuestionOptions(question).length" class="pt-2 border-t border-gray-100">
            <div class="flex justify-between text-xs text-gray-500">
            <span>Correct answer is highlighted in green</span>
            <span>{{ getCorrectPercentage(question) }}% answered correctly</span>
            </div>
        </div>
        </div>
          
          <!-- Fill in the Blank Responses -->
          <div v-else-if="question.type === 'fill_in_blank'" class="space-y-3">
            <div class="text-sm text-gray-600 mb-2">
              {{ question.total_responses || 0 }} responses collected
            </div>
            <div class="bg-gray-50 p-3 rounded">
              <p class="text-sm font-medium text-gray-700 mb-2">Response Summary:</p>
              <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                  <span>Average time spent:</span>
                  <span class="font-medium">{{ Math.round(question.average_time || 0) }} seconds</span>
                </div>
                <div class="flex justify-between">
                  <span>Average points earned:</span>
                  <span class="font-medium">{{ Math.round(question.average_points || 0) }} / {{ question.points }}</span>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-3">
                For detailed text responses, view individual attempts
              </p>
            </div>
          </div>
          
          <!-- Short Answer Responses -->
          <div v-else-if="question.type === 'short_answer'" class="space-y-3">
            <div class="text-sm text-gray-600 mb-2">
              {{ question.total_responses || 0 }} responses collected
            </div>
            <div class="bg-gray-50 p-3 rounded">
              <p class="text-sm font-medium text-gray-700 mb-2">Response Summary:</p>
              <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                  <span>Average time spent:</span>
                  <span class="font-medium">{{ Math.round(question.average_time || 0) }} seconds</span>
                </div>
                <div class="flex justify-between">
                  <span>Average points earned:</span>
                  <span class="font-medium">{{ Math.round(question.average_points || 0) }} / {{ question.points }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Correct responses:</span>
                  <span class="font-medium">{{ question.correct_responses || 0 }} ({{ getCorrectPercentage(question) }}%)</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Essay Responses -->
          <div v-else-if="question.type === 'essay'" class="space-y-3">
            <div class="text-sm text-gray-600 mb-2">
              {{ question.total_responses || 0 }} responses collected
            </div>
            <div class="bg-gray-50 p-3 rounded">
              <p class="text-sm font-medium text-gray-700 mb-2">Response Summary:</p>
              <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                  <span>Average time spent:</span>
                  <span class="font-medium">{{ Math.round(question.average_time || 0) }} seconds</span>
                </div>
                <div class="flex justify-between">
                  <span>Average points earned:</span>
                  <span class="font-medium">{{ Math.round(question.average_points || 0) }} / {{ question.points }}</span>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-3">
                For detailed essay responses, view individual attempts
              </p>
            </div>
          </div>
          
          <!-- Matching Questions -->
          <div v-else-if="question.type === 'matching'" class="space-y-3">
            <div class="text-sm text-gray-600">
              {{ question.total_responses || 0 }} matching attempts
            </div>
            <div class="bg-gray-50 p-3 rounded">
              <p class="text-sm font-medium text-gray-700 mb-2">Match Distribution:</p>
              <div v-if="question.options_stats && question.options_stats.length > 0" class="space-y-2">
                <div v-for="(option, index) in question.options_stats" :key="index" 
                     class="flex items-center justify-between text-xs">
                  <span class="flex-1">{{ option.option }}</span>
                  <div class="flex items-center space-x-2 w-32">
                    <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                      <div 
                        class="h-1.5 rounded-full" 
                        :class="option.is_correct ? 'bg-green-500' : 'bg-blue-500'"
                        :style="{ width: `${getOptionPercentage(question, option)}%` }"
                      ></div>
                    </div>
                    <span class="text-gray-600 w-8">{{ getOptionPercentage(question, option) }}%</span>
                    <span class="text-gray-500 w-8 text-right">({{ option.count }})</span>
                  </div>
                </div>
              </div>
              <div v-else class="text-xs text-gray-500">
                No matching data available
              </div>
            </div>
          </div>
          
          <!-- Ordering Questions -->
          <div v-else-if="question.type === 'ordering'" class="space-y-3">
            <div class="text-sm text-gray-600">
              {{ question.total_responses || 0 }} ordering attempts
            </div>
            <div class="bg-gray-50 p-3 rounded">
              <p class="text-sm font-medium text-gray-700 mb-2">Ordering Statistics:</p>
              <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                  <span>Correct orders:</span>
                  <span class="font-medium">{{ question.correct_responses || 0 }} ({{ getCorrectPercentage(question) }}%)</span>
                </div>
                <div class="flex justify-between">
                  <span>Average time spent:</span>
                  <span class="font-medium">{{ Math.round(question.average_time || 0) }} seconds</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Default for other question types -->
          <div v-else class="bg-yellow-50 p-3 rounded">
            <div class="text-sm text-yellow-700">
              Response analysis not available for {{ questionTypeLabel(question.type) }} questions
            </div>
          </div>
          
          <!-- Question Statistics -->
          <div class="grid grid-cols-3 gap-2 mt-3 text-xs">
            <div class="text-center p-2 bg-blue-50 rounded">
              <div class="font-medium text-blue-700">{{ question.total_responses || 0 }}</div>
              <div class="text-blue-600">Responses</div>
            </div>
            <div class="text-center p-2 bg-green-50 rounded">
              <div class="font-medium text-green-700">{{ question.correct_responses || 0 }}</div>
              <div class="text-green-600">Correct</div>
            </div>
            <div class="text-center p-2 bg-purple-50 rounded">
              <div class="font-medium text-purple-700">{{ Math.round(question.average_time || 0) }}s</div>
              <div class="text-purple-600">Avg Time</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  quiz: Object,
  attempts: Object,
  questionStats: Array
});

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

const getDifficultyColor = (difficulty) => {
  const colors = {
    'Easy': 'text-green-600',
    'Medium': 'text-yellow-600',
    'Hard': 'text-red-600',
    'N/A': 'text-gray-600'
  };
  return colors[difficulty] || 'text-gray-600';
};

const getQuestionOptions = (question) => {
  // Use the actual options_stats from the service
  if (question.options_stats && Array.isArray(question.options_stats)) {
    return question.options_stats;
  }
  
  // For true/false questions without data, try to create from question data
  if (question.type === 'true_false') {
    // Try to get correct answer from question data
    const correctAnswer = question.correct_answers?.[0] ?? 
                         question.correct_answer ?? 
                         (question.correct_answers ? question.correct_answers[0] : null);
    
    console.log('True/False question data:', { 
      question, 
      correctAnswer,
      options_stats: question.options_stats 
    });
    
    return [
      { 
        option: 'True', 
        value: 'true', 
        count: 0, 
        is_correct: correctAnswer === 'true' || correctAnswer === true || correctAnswer === 1 
      },
      { 
        option: 'False', 
        value: 'false', 
        count: 0, 
        is_correct: correctAnswer === 'false' || correctAnswer === false || correctAnswer === 0 
      }
    ];
  }
  
  // Fallback
  return [];
};

const getOptionPercentage = (question, option) => {
  const total = question.total_responses || 0;
  if (total === 0) return 0;
  
  const count = option.count || 0;
  return Math.round((count / total) * 100);
};

const getCorrectPercentage = (question) => {
  const total = question.total_responses || 0;
  const correct = question.correct_responses || 0;
  return total > 0 ? Math.round((correct / total) * 100) : 0;
};
</script>