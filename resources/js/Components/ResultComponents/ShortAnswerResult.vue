<template>
    <div class="space-y-4">
      <div>
        <div class="font-medium text-gray-700 mb-1">Your Answer:</div>
        <div class="p-3 bg-gray-50 rounded border border-gray-200">
          {{ response || "(No answer provided)" }}
        </div>
      </div>
      
      <div v-if="question.correct_answers?.length > 0">
        <div class="font-medium text-gray-700 mb-1">Accepted Answers:</div>
        <ul class="list-disc pl-5 space-y-1">
          <li v-for="(answer, index) in question.correct_answers" :key="index">
            {{ answer }}
          </li>
        </ul>
      </div>
      
      <div v-if="question.settings?.case_sensitive" class="text-sm text-gray-600">
        Note: Answers were checked case-sensitively
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    question: Object,
    response: String
  });
  </script>