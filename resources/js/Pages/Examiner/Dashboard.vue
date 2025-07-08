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
              class="btn btn-primary"
            >
              Create New Quiz
            </Link>
          </div>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Summary Cards -->
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
  
          <!-- Main Content -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Performance Overview -->
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-medium">Performance Overview</h3>
                  <select
                    v-model="timeRange"
                    class="block pl-3 pr-8 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                  >
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                  </select>
                </div>
                <LineChart
                  :data="performanceChartData"
                  :options="chartOptions"
                />
              </div>
            </div>
  
            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium mb-4">Recent Activity</h3>
                <ul class="space-y-4">
                  <li
                    v-for="activity in recentActivity"
                    :key="activity.id"
                    class="flex items-start"
                  >
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center">
                      <span class="text-blue-600">
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
                <DoughnutChart
                  :data="quizTypeData"
                  :options="chartOptions"
                />
              </div>
            </div>
  
            <!-- Top Quizzes -->
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-medium">Top Quizzes</h3>
                  <Link
                    :href="route('examiner.quizzes.index')"
                    class="text-sm text-blue-600 hover:text-blue-800"
                  >
                    View All
                  </Link>
                </div>
                <div class="overflow-x-auto">
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
    BellIcon,
    ArrowRightIcon
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
    }
  });
  
  const timeRange = ref('7');
  
  const performanceChartData = computed(() => ({
    labels: props.performanceData.labels,
    datasets: [
      {
        label: 'Average Score',
        data: props.performanceData.avg_scores,
        borderColor: '#3B82F6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        fill: true,
        tension: 0.3
      },
      {
        label: 'Attempts',
        data: props.performanceData.attempts,
        borderColor: '#10B981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
        '#3B82F6',
        '#10B981',
        '#F59E0B',
        '#EF4444',
        '#8B5CF6'
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
  }
  </style>