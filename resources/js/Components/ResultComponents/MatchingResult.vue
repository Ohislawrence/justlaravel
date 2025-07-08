<template>
  <div class="space-y-4">
    <div class="font-medium text-gray-700">Your Matches:</div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Left column -->
      <div class="space-y-2">
        <h3 class="text-sm font-medium text-gray-700">Items</h3>
        <div 
          v-for="(item, index) in question.options" 
          :key="index"
          class="p-2 bg-gray-50 rounded border"
          :class="{
            'border-green-500': isCorrectMatch(index),
            'border-red-500': !isCorrectMatch(index)
          }"
        >
          {{ item.left }}
        </div>
      </div>

      <!-- Right column -->
      <div class="space-y-2">
        <h3 class="text-sm font-medium text-gray-700">Your Matches</h3>
        <div 
          v-for="(item, index) in question.options" 
          :key="index"
          class="p-2 bg-gray-50 rounded border"
          :class="{
            'border-green-500': isCorrectMatch(index),
            'border-red-500': !isCorrectMatch(index)
          }"
        >
          {{ getUserMatch(index) }}
        </div>
      </div>
    </div>
    
    <div class="mt-4">
      <div class="font-medium text-gray-700 mb-2">Correct Matches:</div>
      <ul class="space-y-2">
        <li 
          v-for="(pair, index) in question.options" 
          :key="index"
          class="text-sm"
        >
          <span class="font-medium">{{ pair.left }}</span> → 
          <span class="font-medium">{{ pair.right }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  question: {
    type: Object,
    required: true,
    default: () => ({
      options: [],
      correct_answers: []
    })
  },
  response: {
    type: [Array, Object],
    default: () => []
  }
});

// Convert response to consistent array format
const normalizedResponse = computed(() => {
  if (Array.isArray(props.response)) {
    return props.response;
  }
  if (typeof props.response === 'object' && props.response !== null) {
    return Object.entries(props.response).map(([left, right]) => ({
      left,
      right
    }));
  }
  return [];
});

const getUserMatch = (leftIndex) => {
  const leftItem = props.question.options[leftIndex]?.left;
  if (!leftItem) return "(No match)";
  
  const match = normalizedResponse.value.find(m => m.left === leftItem);
  if (!match) return "(No match)";
  
  return match.right || "(No match)";
};

const isCorrectMatch = (leftIndex) => {
  const leftItem = props.question.options[leftIndex]?.left;
  if (!leftItem) return false;
  
  const correctRight = props.question.options[leftIndex]?.right;
  if (!correctRight) return false;
  
  const userMatch = normalizedResponse.value.find(m => m.left === leftItem);
  if (!userMatch) return false;
  
  return userMatch.right === correctRight;
};
</script>