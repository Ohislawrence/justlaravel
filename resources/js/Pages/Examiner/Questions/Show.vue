<template>
  <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <h2 class="text-lg font-medium text-gray-900">{{ question.question }}</h2>
    <div class="mt-4">
      <h3 class="text-sm font-medium text-gray-700 mb-2">Pools containing this question:</h3>
      <ul class="space-y-2">
        <li 
          v-for="pool in question.pools" 
          :key="pool.id" 
          class="flex items-center justify-between p-3 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors duration-150"
        >
          <span class="text-sm font-medium text-gray-800">{{ pool.name }}</span>
          <button 
            @click="detach(pool)" 
            class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-150 text-sm"
          >
            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Remove
          </button>
        </li>
      </ul>
      
      <div class="mt-6">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Add to another pool:</h3>
        <PoolSelector 
          :pools="availablePools" 
          :question-id="question.id"
        />
      </div>
    </div>
    </div>
  </template>
  
  <script setup>
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    question: Object,
    availablePools: Array
  });
  
  const detach = (pool) => {
    if (confirm(`Are you sure you want to remove this question from the "${pool.name}" pool?`)) {
      router.delete(route('examiner.pools.detach-question', {
        pool: pool.id,
        question: props.question.id
      }), {
        preserveScroll: true
      });
    }
  };
  </script>

<style scoped>
/* Enhanced styling for the pool management component */
.bg-gray-50 {
    background-color: #f9fafb;
}

.bg-gray-100:hover {
    background-color: #f3f4f6;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .p-6 {
        padding: 1.5rem;
    }
    
    .space-y-2 > *:not(:last-child) {
        margin-bottom: 0.5rem;
    }
}
</style>