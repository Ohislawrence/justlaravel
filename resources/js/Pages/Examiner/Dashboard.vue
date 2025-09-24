<template>
  <AppLayout title="Quiz Dashboard">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Quiz Dashboard
        </h2>
        <div class="flex space-x-2">
          <Link
            :href="route('examiner.quizzes.create')"
            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
          >
            Create New Exam
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Summary Cards (Updated with Account Stats) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <SummaryCard
            title="Total Quizzes"
            :value="stats.total_quizzes"
            icon="document-text"
            trend="up"
            :trend-value="stats.quizzes_growth"
          />
          <SummaryCard
            title="Active Attempts"
            :value="stats.active_attempts"
            icon="users"
            trend="down"
            :trend-value="stats.attempts_change"
          />
          <SummaryCard
            title="Avg. Score"
            :value="`${stats.avg_score}%`"
            icon="chart-bar"
            trend="neutral"
          />
          <SummaryCard
            title="Pass Rate"
            :value="`${stats.pass_rate}%`"
            icon="check-circle"
            trend="up"
            :trend-value="stats.pass_rate_change"
          />
          
          
        </div>

        <!-- Account Stats Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium mb-4">Account Usage</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Questions Usage -->
              <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Questions</h4>
                    <p class="text-2xl font-semibold mt-1">{{ UsedQuestion}} 
                      <span class="text-sm font-normal text-gray-500">/ {{ props.AllowedQuestionLimit }}</span>
                    </p>
                  </div>
                  <div class="bg-green-100 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="mt-3">
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-green-600 h-2 rounded-full" 
                      :style="{ width: props.percentageQuestionUsed + '%' }">
                    </div>
                  </div>
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>{{ props.percentageQuestionUsed }}% used</span>
                    <span>{{ props.remainingQuestion }} remaining</span>
                  </div>
                </div>
              </div>

              <!-- Monthly Questions Usage -->
              <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Question Attempts</h4>
                    <p class="text-2xl font-semibold mt-1">{{ UsedAttempt }} 
                      <span class="text-sm font-normal text-gray-500">/ {{ AllowedAttemptLimit }}</span>
                    </p>
                  </div>
                  <div class="bg-emerald-100 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="mt-3">
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-emerald-600 h-2 rounded-full" 
                      :style="{ width: props.percentageAttempt + '%' }">
                    </div>
                  </div>
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>{{ props.percentageAttempt }}% used</span>
                    <span>{{ props.remainingAttempts}} remaining</span>
                  </div>
                </div>
              </div>

              <!-- Quizzes Usage -->
              <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">AI Questions</h4>
                    <p class="text-2xl font-semibold mt-1">{{ UsedAiQuestion }} 
                      <span class="text-sm font-normal text-gray-500">/ {{ AllowedAiQuestiontLimit }}</span>
                    </p>
                  </div>
                  <div class="bg-teal-100 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                </div>
                <div class="mt-3">
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-teal-600 h-2 rounded-full" 
                      :style="{ width: props.percentageAiused + '%' }">
                    </div>
                  </div>
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>{{ props.percentageAiused }}% used</span>
                    <span>{{ props.remainingAiQuestion }} remaining</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200">
              <div class="flex justify-between items-center">
                <div>
                  <h4 class="text-sm font-medium text-gray-500">Subscription Plan</h4>
                  <p class="text-lg font-semibold">{{ planCurrent }}</p>
                </div>
                <Link 
                  :href="route('examiner.subscription.plans')" 
                  class="text-sm text-green-600 hover:text-green-800 font-medium"
                >
                  Manage Subscription →
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Performance Overview -->
          <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Performance Overview</h3>
                <select
                  v-model="timeRange"
                  class="block pl-3 pr-8 py-2 text-base border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                >
                  <option value="7">Last 7 days</option>
                  <option value="30">Last 30 days</option>
                  <option value="90">Last 90 days</option>
                </select>
              </div>
              <div class="chart-container">
                <LineChart
                  :data="performanceChartData"
                  :options="chartOptions"
                />
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Recent Activity</h3>
              <div v-if="!recentActivity || recentActivity.length === 0" class="text-center py-4 text-gray-500">
                No recent activity.
              </div>
              <ul v-else class="space-y-4">
                <li
                  v-for="activity in recentActivity"
                  :key="activity.id"
                  class="flex items-start"
                >
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-50 flex items-center justify-center">
                    <span class="text-green-600">
                      <template v-if="activity.type === 'quiz_completed'">
                        <DocumentCheckIcon class="h-5 w-5" />
                      </template>
                      <template v-else-if="activity.type === 'quiz_created'">
                        <DocumentPlusIcon class="h-5 w-5" />
                      </template>
                      <template v-else>
                        <BellIcon class="h-5 w-5" />
                      </template>
                    </span>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">
                      {{ activity.title }}
                    </p>
                    <p class="text-sm text-gray-500">
                      {{ activity.description }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                      {{ formatDateTime(activity.timestamp) }}
                    </p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Bottom Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Quiz Distribution -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Quiz Types</h3>
              <div class="chart-container">
                <DoughnutChart
                  :data="quizTypeData"
                  :options="chartOptions"
                />
              </div>
            </div>
          </div>

          <!-- Top Quizzes -->
          <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Top Quizzes</h3>
                <Link
                  :href="route('examiner.quizzes.index')"
                  class="text-sm text-green-600 hover:text-green-800"
                >
                  View All
                </Link>
              </div>
              <div v-if="!topQuizzes || topQuizzes.length === 0" class="text-center py-4 text-gray-500">
                 No quizzes found.
              </div>
              <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quiz
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Attempts
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Avg. Score
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pass Rate
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="quiz in topQuizzes" :key="quiz.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="text-sm font-medium text-gray-900">
                            <Link :href="route('examiner.quizzes.show', quiz.id)">
                              {{ quiz.title }}
                            </Link>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ quiz.attempts_count }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ quiz.avg_score }}%
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="quiz.pass_rate >= 70 ? 'bg-green-100 text-green-800' : quiz.pass_rate >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'">
                          {{ quiz.pass_rate }}%
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
  DocumentCheckIcon,
  DocumentPlusIcon,
  BellIcon
} from '@heroicons/vue/24/outline';
import SummaryCard from '@/Components/SummaryCard.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';

const props = defineProps({
  stats: {
    type: Object,
    required: true
  },
  recentActivity: {
    type: Array,
    default: () => []
  },
  topQuizzes: {
    type: Array,
    default: () => []
  },
  performanceData: {
    type: Object,
    required: true
  },
  quizTypes: {
    type: Object,
    required: true
  },
  organization: Object,
  planCurrent: String,
  AllowedQuestionLimit: Number,
  UsedQuestion: Number,
  UsedAttempt: Number,
  AllowedAttemptLimit: Number,
  UsedAiQuestion: Number,
  AllowedAiQuestiontLimit: Number,
  percentageQuestionUsed: Number,
  remainingQuestion: Number,
  percentageAttempt: Number,
  remainingAttempts: Number,
  percentageAiused: Number,
  remainingAiQuestion: Number,

  // New prop for account statistics
  accountStats: {
    type: Object,
    required: true,
    default: () => ({
      questions_created: 0,
      questions_allowed: 1000,
      questions_percentage: 50,
      questions_remaining: 1000,
      questions_created_monthly: 0,
      questions_allowed_monthly: 500,
      questions_monthly_percentage: 50,
      questions_monthly_remaining: 500,
      quizzes_created: 0,
      quizzes_allowed: 50,
      quizzes_percentage: 50,
      quizzes_remaining: 50,
      active_subscriptions: 1,
      subscription_plan: "Basic Plan"
    })
  }
});

const timeRange = ref('7');

const activeSubscription = computed(() => {
  return props.organization.active_subscription || props.organization.current_subscription;
});

const performanceChartData = computed(() => ({
  labels: props.performanceData.labels,
  datasets: [
    {
      label: 'Average Score',
      data: props.performanceData.avg_scores,
      borderColor: '#10B981',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      fill: true,
      tension: 0.3
    },
    {
      label: 'Attempts',
      data: props.performanceData.attempts,
      borderColor: '#059669',
      backgroundColor: 'rgba(5, 150, 105, 0.1)',
      fill: true,
      tension: 0.3
    }
  ]
}));

const quizTypeData = computed(() => ({
  labels: Object.keys(props.quizTypes),
  datasets: [{
    data: Object.values(props.quizTypes),
    backgroundColor: [
      '#10B981',
      '#059669',
      '#047857',
      '#166534',
      '#14532d'
    ],
    hoverOffset: 4
  }]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
};

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString();
};
</script>

<style scoped>
.chart-container {
  min-height: 300px;
  position: relative;
}
</style>