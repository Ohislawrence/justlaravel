<template>
    <span class="inline-flex flex-col ml-1 -space-y-1.5">
      <!-- Up arrow (ascending) -->
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        class="h-3 w-3" 
        viewBox="0 0 20 20" 
        fill="currentColor"
        :class="{
          'text-gray-400': direction !== 'asc',
          'text-indigo-600': direction === 'asc'
        }"
      >
        <path 
          fill-rule="evenodd" 
          d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" 
          clip-rule="evenodd" 
        />
      </svg>
      
      <!-- Down arrow (descending) -->
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        class="h-3 w-3" 
        viewBox="0 0 20 20" 
        fill="currentColor"
        :class="{
          'text-gray-400': direction !== 'desc',
          'text-indigo-600': direction === 'desc'
        }"
      >
        <path 
          fill-rule="evenodd" 
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" 
          clip-rule="evenodd" 
        />
      </svg>
    </span>
  </template>
  
  <script setup>
  defineProps({
    direction: {
      type: String,
      default: null,
      validator: (value) => [null, 'asc', 'desc'].includes(value)
    }
  });
  </script>
  
  <style scoped>
  /* No additional styles needed - using Tailwind classes */
  </style>