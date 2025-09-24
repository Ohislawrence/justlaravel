<template>
  <AppLayout :title="`Attempt #${attempt?.attempt_number || 'Unknown'}`">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-3">
          {{ attempt?.user?.name || 'Unknown User' }}'s attempt #{{ attempt?.attempt_number || 'Unknown' }} 
        </h2>
       <!-- Attempt Summary -->
        <div class="bg-white shadow-sm rounded-xl border-2 p-6 mb-6 transition-shadow duration-200"
            :class="attempt?.is_passed ? 'border-green-500 shadow-green-50/30' : 'border-red-500 shadow-red-50/30'">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
              <div class="space-y-1.5">
                <p class="text-sm">
                  <span class="font-semibold text-gray-900">Examinee:</span>
                  <span class="ml-1 text-gray-900">{{ attempt?.user?.name || 'Unknown User' }}</span>
                  <span class="text-gray-500 ml-2">({{ attempt?.user?.email || 'No email' }})</span>
                </p>
                <p class="text-sm text-gray-600 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Completed: <span class="font-medium ml-1">{{ formatDateTime(attempt?.completed_at) }}</span>
                </p>
              </div>
            </div>
            <div class="flex flex-col items-start lg:items-end space-y-2">
              <div class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold shadow-sm"
                  :class="attempt?.is_passed 
                    ? 'bg-green-50 text-green-700 border border-green-200' 
                    : 'bg-red-50 text-red-700 border border-red-200'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        :d="attempt?.is_passed ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                </svg>
                {{ attempt?.percentage || 0 }}%
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Time Spent: <span class="font-medium ml-1">{{ formatTime(attempt?.time_spent) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-6">
          <QuizPoolAnalysis 
            :quiz="quiz" 
            :questionpools="questionpools"
            :attempts="attempt" 
            :userId="attempt.user.id" 
          />
        </div>

        <!-- Proctoring Violations Section -->
        <div class="mb-6">
            <ProctoringTable :attempts="violations" />
        </div>

        <!-- Question & answer -->
        <div class="mb-6">
        <QuestionAttempts
        :responses="attempt.responses"
        />
        </div>
           
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import QuestionResponseCard from '@/Components/QuestionResponseCard.vue';
import QuizPoolAnalysis from '@/Components/Tables/QuizPoolAnalysis.vue';
import ProctoringTable from '@/Components/Tables/ProctoringTable.vue';
import QuestionAttempts from '@/Components/QuestionAttempts.vue';

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
  },
  questionpools: Object,
  violations: Array,
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

<style scoped>
/* Enhanced styling for the attempt details card */
.bg-white {
  background-color: #ffffff;
}

.shadow-sm {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.hover\:shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Green-themed passed status */
.bg-green-100 {
  background-color: #ecfdf5;
}

.text-green-800 {
  color: #065f46;
}

.border-green-500 {
  border-color: #10B981;
}

/* Red-themed failed status */
.bg-red-100 {
  background-color: #fee2e2;
}

.text-red-800 {
  color: #991b1b;
}

.border-red-500 {
  border-color: #ef4444;
}

/* Text colors */
.text-gray-600 {
  color: #4b5563;
}

.text-gray-500 {
  color: #6b7280;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .flex {
      flex-direction: column;
  }
  
  .justify-between > *:not(:last-child) {
      margin-bottom: 1rem;
  }
}

/* Animation for hover effect */
.transition-shadow {
  transition: box-shadow 0.3s ease;
}
</style>