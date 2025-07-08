<template>
    <div class="space-y-6">
      <!-- Correct Answer Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Accepted Answers</label>
        <div class="space-y-2">
          <div v-for="(answer, index) in modelValue.correct_answers" :key="index" class="flex items-center">
            <input
              v-model="modelValue.correct_answers[index]"
              type="text"
              class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter an accepted answer"
            >
            <button
              @click="removeAnswer(index)"
              type="button"
              class="ml-2 inline-flex items-center p-1 border border-transparent rounded-full text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              <XMarkIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
        <button
          @click="addAnswer"
          type="button"
          class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-0.5 mr-2 h-4 w-4" />
          Add Another Accepted Answer
        </button>
      </div>
  
      <!-- Answer Settings -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Answer Settings</label>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Case Sensitivity</label>
            <select
              v-model="modelValue.settings.case_sensitive"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option :value="false">Case insensitive (default)</option>
              <option :value="true">Case sensitive</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Answer Matching</label>
            <select
              v-model="modelValue.settings.matching_type"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="exact">Exact match</option>
              <option value="contains">Contains text</option>
              <option value="regex">Regular expression</option>
            </select>
          </div>
          <div v-if="modelValue.settings.matching_type === 'regex'">
            <label class="block text-xs font-medium text-gray-500 mb-1">Regex Pattern</label>
            <input
              v-model="modelValue.settings.regex_pattern"
              type="text"
              placeholder="Enter regex pattern"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
            <p class="mt-1 text-xs text-gray-500">Example: ^[A-Z][a-z]+$ (matches capitalized words)</p>
          </div>
        </div>
      </div>
  
      <!-- Feedback Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
        <textarea
          v-model="modelValue.settings.feedback"
          rows="3"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          placeholder="Optional feedback to show after answering"
        ></textarea>
      </div>
    </div>
  </template>
  
  <script setup>
  import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid';
  import { watch } from 'vue'; 
  
  const props = defineProps({
    modelValue: {
      type: Object,
      required: true,
      default: () => ({
        type: 'short_answer',
        correct_answers: [],
        settings: {
          case_sensitive: false,
          matching_type: 'exact',
          regex_pattern: '',
          feedback: ''
        }
      })
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  // Initialize if empty
    watch(() => props.modelValue, (newVal) => {
    if (!Array.isArray(newVal.correct_answers)) {
        newVal.correct_answers = [];
    }
    }, { immediate: true });


  const addAnswer = () => {
  // Ensure correct_answers exists and is an array
  if (!Array.isArray(props.modelValue.correct_answers)) {
    props.modelValue.correct_answers = [];
  }
  props.modelValue.correct_answers.push('');
    };
  
  const removeAnswer = (index) => {
    props.modelValue.correct_answers.splice(index, 1);
  };
  </script>