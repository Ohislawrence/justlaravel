<template>
    <AppLayout :title="`Quiz Results: ${quiz.title}`">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Results Analysis: {{ quiz.title }}
          </h2>
          <div class="flex space-x-2">
            <Link 
              :href="route('examiner.quizzes.show', quiz.id)"
              class="btn btn-sm btn-outline"
            >
              Back to Quiz
            </Link>
          </div>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <SummaryCard 
              title="Total Attempts" 
              :value="analysis.total_attempts" 
              icon="users"
            />
            <SummaryCard 
              title="Average Score" 
              :value="`${Math.round(analysis.average_score)}%`"
              icon="chart-bar"
            />
            <SummaryCard 
              title="Pass Rate" 
              :value="`${Math.round(analysis.pass_rate)}%`"
              icon="check-circle"
            />
          </div>
  
          <!-- Groups Filter -->
          <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">Filter by Group</h3>
            <div class="flex flex-wrap gap-2">
              <Link
                :href="route('examiner.quizzes.analysis.index', quiz.id)"
                class="px-4 py-2 rounded-full text-sm"
                :class="{
                  'bg-blue-100 text-blue-800': !$page.url.includes('group'),
                  'bg-gray-100 text-gray-800': $page.url.includes('group')
                }"
              >
                All Participants
              </Link>
              <Link
                v-for="group in groups"
                :key="group.id"
                :href="route('examiner.quizzes.analysis.group', { quiz: quiz.id, group: group.id })"
                class="px-4 py-2 rounded-full text-sm"
                :class="{
                  'bg-blue-100 text-blue-800': $page.url.includes(`group=${group.id}`),
                  'bg-gray-100 text-gray-800': !$page.url.includes(`group=${group.id}`)
                }"
              >
                {{ group.name }} ({{ group.members_count }})
              </Link>
            </div>
          </div>
          

          <!-- Attempts Table -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Attempts</h3>
              <AttemptsTable :quiz="quiz" :attempts="attempts" />
            </div>
          </div>

          <!-- Question Statistics -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-medium mb-4">Question Performance</h3>
              <QuestionStatsTable :stats="questionStats" :quiz="quiz" />
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
  import QuestionStatsTable from '@/Components/Tables/QuestionStatsTable.vue';
  import AttemptsTable from '@/Components/Tables/AttemptsTable.vue';
  
  const props = defineProps({
    quiz: Object,
    analysis: Array,
    groups: Array,
    questionStats: Array,
    averageScore: Number,
    passRate: Number,
    attempts: Object,
  });
  </script>