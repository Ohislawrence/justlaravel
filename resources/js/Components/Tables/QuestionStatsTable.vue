<template>
  <div class="overflow-x-auto">
    <div v-if="!stats || stats.length === 0" class="text-center py-8 text-gray-500">
      No question statistics available
    </div>
    
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
            Source
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Correct
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Avg Points
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Difficulty
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(question, index) in stats" :key="index">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ question.question }}
              <span v-if="question.is_ai === 1" class="ml-2 text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full">
                AI-Generated
              </span>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ questionTypeLabel(question.type) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <template v-if="question.source.type  === 'direct'">
              Direct Question
            </template>
            <template v-else-if="question.source.type === 'pool'">
              From Pool: 
              <Link v-if="quiz" 
                :href="route('examiner.question-pools.show', { question_pool: question.source.pool_id })"
                class="text-blue-600 hover:text-blue-800"
              >
                {{ question.source.pool_name }}
              </Link>
              <span v-else>{{ question.source.pool_name }}</span>
            </template>
            <template v-else>
              Unknown Source
            </template>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ question.correct_responses }} / {{ question.total_responses }}
            ({{ calculatePercentage(question.correct_responses, question.total_responses) }}%)
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ formatAveragePoints(question.average_points, question.points) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                  :class="getDifficultyClass(question.difficulty)">
              {{ question.difficulty }}
            </span>
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
    required: true,
    default: () => [],
    validator: (value) => {
      return Array.isArray(value) && 
        value.every(item => typeof item === 'object' && item !== null)
    }
  },
  quiz: {
    type: Object,
    default: null
  },
  analysis: Object,
  
});

const questionTypeLabel = (type) => {
  const types = {
    'multiple_choice': 'MCQ',
    'true_false': 'True/False',
    'short_answer': 'Short Answer',
    'essay': 'Essay',
    'fill_in_blank': 'Fill Blank',
    'matching': 'Matching',
    'ordering': 'Ordering'
  };
  return types[type] || type;
};

const getDifficultyClass = (difficulty) => {
  switch(difficulty) {
    case 'Easy': return 'bg-green-100 text-green-800';
    case 'Medium': return 'bg-yellow-100 text-yellow-800';
    case 'Hard': return 'bg-red-100 text-red-800';
    default: return 'bg-gray-100 text-gray-800';
  }
};

const calculatePercentage = (correct, total) => {
  if (!total || total === 0) return 0;
  return Math.round((correct / total) * 100);
};

const formatAveragePoints = (average, total) => {
  if (average === undefined || average === null) return 'N/A';
  return `${average.toFixed(1)} / ${total || 'N/A'}`;
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>