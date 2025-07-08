<template>
  <div class="space-y-3">
    <div class="space-y-2">
      <div 
        v-for="(option, index) in question.options" 
        :key="index"
        class="flex items-center"
      >
        <input
          :id="`option-${question.id}-${index}`"
          type="radio"
          :name="`question-${question.id}`"
          :value="option"
          :checked="isSelected(option)"
          @change="selectOption(option)"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
        />
        <label 
          :for="`option-${question.id}-${index}`" 
          class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer"
        >
          {{ option.text || option }}
        </label>
      </div>
    </div>
    
    <div v-if="!question.options || question.options.length === 0" class="text-red-500 text-sm">
      No options available for this question.
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  question: {
    type: Object,
    required: true
  },
  answer: {
    type: [String, Number, Object], // Allow for object values
    default: null
  }
});

const emit = defineEmits(['update']);

const isSelected = (option) => {
  // Compare the entire option object or primitive value
  if (typeof option === 'object' && typeof props.answer === 'object') {
    return JSON.stringify(option) === JSON.stringify(props.answer);
  }
  return props.answer === option;
};

const selectOption = (value) => {
  emit('update', value);
};
</script>