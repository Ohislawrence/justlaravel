<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
    <div class="p-6">
      <h3 class="text-lg font-medium mb-4">Survey Overview</h3>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-green-50 rounded-lg">
          <div class="text-2xl font-bold text-green-600">{{ analysis.total_attempts || 0 }}</div>
          <div class="text-sm text-green-800">Total Responses</div>
        </div>
        <div class="text-center p-4 bg-blue-50 rounded-lg">
          <div class="text-2xl font-bold text-blue-600">{{ completionRate }}%</div>
          <div class="text-sm text-blue-800">Completion Rate</div>
        </div>
        <div class="text-center p-4 bg-purple-50 rounded-lg">
          <div class="text-2xl font-bold text-purple-600">{{ averageTime }}</div>
          <div class="text-sm text-purple-800">Avg. Time</div>
        </div>
        <div class="text-center p-4 bg-orange-50 rounded-lg">
          <div class="text-2xl font-bold text-orange-600">{{ uniqueRespondents }}</div>
          <div class="text-sm text-orange-800">Unique Respondents</div>
        </div>
      </div>
      
      <!-- Additional Survey-specific Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4" v-if="quiz.quiz_type === 'survey'">
        <div class="text-center p-3 bg-gray-50 rounded-lg">
          <div class="text-lg font-bold text-gray-700">{{ totalQuestions }}</div>
          <div class="text-xs text-gray-600">Total Questions</div>
        </div>
        <div class="text-center p-3 bg-gray-50 rounded-lg">
          <div class="text-lg font-bold text-gray-700">{{ averageResponsesPerQuestion }}</div>
          <div class="text-xs text-gray-600">Avg. Responses per Q</div>
        </div>
        <div class="text-center p-3 bg-gray-50 rounded-lg">
          <div class="text-lg font-bold text-gray-700">{{ responseRate }}%</div>
          <div class="text-xs text-gray-600">Overall Response Rate</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  quiz: Object,
  analysis: Object,
  attempts: Object,
  questionStats: Array
});

const completionRate = computed(() => {
  const total = props.analysis.total_attempts || 0;
  const completed = props.attempts?.data?.filter(attempt => attempt.completed_at)?.length || 0;
  return total > 0 ? Math.round((completed / total) * 100) : 0;
});

const averageTime = computed(() => {
  const attempts = props.attempts?.data || [];
  if (attempts.length === 0) return '0m';
  
  const totalSeconds = attempts.reduce((sum, attempt) => {
    return sum + (attempt.time_spent || 0);
  }, 0);
  
  const avgMinutes = Math.round(totalSeconds / attempts.length / 60);
  return `${avgMinutes}m`;
});

const uniqueRespondents = computed(() => {
  const attempts = props.attempts?.data || [];
  const respondents = new Set();
  
  attempts.forEach(attempt => {
    if (attempt.user_id) {
      respondents.add(`user_${attempt.user_id}`);
    } else if (attempt.guest_email) {
      respondents.add(`guest_${attempt.guest_email}`);
    } else if (attempt.guest_id) {
      respondents.add(`guest_${attempt.guest_id}`);
    }
  });
  
  return respondents.size;
});

// Survey-specific computed properties
const totalQuestions = computed(() => {
  return props.questionStats?.length || 0;
});

const averageResponsesPerQuestion = computed(() => {
  const totalResponses = props.analysis.total_attempts || 0;
  const totalQuestions = props.questionStats?.length || 1;
  return Math.round(totalResponses / totalQuestions);
});

const responseRate = computed(() => {
  const totalQuestions = props.questionStats?.length || 0;
  const totalPossibleResponses = totalQuestions * (props.analysis.total_attempts || 0);
  if (totalPossibleResponses === 0) return 0;
  
  // Calculate actual responses from question stats
  const totalActualResponses = props.questionStats?.reduce((sum, question) => {
    return sum + (question.total_responses || 0);
  }, 0) || 0;
  
  return Math.round((totalActualResponses / totalPossibleResponses) * 100);
});
</script>