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
              :value="group.members_count"
              icon="users"
            />
            <SummaryCard 
              title="Attempts"
              :value="attempts.total"
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
                :data="scoreDistributionData"
                :options="chartOptions"
              />
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium mb-4">Completion Over Time</h3>
              <LineChart 
                :data="completionOverTimeData"
                :options="chartOptions"
              />
            </div>
          </div>
  
          <!-- Question Performance -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Question Performance</h3>
              <QuestionStatsTable 
                :stats="questionStats"
                :quiz="quiz"
              />
            </div>
          </div>
  
          <!-- Attempts Table -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Member Attempts</h3>
                <button 
                  @click="exportToCSV"
                  class="btn btn-sm btn-primary"
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
      required: true
    },
    group: {
      type: Object,
      required: true
    },
    attempts: {
      type: Object,
      required: true
    },
    questionStats: {
      type: Array,
      required: true
    },
    averageScore: {
      type: Number,
      required: true
    },
    passRate: {
      type: Number,
      required: true
    }
  });
  
  const scoreDistributionData = computed(() => {
    const scores = props.attempts.data.map(a => Math.floor(a.percentage / 10) * 10);
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
    const attemptsByDate = props.attempts.data.reduce((acc, attempt) => {
      const date = new Date(attempt.completed_at).toLocaleDateString();
      acc[date] = (acc[date] || 0) + 1;
      return acc;
    }, {});
  
    return {
      labels: Object.keys(attemptsByDate).sort(),
      datasets: [{
        label: 'Attempts',
        data: Object.values(attemptsByDate),
        borderColor: '#10B981',
        fill: false
      }]
    };
  });
  
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false
  };
  
  const exportToCSV = () => {
    const headers = ['Name', 'Email', 'Score', 'Status', 'Time Spent', 'Completed At'];
    const rows = props.attempts.data.map(attempt => [
      attempt.user.name,
      attempt.user.email,
      `${attempt.percentage}%`,
      attempt.is_passed ? 'Passed' : 'Failed',
      formatTime(attempt.time_spent),
      new Date(attempt.completed_at).toLocaleString()
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
  /* Custom styles can be added here */
  .chart-container {
    height: 300px;
  }
  </style>