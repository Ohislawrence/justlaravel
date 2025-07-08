<template>
  <div class="space-y-4">
    <div v-if="question.settings?.instructions" class="text-sm text-gray-600">
      {{ question.settings.instructions }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Left column -->
      <div class="space-y-2">
        <h3 class="text-sm font-medium text-gray-700">Items</h3>
        <div 
          v-for="(item, index) in shuffledLeftItems" 
          :key="`left-${index}`"
          class="p-2 bg-gray-50 rounded border border-gray-200"
        >
          {{ item }}
        </div>
      </div>

      <!-- Right column -->
      <div class="space-y-2">
        <h3 class="text-sm font-medium text-gray-700">Matches</h3>
        <select
          v-for="(item, index) in shuffledLeftItems"
          :key="`select-${index}`"
          v-model="userAnswers[index]"
          @change="updateMatchingAnswers"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        >
          <option value="">Select match</option>
          <option 
            v-for="(option, optIndex) in shuffledRightOptions" 
            :key="`option-${optIndex}`"
            :value="optIndex"
          >
            {{ option }}
          </option>
        </select>
      </div>
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

// Initialize user answers
const userAnswers = ref(new Array(props.question.options?.length || 0).fill(''));

// Shuffle items if needed
const shuffledLeftItems = computed(() => {
  const items = props.question.options?.map(opt => opt.left) || [];
  if (props.question.settings?.randomize_left) {
    return [...items].sort(() => Math.random() - 0.5);
  }
  return items;
});

const shuffledRightOptions = computed(() => {
  const items = props.question.options?.map(opt => opt.right) || [];
  const distractors = props.question.settings?.distractors || [];
  const allOptions = [...items, ...distractors];
  
  if (props.question.settings?.randomize_right) {
    return [...allOptions].sort(() => Math.random() - 0.5);
  }
  return allOptions;
});

// Update answers when user selects
const updateMatchingAnswers = () => {
  const formattedAnswers = {};
  
  userAnswers.value.forEach((rightIndex, leftIndex) => {
    if (rightIndex !== '') {
      const leftItem = shuffledLeftItems.value[leftIndex];
      const rightItem = shuffledRightOptions.value[rightIndex];
      formattedAnswers[leftItem] = rightItem;
    }
  });
  
  emit('update', formattedAnswers);
};

// Update the watch for initial answer population
watch(() => props.answer, (newAnswer) => {
  if (newAnswer && typeof newAnswer === 'object' && !Array.isArray(newAnswer)) {
    // Clear previous answers
    userAnswers.value = new Array(shuffledLeftItems.value.length).fill('');
    
    // Map the answer object back to indices
    Object.entries(newAnswer).forEach(([leftItem, rightItem]) => {
      const leftIndex = shuffledLeftItems.value.indexOf(leftItem);
      const rightIndex = shuffledRightOptions.value.indexOf(rightItem);
      
      if (leftIndex !== -1 && rightIndex !== -1) {
        userAnswers.value[leftIndex] = rightIndex.toString();
      }
    });
  }
}, { immediate: true });
</script>