<template>
  <div class="space-y-4">
    <div v-if="loading" class="text-center py-4 text-gray-500">
      Loading results...
    </div>
    
    <div v-else>
      <div v-if="error" class="text-red-500 p-4 border border-red-200 rounded mb-4">
        Error loading results: {{ error }}
      </div>

      <div class="font-medium text-gray-700">Your Answers:</div>
      
      <div class="whitespace-pre-wrap">
        <template v-for="(part, index) in splitQuestion" :key="index">
          <template v-if="part.isBlank">
            <span 
              class="inline-block mx-1 px-2 py-1 border-b-2"
              :class="{
                'border-green-500 text-green-700 bg-green-50': isCorrectBlank(part.blankIndex),
                'border-red-500 text-red-700 bg-red-50': !isCorrectBlank(part.blankIndex) && getUserAnswer(part.blankIndex),
                'border-gray-300': !getUserAnswer(part.blankIndex)
              }"
            >
              {{ getUserAnswer(part.blankIndex) || "(blank)" }}
              <span v-if="isCorrectBlank(part.blankIndex)" class="ml-1">✓</span>
              <span v-else-if="getUserAnswer(part.blankIndex)" class="ml-1">✗</span>
            </span>
          </template>
          <template v-else>
            {{ part.text }}
          </template>
        </template>
      </div>
      
      <div v-if="question.correct_answers?.length > 0" class="mt-4">
        <div class="font-medium text-gray-700 mb-2">Correct Answers:</div>
        <ul class="list-disc pl-5 space-y-1">
          <li v-for="(answer, index) in question.correct_answers" :key="index">
            Blank {{ index + 1 }}: <span class="font-medium">{{ answer }}</span>
          </li>
        </ul>
      </div>

      <div v-if="question.settings?.case_sensitive" class="text-sm text-gray-600 mt-2">
        Note: Answers were checked case-sensitively
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
      question: '',
      correct_answers: [],
      settings: {}
    })
  },
  response: {
    type: Array,
    default: () => []
  }
});

const loading = ref(false);
const error = ref(null);

const splitQuestion = computed(() => {
  const parts = [];
  const text = props.question.question || '';
  const regex = /(\[blank\])/gi;
  let lastIndex = 0;
  let blankCount = 0;
  
  let match;
  while ((match = regex.exec(text)) !== null) {
    if (match.index > lastIndex) {
      parts.push({
        text: text.substring(lastIndex, match.index),
        isBlank: false
      });
    }
    
    parts.push({
      text: match[0],
      isBlank: true,
      blankIndex: blankCount++
    });
    
    lastIndex = regex.lastIndex;
  }
  
  if (lastIndex < text.length) {
    parts.push({
      text: text.substring(lastIndex),
      isBlank: false
    });
  }
  
  return parts;
});

const getUserAnswer = (blankIndex) => {
  return props.response?.[blankIndex];
};

const isCorrectBlank = (blankIndex) => {
  if (!props.response || !props.question.correct_answers) return false;
  
  const userAnswer = props.response[blankIndex];
  const correctAnswer = props.question.correct_answers[blankIndex];
  
  if (!userAnswer) return false;
  
  if (props.question.settings?.case_sensitive) {
    return userAnswer === correctAnswer;
  }
  return userAnswer.toLowerCase() === correctAnswer?.toLowerCase();
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>