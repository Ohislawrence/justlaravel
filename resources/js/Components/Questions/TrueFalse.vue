<template>
  <div class="space-y-4">
    <div class="flex items-center space-x-4">
      <label class="inline-flex items-center">
        <input
          v-model="userAnswer"
          type="radio"
          :name="`question-${question.id}`"
          :value="true"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
        >
        <span class="ml-2 text-sm font-medium text-gray-700">True</span>
      </label>
      <label class="inline-flex items-center">
        <input
          v-model="userAnswer"
          type="radio"
          :name="`question-${question.id}`"
          :value="false"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
        >
        <span class="ml-2 text-sm font-medium text-gray-700">False</span>
      </label>
    </div>
    
    <div v-if="question.settings.show_feedback" class="mt-3 space-y-2">
      <div v-if="userAnswer === true && question.settings.true_feedback" class="p-2 bg-blue-50 rounded-md">
        <p class="text-sm text-blue-800">{{ question.settings.true_feedback }}</p>
      </div>
      <div v-if="userAnswer === false && question.settings.false_feedback" class="p-2 bg-blue-50 rounded-md">
        <p class="text-sm text-blue-800">{{ question.settings.false_feedback }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  question: Object,
  answer: Boolean
});

const emit = defineEmits(['update']);

const userAnswer = ref(props.answer);

watch(userAnswer, (newValue) => {
  emit('update', newValue);
});
</script>