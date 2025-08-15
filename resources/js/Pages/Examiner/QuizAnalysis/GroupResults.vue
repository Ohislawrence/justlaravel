<template>
  <AppLayout :title="`Group Results - ${group.name}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ quiz.title }} - {{ group.name }} Results
        </h2>
        <Link 
          :href="route('examiner.quizzes.analysis.index', { quiz: quiz.id })"
          class="btn btn-sm btn-outline"
        >
          Back to Full Results
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Group Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <SummaryCard 
            title="Group Members"
            :value="group.members_count || 0"
            icon="users"
          />
          <SummaryCard 
            title="Attempts"
            :value="totalAttempts || 0"
            icon="clipboard-document"
          />
          <SummaryCard 
            title="Average Score"
            :value="`${Math.round(averageScore)}%`"
            icon="chart-bar"
          />
          <SummaryCard 
            title="Pass Rate"
            :value="`${Math.round(passRate)}%`"
            icon="check-circle"
          />
        </div>

        <!-- Performance Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Score Distribution</h3>
            <BarChart 
              v-if="scoreDistributionData"
              :data="scoreDistributionData"
              :options="chartOptions"
            />
            <div v-else class="text-gray-500 text-center py-8">
              No score data available
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Completion Over Time</h3>
            <LineChart 
              v-if="completionOverTimeData"
              :data="completionOverTimeData"
              :options="chartOptions"
              :key="JSON.stringify(completionOverTimeData)" 
            />
            <div v-else class="text-gray-500 text-center py-8">
              No completion data available
            </div>
          </div>
        </div>

        <!-- Attempts Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium">Member Attempts</h3>
              <button 
                @click="exportToCSV"
                class="btn btn-sm btn-primary"
                :disabled="!attempts.data?.length"
              >
                Export to CSV
              </button>
            </div>
            <AttemptsTable 
              :attempts="attempts"
              :quiz-id="quiz.id"
            />
          </div>
        </div>

        <!-- Question Performance -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg ">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium mb-4">Question Performance</h3>
            <QuestionStatsTable 
              :stats="questionStats"
              :quiz="quiz"
            />
          </div>
        </div>

        
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import SummaryCard from '@/Components/SummaryCard.vue';
import QuestionStatsTable from '@/Components/Tables/QuestionStatsTable.vue';
import AttemptsTable from '@/Components/Tables/AttemptsTable.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import LineChart from '@/Components/Charts/LineChart.vue';

const props = defineProps({
  quiz: {
    type: Object,
    required: true,
    default: () => ({})
  },
  group: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      members_count: 0
    })
  },
  attempts: {
    type: Object,
    required: true,
    default: () => ({
      data: [],
      links: [],
      meta: {
        total: 0
      }
    }),
    validator: (value) => {
      return value.data && Array.isArray(value.data)
    }
  },
  questionStats: {
    type: Array,
    required: true,
    default: () => []
  },
  averageScore: {
    type: Number,
    required: true,
    default: 0
  },
  passRate: {
    type: Number,
    required: true,
    default: 0
  },
  totalAttempts: {
    type: Number,
    default: 0
  },
  allAttempts: {
    type: Array,
    default: () => []
  }
});

const scoreDistributionData = computed(() => {
  if (!props.allAttempts?.length) return null;
  
  const scores = props.allAttempts
    .map(a => a.percentage ? Math.floor(a.percentage / 10) * 10 : 0);
  const distribution = Array(10).fill(0);
  
  scores.forEach(score => {
    const index = Math.min(Math.floor(score / 10), 9);
    distribution[index]++;
  });

  return {
    labels: ['0-10%', '10-20%', '20-30%', '30-40%', '40-50%', 
            '50-60%', '60-70%', '70-80%', '80-90%', '90-100%'],
    datasets: [{
      label: 'Number of Attempts',
      data: distribution,
      backgroundColor: '#3B82F6'
    }]
  };
});

const completionOverTimeData = computed(() => {
  if (!props.allAttempts?.length) return null;

  // Group attempts by date
  const attemptsByDate = props.allAttempts.reduce((acc, attempt) => {
    if (!attempt.completed_at) return acc;
    
    // Format date as YYYY-MM-DD for consistent sorting
    const date = new Date(attempt.completed_at);
    const dateString = date.toISOString().split('T')[0];
    
    acc[dateString] = (acc[dateString] || 0) + 1;
    return acc;
  }, {});

  // Sort dates chronologically
  const sortedDates = Object.keys(attemptsByDate).sort((a, b) => 
    new Date(a) - new Date(b)
  );

  // Format dates for display (e.g., "Jan 1")
  const formattedDates = sortedDates.map(date => {
    const d = new Date(date);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  });

  return {
    labels: formattedDates,
    datasets: [{
      label: 'Attempts Completed',
      data: sortedDates.map(date => attemptsByDate[date]),
      borderColor: '#10B981',
      backgroundColor: '#10B98120', // Slightly transparent version
      borderWidth: 2,
      tension: 0.1,
      fill: true
    }]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};

const exportToCSV = () => {
  if (!props.attempts.data?.length) return;

  const headers = ['Name', 'Email', 'Score', 'Status', 'Time Spent', 'Completed At'];
  const rows = props.attempts.data.map(attempt => [
    attempt.user?.name || 'Unknown',
    attempt.user?.email || '',
    `${attempt.percentage?.toFixed(1) || 0}%`,
    attempt.is_passed ? 'Passed' : 'Failed',
    formatTime(attempt.time_spent),
    attempt.completed_at ? new Date(attempt.completed_at).toLocaleString() : 'N/A'
  ]);

  let csvContent = "data:text/csv;charset=utf-8," 
    + headers.join(",") + "\n" 
    + rows.map(row => row.join(",")).join("\n");

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", `${props.quiz.title}_${props.group.name}_results.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const formatTime = (seconds) => {
  if (!seconds) return 'N/A';
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}m ${secs}s`;
};
</script>

<style scoped>
.chart-container {
  height: 300px;
}
.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>