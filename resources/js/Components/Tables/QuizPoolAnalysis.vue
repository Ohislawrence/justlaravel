<template>
  <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Quiz Pool Performance Analysis</h3>
          <p class="text-sm text-gray-500 mt-1">Performance breakdown by question pool</p>
      </div>
      
      <div class="p-6">
          <!-- Summary Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <div class="bg-blue-50 p-4 rounded-lg">
                  <p class="text-sm font-medium text-blue-800">Total Questions</p>
                  <p class="text-2xl font-bold text-blue-900">{{ totalQuestions }}</p>
              </div>
              <div class="bg-green-50 p-4 rounded-lg">
                  <p class="text-sm font-medium text-green-800">Correct Answers</p>
                  <p class="text-2xl font-bold text-green-900">{{ totalCorrect }}</p>
              </div>
              <div class="bg-purple-50 p-4 rounded-lg">
                  <p class="text-sm font-medium text-purple-800">Overall Score</p>
                  <p class="text-2xl font-bold text-purple-900">{{ overallPercentage }}%</p>
              </div>
          </div>

          

          <!-- Pool Performance Table -->
          <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                      <tr>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Question Pool
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Questions
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Correct
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Score
                          </th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Performance
                          </th>
                      </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                      <!-- Pool Rows -->
                      <tr v-for="pool in poolPerformance" :key="pool.id">
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                              {{ pool.name }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ pool.questionCount }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ pool.correctCount }} / {{ pool.questionCount }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ pool.score }} / {{ pool.maxScore }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                  <div class="w-full bg-gray-200 rounded-full h-2.5">
                                      <div 
                                          class="h-2.5 rounded-full" 
                                          :class="getPerformanceColor(pool.percentage)"
                                          :style="`width: ${Math.min(pool.percentage, 100)}%`"
                                      ></div>
                                  </div>
                                  <span class="ml-2 text-xs font-medium text-gray-700">
                                      {{ pool.percentage.toFixed(1) }}%
                                  </span>
                              </div>
                          </td>
                      </tr>
                      
                      <!-- Others Row -->
                      <tr v-if="othersPerformance.questionCount > 0">
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                              Others
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ othersPerformance.questionCount }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ othersPerformance.correctCount }} / {{ othersPerformance.questionCount }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              {{ othersPerformance.score }} / {{ othersPerformance.maxScore }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                  <div class="w-full bg-gray-200 rounded-full h-2.5">
                                      <div 
                                          class="h-2.5 rounded-full" 
                                          :class="getPerformanceColor(othersPerformance.percentage)"
                                          :style="`width: ${Math.min(othersPerformance.percentage, 100)}%`"
                                      ></div>
                                  </div>
                                  <span class="ml-2 text-xs font-medium text-gray-700">
                                      {{ othersPerformance.percentage.toFixed(1) }}%
                                  </span>
                              </div>
                          </td>
                      </tr>
                  </tbody>
              </table>
          </div>
      </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  quiz: {
      type: Object,
      required: true
  },
  questionpools: {
      type: Object,
      default: () => ({})
  },
  attempts: {
      type: Object,
      required: true
  },
  userId: {
      type: Number,
      required: true
  }
});

// Reactive data
const poolPerformance = ref([]);
const othersPerformance = ref({
  name: 'Others',
  questionCount: 0,
  correctCount: 0,
  score: 0,
  maxScore: 0,
  percentage: 0
});

// For debugging
const debugInfo = ref(true);
const poolQuestionsMap = ref({});
const otherQuestions = ref([]);
const responses = ref([]);
const samplePoolData = ref('');

// Computed properties
const totalQuestions = computed(() => {
  return poolPerformance.value.reduce((sum, pool) => sum + pool.questionCount, 0) + 
         othersPerformance.value.questionCount;
});

const totalCorrect = computed(() => {
  return poolPerformance.value.reduce((sum, pool) => sum + pool.correctCount, 0) + 
         othersPerformance.value.correctCount;
});

const overallPercentage = computed(() => {
  const totalScore = poolPerformance.value.reduce((sum, pool) => sum + pool.score, 0) + 
                    othersPerformance.value.score;
  const totalMaxScore = poolPerformance.value.reduce((sum, pool) => sum + pool.maxScore, 0) + 
                       othersPerformance.value.maxScore;
  
  return totalMaxScore > 0 ? ((totalScore / totalMaxScore) * 100).toFixed(1) : 0;
});

// Methods
const getPerformanceColor = (percentage) => {
  if (percentage >= 80) return 'bg-green-600';
  if (percentage >= 60) return 'bg-yellow-500';
  return 'bg-red-600';
};

const getPoolName = (poolId) => {
  // Find pool in questionpools
  const pools = Array.isArray(props.questionpools) 
    ? props.questionpools 
    : (props.questionpools.data || []);
    
  const pool = pools.find(p => p.id == poolId);
  return pool ? pool.name : `Unknown (${poolId})`;
};

const analyzePoolPerformance = () => {
  // Reset performance data
  poolPerformance.value = [];
  othersPerformance.value = {
      name: 'Others',
      questionCount: 0,
      correctCount: 0,
      score: 0,
      maxScore: 0,
      percentage: 0
  };
  
  poolQuestionsMap.value = {};
  otherQuestions.value = [];
  responses.value = props.attempts.responses || [];

  // First, build a map of all pools and their questions
  const poolMap = {};
  
  // Get pools from questionpools prop
  const pools = Array.isArray(props.questionpools) 
    ? props.questionpools 
    : (props.questionpools.data || []);
  
  // Store sample data for debugging
  if (pools.length > 0) {
    samplePoolData.value = JSON.stringify({
      id: pools[0].id,
      name: pools[0].name,
      hasQuestions: !!pools[0].questions,
      questionsCount: pools[0].questions ? pools[0].questions.length : 0,
      questionsSample: pools[0].questions ? pools[0].questions.slice(0, 2) : null
    });
  }
  
  // Build a map of pool IDs to question IDs using the eager-loaded questions
  pools.forEach(pool => {
    const questionIds = [];
    
    // Use the eager-loaded questions relationship
    if (pool.questions && Array.isArray(pool.questions)) {
      pool.questions.forEach(question => {
        if (question.id) {
          questionIds.push(question.id);
        }
      });
    }
    
    poolMap[pool.id] = {
        ...pool,
        responses: [],
        maxScore: 0,
        questionIds: questionIds
    };
    
    // Initialize the debug map
    poolQuestionsMap.value[pool.id] = questionIds;
  });

  // Get all responses from the attempt
  responses.value.forEach(response => {
      const question = response.question;
      if (!question || !question.id) return;
      
      // Check if this question belongs to any pool using our poolMap
      let foundInPool = false;
      
      // Check each pool to see if it contains this question
      for (const poolId in poolMap) {
          if (poolMap[poolId].questionIds.includes(question.id)) {
              poolMap[poolId].responses.push(response);
              poolMap[poolId].maxScore += question.points || 1;
              foundInPool = true;
              break; // Question can only be in one pool for this analysis
          }
      }
      
      // If not found in any pool, add to others
      if (!foundInPool) {
          otherQuestions.value.push(question.id);
          othersPerformance.value.maxScore += question.points || 1;
          
          // Create a temporary structure for others if needed
          if (!poolMap.others) {
              poolMap.others = {
                  id: 'others',
                  name: 'Others',
                  responses: [],
                  maxScore: 0,
                  questionIds: []
              };
          }
          poolMap.others.responses.push(response);
          poolMap.others.maxScore += question.points || 1;
      }
  });
  
  // Calculate performance for each pool
  for (const poolId in poolMap) {
      const poolData = poolMap[poolId];
      
      if (poolId === 'others') {
          // Handle others separately
          let score = 0;
          let correctCount = 0;
          
          poolData.responses.forEach(response => {
              if (response.is_correct) {
                  correctCount++;
              }
              score += response.points_earned || 0;
          });
          
          othersPerformance.value.questionCount = poolData.responses.length;
          othersPerformance.value.correctCount = correctCount;
          othersPerformance.value.score = score;
          othersPerformance.value.percentage = othersPerformance.value.maxScore > 0 ? 
              (score / othersPerformance.value.maxScore) * 100 : 0;
      } else {
          // Handle regular pools
          const poolResponses = poolData.responses;
          const maxScore = poolData.maxScore;
          
          let score = 0;
          let correctCount = 0;
          
          poolResponses.forEach(response => {
              if (response.is_correct) {
                  correctCount++;
              }
              score += response.points_earned || 0;
          });
          
          const percentage = maxScore > 0 ? (score / maxScore) * 100 : 0;
          
          poolPerformance.value.push({
              id: poolData.id,
              name: poolData.name,
              questionCount: poolResponses.length,
              correctCount: correctCount,
              score: score,
              maxScore: maxScore,
              percentage: percentage
          });
      }
  }
};

// Lifecycle
onMounted(() => {
  analyzePoolPerformance();
});
</script>