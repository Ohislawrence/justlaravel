<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
    <div class="p-6">
      <h3 class="text-lg font-medium mb-4">Survey Analytics</h3>
      
      <!-- Question Type Distribution -->
      <div class="mb-8">
        <h4 class="font-medium mb-3 text-center">Question Types Distribution</h4>
        <div class="flex flex-col lg:flex-row items-center justify-between">
          <div class="lg:w-1/3 flex justify-center">
            <div class="relative w-48 h-48">
              <svg viewBox="0 0 100 100" class="w-full h-full">
                <path 
                  v-for="(slice, index) in questionTypeChart" 
                  :key="slice.type"
                  :d="getSlicePath(slice)"
                  :fill="slice.color"
                  class="transition-all duration-300 hover:opacity-80 cursor-pointer"
                  @mouseenter="hoveredSlice = slice.type"
                  @mouseleave="hoveredSlice = null"
                />
                <circle cx="50" cy="50" r="30" fill="white" />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <div class="text-lg font-bold">{{ totalQuestions }}</div>
                  <div class="text-xs text-gray-600">Total Questions</div>
                </div>
              </div>
            </div>
          </div>
          <div class="lg:w-2/3 mt-4 lg:mt-0">
            <div class="space-y-2">
              <div 
                v-for="slice in questionTypeChart" 
                :key="slice.type"
                class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': hoveredSlice === slice.type }"
              >
                <div class="flex items-center">
                  <div class="w-3 h-3 rounded-full mr-2" :style="{ backgroundColor: slice.color }"></div>
                  <span class="text-sm font-medium">{{ slice.type }}</span>
                </div>
                <div class="text-right">
                  <div class="text-sm font-bold">{{ slice.count }}</div>
                  <div class="text-xs text-gray-500">{{ slice.percentage }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Response Statistics -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Difficulty Distribution -->
        <div class="border rounded-lg p-4">
          <h4 class="font-medium mb-3 text-center">Question Difficulty</h4>
          <div class="h-48 flex items-center justify-center">
            <div class="w-full max-w-xs">
              <div v-for="level in difficultyLevels" :key="level.name" class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600">{{ level.name }}</span>
                <div class="flex items-center space-x-2">
                  <div class="w-32 bg-gray-200 rounded-full h-3">
                    <div 
                      class="h-3 rounded-full transition-all duration-500" 
                      :class="level.color"
                      :style="{ width: `${level.percentage}%` }"
                    ></div>
                  </div>
                  <span class="text-xs text-gray-600 w-8">{{ level.count }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Response Rate by Question -->
        <div class="border rounded-lg p-4">
          <h4 class="font-medium mb-3 text-center">Response Rates</h4>
          <div class="h-48 overflow-y-auto">
            <div v-for="question in responseRates" :key="question.id" class="flex items-center justify-between mb-3">
              <div class="flex-1 truncate mr-2">
                <div class="text-sm text-gray-700 truncate">{{ question.text }}</div>
                <div class="text-xs text-gray-500">{{ question.type }}</div>
              </div>
              <div class="flex items-center space-x-2 w-32">
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-blue-500 h-2 rounded-full" 
                    :style="{ width: `${question.responseRate}%` }"
                  ></div>
                </div>
                <span class="text-xs text-gray-600 w-8">{{ question.responseRate }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Time Statistics -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="text-center p-4 border rounded-lg">
          <div class="text-2xl font-bold text-green-600">{{ averageTimeSpent }}</div>
          <div class="text-sm text-gray-600">Avg. Time per Question</div>
        </div>
        <div class="text-center p-4 border rounded-lg">
          <div class="text-2xl font-bold text-blue-600">{{ totalSurveyTime }}</div>
          <div class="text-sm text-gray-600">Total Survey Time</div>
        </div>
        <div class="text-center p-4 border rounded-lg">
          <div class="text-2xl font-bold text-purple-600">{{ completionRate }}%</div>
          <div class="text-sm text-gray-600">Average Completion</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  quiz: Object,
  attempts: Object,
  questionStats: Array
});

const hoveredSlice = ref(null);

const totalQuestions = computed(() => {
  return props.questionStats?.length || 0;
});

const questionTypeChart = computed(() => {
  const questions = props.questionStats || [];
  const typeCounts = {};
  
  questions.forEach(question => {
    const type = question.type || 'unknown';
    typeCounts[type] = (typeCounts[type] || 0) + 1;
  });
  
  const colors = ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444', '#6B7280'];
  let total = questions.length;
  let accumulated = 0;
  
  return Object.entries(typeCounts).map(([type, count], index) => {
    const percentage = (count / total) * 100;
    const slice = {
      type: type.replace(/_/g, ' ').toUpperCase(),
      count,
      percentage: Math.round(percentage),
      startAngle: accumulated,
      endAngle: accumulated + percentage,
      color: colors[index % colors.length]
    };
    accumulated += percentage;
    return slice;
  });
});

const difficultyLevels = computed(() => {
  const questions = props.questionStats || [];
  const levels = {
    'Easy': { count: 0, color: 'bg-green-500' },
    'Medium': { count: 0, color: 'bg-yellow-500' },
    'Hard': { count: 0, color: 'bg-red-500' },
    'N/A': { count: 0, color: 'bg-gray-500' }
  };
  
  questions.forEach(question => {
    const difficulty = question.difficulty || 'N/A';
    if (levels[difficulty]) {
      levels[difficulty].count++;
    }
  });
  
  const total = questions.length;
  return Object.entries(levels).map(([name, data]) => ({
    name,
    count: data.count,
    percentage: total > 0 ? (data.count / total) * 100 : 0,
    color: data.color
  }));
});

const responseRates = computed(() => {
  const questions = props.questionStats || [];
  const totalAttempts = props.attempts?.data?.length || 0;
  
  return questions.map(question => {
    const responses = question.total_responses || 0;
    const responseRate = totalAttempts > 0 ? (responses / totalAttempts) * 100 : 0;
    
    return {
      id: question.id,
      text: question.question?.substring(0, 30) + (question.question?.length > 30 ? '...' : ''),
      type: question.type,
      responses,
      responseRate: Math.round(responseRate)
    };
  }).sort((a, b) => b.responseRate - a.responseRate);
});

const averageTimeSpent = computed(() => {
  const questions = props.questionStats || [];
  if (questions.length === 0) return '0s';
  
  const totalTime = questions.reduce((sum, question) => {
    return sum + (question.average_time || 0);
  }, 0);
  
  const avgSeconds = Math.round(totalTime / questions.length);
  return avgSeconds < 60 ? `${avgSeconds}s` : `${Math.round(avgSeconds / 60)}m`;
});

const totalSurveyTime = computed(() => {
  const attempts = props.attempts?.data || [];
  if (attempts.length === 0) return '0m';
  
  const totalSeconds = attempts.reduce((sum, attempt) => {
    return sum + (Number(attempt.time_spent) || 0);
  }, 0);
  
  const totalHours = totalSeconds / 3600;
  
  if (totalHours < 1) {
    // Less than 1 hour - show minutes
    const minutes = Math.round(totalSeconds / 60);
    return `${minutes}m`;
  } else {
    // 1 hour or more - show hours
    const hours = Math.round(totalHours * 10) / 10; // 1 decimal place
    return `${hours}h`;
  }
});

const completionRate = computed(() => {
  const questions = props.questionStats || [];
  if (questions.length === 0) return 0;
  
  const totalCompletion = questions.reduce((sum, question) => {
    const total = question.total_responses || 0;
    const attempts = props.attempts?.data?.length || 1;
    return sum + (total / attempts) * 100;
  }, 0);
  
  return Math.round(totalCompletion / questions.length);
});

const getSlicePath = (slice) => {
  const startAngle = (slice.startAngle / 100) * 360;
  const endAngle = (slice.endAngle / 100) * 360;
  
  const startRad = (startAngle - 90) * Math.PI / 180;
  const endRad = (endAngle - 90) * Math.PI / 180;
  
  const x1 = 50 + 35 * Math.cos(startRad);
  const y1 = 50 + 35 * Math.sin(startRad);
  const x2 = 50 + 35 * Math.cos(endRad);
  const y2 = 50 + 35 * Math.sin(endRad);
  
  const largeArc = endAngle - startAngle > 180 ? 1 : 0;
  
  return `M50,50 L${x1},${y1} A35,35 0 ${largeArc},1 ${x2},${y2} Z`;
};
</script>