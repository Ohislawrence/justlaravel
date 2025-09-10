<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Options</label>
      <div v-for="(option, index) in form.options" :key="index" class="mt-2 flex items-start">
        <!-- Radio button for single correct answer -->
        <input
          v-if="!form.settings.multiple_selection"
          :checked="isCorrect(option)"
          @change="setCorrectAnswer(option)"
          type="radio"
          :name="`correct-answer-${uniqueId}`"
          class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 mt-1"
        >
        <!-- Checkbox for multiple correct answers -->
        <input
          v-else
          :checked="isCorrect(option)"
          @change="toggleCorrectAnswer(option, $event.target.checked)"
          type="checkbox"
          class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded mt-1"
        >
        <input
          :value="option"
          @input="updateOptionValue(index, $event.target.value)"
          type="text"
          class="ml-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          placeholder="Option text"
        >
        <button
          v-if="form.options.length > 1"
          @click="removeOption(index)"
          type="button"
          class="ml-2 inline-flex items-center p-1 border border-transparent rounded-full text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          <XMarkIcon class="h-4 w-4" />
        </button>
      </div>
      <button
        @click="addOption"
        type="button"
        class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <PlusIcon class="-ml-0.5 mr-2 h-4 w-4" />
        Add Option
      </button>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Settings</label>
      <div class="mt-2 space-y-2">
        <label class="inline-flex items-center">
          <input
            v-model="form.settings.randomize"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Randomize option order</span>
        </label>
        <label class="inline-flex items-center">
          <input
            v-model="form.settings.multiple_selection"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Allow multiple correct answers</span>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { ref, watch, computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    default: () => ({
      options: [''],
      correct_answers: [],
      settings: {
        randomize: false,
        multiple_selection: false
      }
    })
  }
});

const emit = defineEmits(['update:modelValue']);

const uniqueId = ref(Math.random().toString(36).substring(2));

// Use a computed property to work with the form object directly
const form = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  }
});

const isCorrect = (optionText) => {
  return Array.isArray(form.value.correct_answers) && form.value.correct_answers.includes(optionText);
};

const setCorrectAnswer = (optionText) => {
  if (form.value.settings.multiple_selection) {
    toggleCorrectAnswer(optionText, !isCorrect(optionText));
  } else {
    // Use $inertia.set to properly update the form data
    form.value.correct_answers = [optionText];
  }
};

const toggleCorrectAnswer = (optionText, isCorrect) => {
  let newCorrectAnswers = Array.isArray(form.value.correct_answers) 
    ? [...form.value.correct_answers] 
    : [];
  
  if (isCorrect) {
    if (!newCorrectAnswers.includes(optionText)) {
      newCorrectAnswers.push(optionText);
    }
  } else {
    newCorrectAnswers = newCorrectAnswers.filter(text => text !== optionText);
  }
  
  form.value.correct_answers = newCorrectAnswers;
};

const removeOption = (index) => {
  const optionToRemove = form.value.options[index];
  
  // Create new arrays to trigger reactivity
  const newOptions = [...form.value.options];
  newOptions.splice(index, 1);
  form.value.options = newOptions;
  
  // Remove the option from correct answers if it was selected
  if (isCorrect(optionToRemove)) {
    const newCorrectAnswers = form.value.correct_answers.filter(text => text !== optionToRemove);
    form.value.correct_answers = newCorrectAnswers;
  }
};

const addOption = () => {
  form.value.options = [...form.value.options, ''];
};

const updateOptionValue = (index, newValue) => {
  const oldValue = form.value.options[index];
  
  // Update the option text
  const newOptions = [...form.value.options];
  newOptions[index] = newValue;
  form.value.options = newOptions;
  
  // Update correct answers if this option was previously marked as correct
  if (isCorrect(oldValue)) {
    const newCorrectAnswers = form.value.correct_answers.map(text => 
      text === oldValue ? newValue : text
    );
    form.value.correct_answers = newCorrectAnswers;
  }
};

// Watch for multiple_selection changes to handle correct answers
watch(() => form.value.settings.multiple_selection, (newValue) => {
  if (!newValue && Array.isArray(form.value.correct_answers) && form.value.correct_answers.length > 1) {
    // If switching from multiple to single selection, keep only the first correct answer
    form.value.correct_answers = [form.value.correct_answers[0]];
  }
});
</script>