<template>
  <AppLayout :title="`Attempt #${attempt?.attempt_number || 'Unknown'}`">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Attempt Summary -->
        <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6 border-l-4" :class="attempt?.is_passed ? 'border-green-500' : 'border-red-500'">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-xl font-semibold">Attempt Details</h2>
              <p class="text-gray-600 mt-2">
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
           
        <!-- Questions & Responses -->
        <div class="space-y-6">
          <div v-for="question in questions" :key="question.id" class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow duration-200 border border-gray-100">
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
import QuizPoolAnalysis from '@/Components/Tables/QuizPoolAnalysis.vue';
import ProctoringTable from '@/Components/Tables/ProctoringTable.vue';

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