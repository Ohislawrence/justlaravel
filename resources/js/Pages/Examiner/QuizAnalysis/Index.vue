<template>
  <AppLayout :title="`Results: ${quiz.title}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ quiz.quiz_type === 'survey' ? 'Survey Results' : 'Results Analysis' }}: {{ quiz.title }}
        </h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('examiner.quizzes.show', quiz.id)"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to {{ quiz.quiz_type === 'survey' ? 'Survey' : 'Quiz' }}
          </Link>
        </div>
      </div>
    </template>
    

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Survey Components -->
        <template v-if="quiz.quiz_type === 'survey'">
          <!-- Survey Summary Cards -->
          <SurveySummary 
            :quiz="quiz" 
            :analysis="analysis"
            class="mb-6"
          />

          <!-- Survey Response Breakdown -->
          <SurveyBreakdown 
            :quiz="quiz"
            :attempts="attempts"
            :questionStats="questionStats"
            class="mb-6"
          />

          <!-- Survey Charts -->
          <SurveyCharts 
            :quiz="quiz"
            :attempts="attempts"
            :questionStats="questionStats"
            class="mb-6"
          />
          
          
          <!-- Survey Responses Table -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-100">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Individual Responses</h3>
              <SurveyResponsesTable :quiz="quiz" :attempts="attempts" />
            </div>
          </div>
        </template>

        <!-- Quiz Components (Original Content) -->
        <template v-else>
          <!-- Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <SummaryCard 
              title="Total Attempts" 
              :value="analysis.total_attempts" 
              icon="users"
              color="green"
            />
            <SummaryCard 
              title="Average Score" 
              :value="`${Math.round(analysis.average_score)}%`"
              icon="chart-bar"
              color="green"
            />
            <SummaryCard 
              title="Pass Rate" 
              :value="`${Math.round(analysis.pass_rate)}%`"
              icon="check-circle"
              color="green"
            />
          </div>

          <!-- Groups Filter -->
          <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">Filter by Group</h3>
            <div class="flex flex-wrap gap-2">
              <Link
                :href="route('examiner.quizzes.analysis.index', quiz.id)"
                class="px-4 py-2 rounded-full text-sm transition-all duration-200 hover:shadow-md"
                :class="{
                  'bg-green-100 text-green-800 border border-green-200': !$page.url.includes('group'),
                  'bg-gray-100 text-gray-800 border border-gray-200': $page.url.includes('group')
                }"
              >
                All Participants
              </Link>
              <Link
                v-for="group in groups"
                :key="group.id"
                :href="route('examiner.quizzes.analysis.group', { quiz: quiz.id, group: group.id })"
                class="px-4 py-2 rounded-full text-sm transition-all duration-200 hover:shadow-md"
                :class="{
                  'bg-green-100 text-green-800 border border-green-200': $page.url.includes(`group=${group.id}`),
                  'bg-gray-100 text-gray-800 border border-gray-200': !$page.url.includes(`group=${group.id}`)
                }"
              >
                {{ group.name }} ({{ group.members_count }})
              </Link>
            </div>
          </div>

          <!-- Attempts Table -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-100">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Attempts</h3>
              <AttemptsTable :quiz="quiz" :attempts="attempts" :grouped="true" :quiz-id="quiz.id"/>
            </div>
          </div>

          <!-- Question Statistics -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Question Performance</h3>
              <QuestionStatsTable :attempts="attempt" :quiz="quiz" />
            </div>
          </div>
        </template>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import SummaryCard from '@/Components/SummaryCard.vue';
import QuestionStatsTable from '@/Components/Tables/QuestionStatsTable.vue';
import AttemptsTable from '@/Components/Tables/AttemptsTable.vue';
import SurveySummary from '@/Components/Survey/SurveySummary.vue';
import SurveyBreakdown from '@/Components/Survey/SurveyBreakdown.vue';
import SurveyCharts from '@/Components/Survey/SurveyCharts.vue';
import SurveyResponsesTable from '@/Components/Survey/SurveyResponsesTable.vue';

const props = defineProps({
  quiz: Object,
  analysis: Array,
  groups: Array,
  questionStats: Array,
  averageScore: Number,
  passRate: Number,
  attempts: Object,
  attempt: Object,
  questionpools: Object,
});
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

/* Green-themed summary cards */
.bg-green-100 {
  background-color: #ecfdf5;
  border-color: #d1fae5;
}

.text-green-800 {
  color: #065f46;
}

/* Hover effects */
.transition-all {
  transition: all 0.3s ease;
}

.hover\:shadow-md:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Border styling */
.border-gray-100 {
  border-color: #f3f4f6;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .grid {
      grid-template-columns: 1fr;
  }
  
  .gap-4 > *:not(:last-child) {
      margin-bottom: 1rem;
  }
}
</style>