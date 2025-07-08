<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Your Answer</label>
      <input
        v-model="userAnswer"
        type="text"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        placeholder="Type your answer here"
      >
    </div>
    
    <div v-if="question.settings?.feedback" class="p-3 bg-gray-50 rounded-md">
      <p class="text-sm text-gray-600">{{ question.settings.feedback }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  question: {
    type: Object,
    required: true,
    default: () => ({
      settings: {
        feedback: ''
      },
      correct_answers: []
    })
  },
  answer: String
});

const emit = defineEmits(['update']);

const userAnswer = ref(props.answer || '');

watch(userAnswer, (newValue) => {
  emit('update', newValue);
});
</script>