<template>
  <div class="space-y-4">
    <div v-if="question.settings?.instructions" class="text-sm text-gray-600 mb-3">
      {{ question.settings.instructions }}
    </div>

    <draggable
      v-model="userOrder"
      item-key="id"
      class="space-y-2"
      @end="updateAnswers"
    >
      <template #item="{ element, index }">
        <div class="flex items-center bg-gray-50 p-3 rounded-md shadow-sm border border-gray-200">
          <!-- Drag handle -->
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-gray-400 cursor-move mr-3">
            <line x1="9" y1="18" x2="9" y2="6"></line>
            <line x1="15" y1="18" x2="15" y2="6"></line>
          </svg>

          <!-- Item text -->
          <span class="flex-1">{{ element.text }}</span>

          <!-- Position indicator -->
          <span class="ml-3 text-sm text-gray-500">#{{ index + 1 }}</span>
        </div>
      </template>
    </draggable>

    <div class="mt-4">
      <h4 class="text-sm font-medium text-gray-700">Your current order:</h4>
      <ol class="list-decimal pl-5 mt-1 space-y-1">
        <li v-for="(item, index) in userOrder" :key="`current-${index}`">
          {{ item.text }}
        </li>
      </ol>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import draggable from 'vuedraggable';

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

// Initialize user's order
const userOrder = ref([]);

// Set up initial order
watch(() => props.question, (newQuestion) => {
  if (newQuestion.options) {
    // If we have an existing answer, use that order
    if (props.answer && props.answer.length > 0) {
      userOrder.value = props.answer.map(index => newQuestion.options[index]);
    } 
    // Otherwise use the original order (or randomized if setting is enabled)
    else {
      const items = [...newQuestion.options];
      if (newQuestion.settings?.randomize) {
        items.sort(() => Math.random() - 0.5);
      }
      userOrder.value = items;
    }
  }
}, { immediate: true });

// Update answers when order changes
const updateAnswers = () => {
  if (!props.question.options) return;
  
  // Map the current order back to original indices
  const answerIndices = userOrder.value.map(item => 
    props.question.options.findIndex(opt => opt.id === item.id)
  ).filter(index => index !== -1);
  
  emit('update', answerIndices);
};
</script>