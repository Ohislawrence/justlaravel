<template>
    <div class="space-y-6">
      <!-- Correct Answer Selection -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
        <div class="flex items-center space-x-4">
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.correct_answers"
              type="radio"
              :value="['true']"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
            >
            <span class="ml-2 text-sm text-gray-700">True</span>
          </label>
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.correct_answers"
              type="radio"
              :value="['false']"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
            >
            <span class="ml-2 text-sm text-gray-700">False</span>
          </label>
        </div>
      </div>
  
      <!-- Feedback Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback Messages</label>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Feedback when answered True</label>
            <input
              v-model="modelValue.settings.true_feedback"
              type="text"
              placeholder="Optional feedback message"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Feedback when answered False</label>
            <input
              v-model="modelValue.settings.false_feedback"
              type="text"
              placeholder="Optional feedback message"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
          </div>
        </div>
      </div>
  
      <!-- Additional Settings -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Settings</label>
        <div class="space-y-2">
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.settings.randomize"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <span class="ml-2 text-sm text-gray-600">Randomize True/False order</span>
          </label>
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.settings.show_feedback"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <span class="ml-2 text-sm text-gray-600">Show feedback after answering</span>
          </label>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    modelValue: {
      type: Object,
      required: true,
      default: () => ({
        type: 'true_false',
        correct_answers: ['true'], 
        settings: {
          randomize: false,
          show_feedback: true,
          true_feedback: '',
          false_feedback: ''
        }
      })
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  </script>