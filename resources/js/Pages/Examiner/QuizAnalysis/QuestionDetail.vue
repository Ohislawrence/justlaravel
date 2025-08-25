<template>
  <AppLayout :title="`Question Analysis - ${question.question}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Question Analysis: {{ quiz.title }}
        </h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('examiner.quizzes.analysis.index', { quiz: quiz.id })"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Quiz Analysis
          </Link>
          <Link 
            :href="route('examiner.quizzes.questions.edit', { quiz: quiz.id, question: question.id })"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Question
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Question Overview -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
              <div class="flex-1">
                <h3 class="text-lg font-medium mb-2">Question Content</h3>
                <div class="prose max-w-none">
                  <p class="whitespace-pre-wrap">{{ question.question }}</p>
                  <p v-if="question.description" class="text-gray-600 mt-2">
                    {{ question.description }}
                  </p>
                </div>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <p class="text-sm text-gray-500">Type</p>
                    <p class="font-medium">{{ questionTypeLabel(question.type) }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Points</p>
                    <p class="font-medium">{{ question.points }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Correct Answers</p>
                    <p class="font-medium">{{ question.correct_answers?.length || 0 }}</p>
                  </div>
                </div>
              </div>
              
              <div class="md:w-64">
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                  <h4 class="font-medium text-green-800 mb-2">Performance Summary</h4>
                  <div class="space-y-3">
                    <div>
                      <p class="text-sm text-green-700">Correct Responses</p>
                      <p class="text-xl font-bold">
                        {{ correctResponses }} / {{ totalResponses }}
                        ({{ correctPercentage }}%)
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-green-700">Average Points Earned</p>
                      <p class="text-xl font-bold">
                        {{ averagePoints.toFixed(1) }} / {{ question.points }}
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-green-700">Difficulty</p>
                      <p class="text-xl font-bold" :class="difficultyClass">
                        {{ difficultyLevel }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Response Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Correctness Chart -->
          <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <h3 class="text-lg font-medium mb-4">Response Distribution</h3>
            <DoughnutChart 
              :data="correctnessChartData"
              :options="chartOptions"
            />
          </div>

          <!-- Options Breakdown (for MCQ) -->
          <div v-if="question.type === 'multiple_choice'" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <h3 class="text-lg font-medium mb-4">Option Selection</h3>
            <BarChart 
              :data="optionsChartData"
              :options="chartOptions"
            />
          </div>

          <!-- Time Spent Analysis -->
          <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <h3 class="text-lg font-medium mb-4">Time Spent</h3>
            <div class="space-y-4">
              <div>
                <p class="text-sm text-gray-500">Average</p>
                <p class="font-medium">{{ formatTime(averageTime) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Fastest Correct</p>
                <p class="font-medium">{{ formatTime(fastestCorrect) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Slowest Correct</p>
                <p class="font-medium">{{ formatTime(slowestCorrect) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Student Responses -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium">Student Responses</h3>
              <div class="flex space-x-2">
                <select
                  v-model="responseFilter"
                  class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md transition-colors duration-200"
                >
                  <option value="all">All Responses</option>
                  <option value="correct">Correct Only</option>
                  <option value="incorrect">Incorrect Only</option>
                </select>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Student
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Response
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Points
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Time Spent
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="response in filteredResponses" :key="response.id" class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img class="h-10 w-10 rounded-full" :src="response.attempt.user.avatar_url || '/images/default-avatar.png'" alt="">
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ response.attempt.user.name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            {{ response.attempt.user.email }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div v-if="question.type === 'multiple_choice'">
                        {{ getOptionText(response.answer) }}
                      </div>
                      <div v-else-if="question.type === 'fill_in_blank'">
                        <span v-for="(answer, index) in (Array.isArray(response.answer) ? response.answer : [response.answer])" 
                              :key="index" class="mr-2">
                          {{ answer }}
                        </span>
                      </div>
                      <div v-else>
                        {{ response.answer }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ response.points_earned }} / {{ question.points }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatTime(response.time_spent) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                            :class="response.is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ response.is_correct ? 'Correct' : 'Incorrect' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <Pagination v-if="responses.last_page > 1" :links="responses.links" class="mt-4" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';

const props = defineProps({
  quiz: {
    type: Object,
    required: true
  },
  question: {
    type: Object,
    required: true
  },
  responses: {
    type: Object,
    required: true
  }
});

const responseFilter = ref('all');

// Performance metrics
const totalResponses = computed(() => props.responses.total);
const correctResponses = computed(() => props.responses.data.filter(r => r.is_correct).length);
const correctPercentage = computed(() => Math.round((correctResponses.value / totalResponses.value) * 100));
const averagePoints = computed(() => {
  if (totalResponses.value === 0) return 0;
  return props.responses.data.reduce((sum, r) => sum + r.points_earned, 0) / totalResponses.value;
});

// Difficulty analysis
const difficultyLevel = computed(() => {
  if (correctPercentage.value >= 80) return 'Easy';
  if (correctPercentage.value >= 50) return 'Medium';
  return 'Hard';
});

const difficultyClass = computed(() => {
  switch(difficultyLevel.value) {
    case 'Easy': return 'text-green-600';
    case 'Medium': return 'text-yellow-600';
    case 'Hard': return 'text-red-600';
    default: return '';
  }
});

// Time analysis
const averageTime = computed(() => {
  if (totalResponses.value === 0) return 0;
  return props.responses.data.reduce((sum, r) => sum + (r.time_spent || 0), 0) / totalResponses.value;
});

const fastestCorrect = computed(() => {
  const correct = props.responses.data.filter(r => r.is_correct);
  return correct.length ? Math.min(...correct.map(r => r.time_spent || 0)) : 0;
});

const slowestCorrect = computed(() => {
  const correct = props.responses.data.filter(r => r.is_correct);
  return correct.length ? Math.max(...correct.map(r => r.time_spent || 0)) : 0;
});

// Chart data
const correctnessChartData = computed(() => ({
  labels: ['Correct', 'Incorrect'],
  datasets: [{
    data: [correctResponses.value, totalResponses.value - correctResponses.value],
    backgroundColor: ['#10B981', '#EF4444'],
    hoverOffset: 4
  }]
}));

const optionsChartData = computed(() => {
  if (question.type !== 'multiple_choice') return null;
  
  const optionCounts = {};
  question.options.forEach((option, index) => {
    optionCounts[index] = 0;
  });

  props.responses.data.forEach(response => {
    const answerIndex = question.options.findIndex(opt => 
      opt === response.answer || opt.text === response.answer
    );
    if (answerIndex >= 0) {
      optionCounts[answerIndex]++;
    }
  });

  return {
    labels: question.options.map(opt => opt.text || opt),
    datasets: [{
      label: 'Times Selected',
      data: Object.values(optionCounts),
      backgroundColor: question.options.map((opt, index) => 
        question.correct_answers.includes(index) ? '#10B981' : '#3B82F6'
      )
    }]
  };
});

// Filter responses
const filteredResponses = computed(() => {
  if (responseFilter.value === 'all') return props.responses.data;
  return props.responses.data.filter(r => 
    responseFilter.value === 'correct' ? r.is_correct : !r.is_correct
  );
});

// Helper functions
const formatTime = (seconds) => {
  if (!seconds) return 'N/A';
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}m ${secs}s`;
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

const getOptionText = (option) => {
  if (typeof option === 'string') return option;
  return option.text || option.value || JSON.stringify(option);
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `${context.dataset.label}: ${context.parsed}`;
        }
      }
    }
  }
};
</script>

<style scoped>
/* Enhanced green-themed styling */
.bg-green-600 {
  background-color: #10B981;
}

.bg-green-700:hover {
  background-color: #059669;
}

.focus\:ring-green-300:focus {
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Green-themed performance summary */
.bg-green-50 {
  background-color: #ecfdf5;
  border-color: #d1fae5;
}

.text-green-800 {
  color: #065f46;
}

.text-green-700 {
  color: #047857;
}

/* Hover effects */
.hover\:shadow-md:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Border styling */
.border-gray-100 {
  border-color: #f3f4f6;
}

/* Table hover effect */
.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}

/* Select focus state */
.focus\:ring-green-500:focus {
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .flex {
      flex-direction: column;
  }
  
  .space-x-2 > *:not(:last-child) {
      margin-bottom: 0.5rem;
  }
}
</style>