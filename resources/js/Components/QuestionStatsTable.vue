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
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ questionTypeLabel(question.type) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ question.correct_responses }} / {{ question.total_responses }}
              ({{ Math.round((question.correct_responses / question.total_responses) * 100) }}%)
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ question.average_points.toFixed(1) }} / {{ question.points }}
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
    stats: Array,
    quiz: Object
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
  </script>