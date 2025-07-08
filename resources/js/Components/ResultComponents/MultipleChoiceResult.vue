<template>
  <div class="space-y-4">
    <div v-if="loading" class="text-center py-4 text-gray-500">
      Loading results...
    </div>

    <div v-else>
      <div v-if="error" class="text-red-500 p-4 border border-red-200 rounded mb-4">
        Error loading results: {{ error }}
      </div>

      <div class="font-medium text-gray-700 mb-2">Your Answer:</div>
      <div class="space-y-2">
        <div 
          v-for="(option, index) in question.options" 
          :key="index"
          class="flex items-center p-3 rounded border transition-colors"
          :class="{
            'border-green-500 bg-green-50': isSelected(index) && isCorrectOption(index),
            'border-red-500 bg-red-50': isSelected(index) && !isCorrectOption(index),
            'border-green-300 bg-green-50': !isSelected(index) && isCorrectOption(index),
            'border-gray-200': !isSelected(index) && !isCorrectOption(index)
          }"
        >
          <!-- Selection indicator -->
          <div 
            class="flex-shrink-0 h-5 w-5 rounded-full border flex items-center justify-center mr-3"
            :class="{
              'bg-green-500 border-green-500 text-white': isSelected(index) && isCorrectOption(index),
              'bg-red-500 border-red-500 text-white': isSelected(index) && !isCorrectOption(index),
              'border-gray-300': !isSelected(index)
            }"
          >
            <span v-if="isSelected(index)" class="text-xs">✓</span>
          </div>

          <!-- Option text -->
          <div class="flex-1">
            {{ getOptionText(option) }}
          </div>

          <!-- Correct/incorrect indicator -->
          <div v-if="isCorrectOption(index)" class="ml-2 text-green-600">
            ✓ Correct answer
          </div>
          <div v-else-if="isSelected(index)" class="ml-2 text-red-600">
            ✗ Incorrect
          </div>
        </div>
      </div>

      <div v-if="showExplanation" class="mt-4 p-3 bg-blue-50 rounded border border-blue-200">
        <div class="font-medium text-blue-700 mb-1">Explanation:</div>
        <div class="text-sm text-blue-600">{{ question.explanation || 'No explanation provided' }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  question: {
    type: Object,
    required: true,
    default: () => ({
      options: [],
      correct_answers: [],
      explanation: '',
      settings: {}
    })
  },
  response: {
    type: [Array, Number, String, Object],
    default: null
  },
  showExplanation: {
    type: Boolean,
    default: true
  }
});

const loading = ref(false);
const error = ref(null);

const getOptionValue = (option) => {
  if (typeof option === 'string') return option;
  return option.value || option.text || option.id || JSON.stringify(option);
};

const isSelected = (index) => {
  if (props.response === null || props.response === undefined) return false;
  
  const optionValue = getOptionValue(props.question.options[index]);
  
  if (Array.isArray(props.response)) {
    return props.response.some(resp => getOptionValue(resp) === optionValue);
  }
  return getOptionValue(props.response) === optionValue;
};

const isCorrectOption = (index) => {
  if (!props.question.correct_answers) return false;
  
  const optionValue = getOptionValue(props.question.options[index]);
  
  if (Array.isArray(props.question.correct_answers)) {
    return props.question.correct_answers.some(ans => getOptionValue(ans) === optionValue);
  }
  return getOptionValue(props.question.correct_answers) === optionValue;
};

const getOptionText = (option) => {
  if (typeof option === 'string') return option;
  return option.text || option.label || option.value || JSON.stringify(option);
};
</script>

<style scoped>
/* Smooth transitions for visual feedback */
.border {
  transition: border-color 0.2s ease, background-color 0.2s ease;
}
</style>