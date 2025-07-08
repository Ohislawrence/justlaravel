<template>
  <div class="space-y-4">
    <!-- Display question text with blanks as inputs -->
    <div class="whitespace-pre-wrap">
      <template v-for="(part, index) in splitQuestion" :key="index">
        <template v-if="part.isBlank">
          <input
            v-model="blankAnswers[part.blankIndex]"
            @input="updateAnswers"
            type="text"
            class="inline-block mx-1 px-2 py-1 border-b-2 border-gray-300 focus:border-blue-500 focus:outline-none"
            :placeholder="`Blank ${part.blankIndex + 1}`"
          />
        </template>
        <template v-else>
          {{ part.text }}
        </template>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  question: {
    type: Object,
    required: true
  },
  answer: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update']);

// Parse question text into parts (text or blank)
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

// Track blank answers
const blankAnswers = ref([]);

// Initialize blank answers
watch(() => props.question, (newQuestion) => {
  const blankCount = (newQuestion.question.match(/\[blank\]/gi) || []).length;
  blankAnswers.value = new Array(blankCount).fill('');
  
  // Fill in existing answers if available
  if (props.answer && props.answer.length > 0) {
    props.answer.forEach((ans, idx) => {
      if (idx < blankAnswers.value.length) {
        blankAnswers.value[idx] = ans;
      }
    });
  }
}, { immediate: true });

// Update answers when blanks are filled
const updateAnswers = () => {
  emit('update', [...blankAnswers.value]);
};
</script>