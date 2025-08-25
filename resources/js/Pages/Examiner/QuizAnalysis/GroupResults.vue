<template>
  <AppLayout :title="`Group Results - ${group.name}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ quiz.title }} - {{ group.name }} Results
        </h2>
        <Link 
          :href="route('examiner.quizzes.analysis.index', { quiz: quiz.id })"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
        >
          <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
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
            color="green"
          />
          <SummaryCard 
            title="Attempts"
            :value="totalAttempts || 0"
            icon="clipboard-document"
            color="green"
          />
          <SummaryCard 
            title="Average Score"
            :value="`${Math.round(averageScore)}%`"
            icon="chart-bar"
            color="green"
          />
          <SummaryCard 
            title="Pass Rate"
            :value="`${Math.round(passRate)}%`"
            icon="check-circle"
            color="green"
          />
        </div>

        <!-- Performance Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <h3 class="text-lg font-medium mb-4">Score Distribution</h3>
            <div class="chart-container">
              <BarChart 
                v-if="scoreDistributionData"
                :data="scoreDistributionData"
                :options="chartOptions"
              />
              <div v-else class="text-gray-500 text-center py-8">
                No score data available
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <h3 class="text-lg font-medium mb-4">Completion Over Time</h3>
            <div class="chart-container">
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
        </div>

        <!-- Attempts Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium">Member Attempts</h3>
              <button 
                @click="exportToCSV"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                :disabled="!attempts.data?.length"
              >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
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
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
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
      backgroundColor: '#10B981' // Changed to green
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
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `${context.dataset.label}: ${context.parsed.y}`;
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        stepSize: 1
      }
    }
  }
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

/* Card hover effect */
.hover\:shadow-md:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Border styling */
.border-gray-100 {
    border-color: #f3f4f6;
}

/* Disabled state */
.disabled\:opacity-25:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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