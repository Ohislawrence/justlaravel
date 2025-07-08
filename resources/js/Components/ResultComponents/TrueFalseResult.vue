<template>
    <div class="space-y-4">
      <div class="font-medium text-gray-700">Your Answer:</div>
      <div class="flex space-x-4">
        <div 
          class="p-3 rounded border flex-1 text-center"
          :class="{
            'border-green-500 bg-green-50': response === true && correctAnswer === true,
            'border-red-500 bg-red-50': response === true && correctAnswer !== true,
            'border-gray-200': response !== true
          }"
        >
          True
          <span v-if="response === true" class="ml-2">✓</span>
        </div>
        <div 
          class="p-3 rounded border flex-1 text-center"
          :class="{
            'border-green-500 bg-green-50': response === false && correctAnswer === false,
            'border-red-500 bg-red-50': response === false && correctAnswer !== false,
            'border-gray-200': response !== false
          }"
        >
          False
          <span v-if="response === false" class="ml-2">✓</span>
        </div>
      </div>
      <div class="text-sm text-gray-600 mt-2">
        Correct answer: {{ correctAnswer === true ? 'True' : 'False' }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  
  const props = defineProps({
    question: {
      type: Object,
      required: true,
      default: () => ({
        correct_answers: [true] // Default to true if not provided
      })
    },
    response: {
      type: Boolean,
      default: null
    }
  });
  
  const correctAnswer = computed(() => {
    // Safely get the first correct answer, default to true if not available
    return props.question.correct_answers?.[0] ?? true;
  });
  </script>