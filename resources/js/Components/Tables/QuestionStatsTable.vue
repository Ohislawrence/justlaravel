<template>
  <div class="overflow-x-auto">
    <div v-if="!questionStats || questionStats.length === 0" class="text-center py-8 text-gray-500">
      No question statistics available
    </div>
    
    <table v-else class="min-w-full divide-y divide-gray-200">
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
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(question, index) in questionStats" :key="index">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ question.text }}
              <span v-if="question.is_ai" class="ml-2 text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full">
                AI-Generated
              </span>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ questionTypeLabel(question.type) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ question.correctCount }} / {{ question.totalAttempts }}
            ({{ calculateCorrectPercentage(question.correctCount, question.totalAttempts) }}%)
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ question.averagePoints.toFixed(1) }} / {{ question.maxPoints }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  attempts: {
    type: [Array, Object],
    required: true,
    default: () => [],
  },
  quiz: {
    type: Object,
    default: null
  },
});

// Extract attempts array from the props
const attemptsArray = computed(() => {
  if (Array.isArray(props.attempts)) {
    return props.attempts;
  } else if (props.attempts && Array.isArray(props.attempts.data)) {
    return props.attempts.data; // Handle Laravel paginated response
  }
  return [];
});

// Get all responses from attempts (with question data eager loaded)
const allResponses = computed(() => {
  const responses = [];
  attemptsArray.value.forEach(attempt => {
    // Check if responses are available and eager loaded with question data
    if (attempt.responses && Array.isArray(attempt.responses)) {
      responses.push(...attempt.responses);
    }
  });
  return responses;
});

// Calculate question statistics from responses
const questionStats = computed(() => {
  const responses = allResponses.value;
  if (!responses || responses.length === 0) return [];
  
  // Create a map to store question statistics
  const questionMap = new Map();
  
  // Process each response
  responses.forEach(response => {
    // Check if question data is available in the response
    const question = response.question;
    if (!question || !question.id) return;
    
    // Initialize question in map if not exists
    if (!questionMap.has(question.id)) {
      questionMap.set(question.id, {
        id: question.id,
        text: question.question || 'Unknown Question',
        type: question.type || 'unknown',
        is_ai: question.is_ai || false,
        maxPoints: question.points || 1,
        correctCount: 0,
        totalAttempts: 0,
        totalPoints: 0
      });
    }
    
    // Update question statistics
    const questionStat = questionMap.get(question.id);
    questionStat.totalAttempts += 1;
    questionStat.totalPoints += response.points_earned || 0;
    
    if (response.is_correct) {
      questionStat.correctCount += 1;
    }
  });
  
  // Convert map to array and calculate averages
  return Array.from(questionMap.values()).map(stat => ({
    ...stat,
    averagePoints: stat.totalAttempts > 0 ? stat.totalPoints / stat.totalAttempts : 0
  }));
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

const calculateCorrectPercentage = (correct, total) => {
  if (!total || total === 0) return 0;
  return Math.round((correct / total) * 100);
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