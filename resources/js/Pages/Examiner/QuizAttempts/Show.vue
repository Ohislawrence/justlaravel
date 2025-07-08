<template>
    <AppLayout :title="`Attempt #${attempt?.attempt_number || 'Unknown'}`">
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Attempt Summary -->
          <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-xl font-semibold">Attempt Details</h2>
                <p class="text-gray-600">
                  User: {{ attempt?.user?.name || 'Unknown User' }} ({{ attempt?.user?.email || 'No email' }})
                </p>
                <p class="text-gray-600">
                  Completed: {{ formatDateTime(attempt?.completed_at) }}
                </p>
              </div>
              <div class="text-right">
                <span class="px-3 py-1 rounded-full text-sm font-medium"
                  :class="attempt?.is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  Score: {{ attempt?.percentage || 0 }}%
                </span>
                <p class="text-sm text-gray-500 mt-1">
                  Time: {{ formatTime(attempt?.time_spent) }}
                </p>
              </div>
            </div>
          </div>
             
          <!-- Questions & Responses -->
          <div class="space-y-6">
            <div v-for="question in questions" :key="question.id" class="bg-white shadow-sm sm:rounded-lg p-6">
              <QuestionResponseCard 
                :question="question"
                :response="question.responses?.[0]"
              />
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import QuestionResponseCard from '@/Components/QuestionResponseCard.vue';
  
  const props = defineProps({
    quiz: {
      type: Object,
      default: () => ({})
    },
    attempt: {
      type: Object,
      default: () => ({})
    },
    questions: {
      type: Array,
      default: () => []
    }
  });
  
  const formatDateTime = (date) => {
    if (!date) return 'N/A';
    try {
      return new Date(date).toLocaleString();
    } catch (error) {
      return 'Invalid Date';
    }
  };
  
  const formatTime = (seconds) => {
    if (!seconds) return 'N/A';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}m ${secs}s`;
  };
  </script>