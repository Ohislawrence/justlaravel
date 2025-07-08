<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Question
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Type
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Correct
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Total
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            % Correct
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Actions
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="stat in stats" :key="stat.id">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ stat.question || 'Unknown Question' }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                  :class="questionTypeClasses(stat.type)">
              {{ stat.type || 'Unknown' }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ stat.correct_count || 0 }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ stat.total_responses || 0 }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <div class="flex items-center">
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" 
                     :style="`width: ${stat.correct_percentage || 0}%`"></div>
              </div>
              <span class="ml-2">{{ Math.round(stat.correct_percentage || 0) }}%</span>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <component
              :is="getQuestionAnalysisUrl(stat) ? Link : 'span'"
              :href="getQuestionAnalysisUrl(stat)"
              :class="getQuestionAnalysisUrl(stat) ? 'text-blue-600 hover:text-blue-900' : 'text-gray-400'"
            >
              {{ getQuestionAnalysisUrl(stat) ? 'Details' : 'N/A' }}
            </component>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  stats: {
    type: Array,
    default: () => []
  },
  quiz: {
    type: Object,
    required: true
  }
});

const questionTypeClasses = (type) => {
  const classes = {
    'multiple_choice': 'bg-purple-100 text-purple-800',
    'true_false': 'bg-green-100 text-green-800',
    'short_answer': 'bg-yellow-100 text-yellow-800',
    'fill_in_blank': 'bg-blue-100 text-blue-800',
    'matching': 'bg-indigo-100 text-indigo-800',
    'essay': 'bg-pink-100 text-pink-800'
  };
  return classes[type] || 'bg-gray-100 text-gray-800';
};

const getQuestionAnalysisUrl = (stat) => {
  try {
    // Check if route function exists and required parameters are available
    if (typeof route === 'undefined' || !props.quiz?.id || !stat?.id) {
      return null;
    }
    
    return route('examiner.quizzes.analysis.question', { 
      quiz: props.quiz.id, 
      questionId: stat.id 
    });
  } catch (error) {
    console.error('Route generation failed:', error);
    return null;
  }
};
</script>