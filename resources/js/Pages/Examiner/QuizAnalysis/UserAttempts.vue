<template>
  <AppLayout :title="`${user.name}'s Attempts - ${quiz.title}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ user.name }}'s Attempts: {{ quiz.title }}
        </h2>
        <div class="flex space-x-2">
          <Link 
            :href="route('examiner.quizzes.analysis.index', quiz.id)"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to All Results
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- User Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <SummaryCard 
            title="Total Attempts" 
            :value="attempts.total" 
            icon="users"
            color="green"
          />
          <SummaryCard 
            title="Best Score" 
            :value="`${Math.round(bestScore)}%`"
            icon="trophy"
            color="green"
          />
          <SummaryCard 
            title="Average Score" 
            :value="`${Math.round(averageScore)}%`"
            icon="chart-bar"
            color="green"
          />
        </div>

        <!-- Attempts Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium mb-4">All Attempts</h3>
            <AttemptsTable :quiz="quiz" :attempts="attempts" :grouped="false" :quiz-id="quiz.id" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import SummaryCard from '@/Components/SummaryCard.vue';
import AttemptsTable from '@/Components/Tables/AttemptsTable.vue';

const props = defineProps({
  quiz: Object,
  user: Object,
  attempts: Object,
  questionStats: Array,
  averageScore: Number,
  bestScore: Number,
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
}

.text-green-800 {
  color: #065f46;
}

/* Border styling */
.border-gray-100 {
  border-color: #f3f4f6;
}

/* Hover effects */
.transition {
  transition: all 0.3s ease;
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