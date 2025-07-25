<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Options</label>
      <div v-for="(option, index) in localValue.options" :key="index" class="mt-2 flex items-start">
        <!-- Radio button for single correct answer -->
        <input
          v-if="!localValue.settings.multiple_selection"
          :checked="localValue.correct_answers.includes(index)"
          @change="updateSingleCorrectAnswer(index)"
          type="radio"
          :name="`correct-answer-${Math.random()}`"
          class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
        >
        <!-- Checkbox for multiple correct answers -->
        <input
          v-else
          :checked="localValue.correct_answers.includes(index)"
          @change="updateMultipleCorrectAnswers(index, $event.target.checked)"
          type="checkbox"
          class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
        >
        <input
          :value="option"
          @input="updateOption(index, $event.target.value)"
          type="text"
          class="ml-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          placeholder="Option text"
        >
        <button
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
            :checked="localValue.settings.randomize"
            @change="updateSetting('randomize', $event.target.checked)"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Randomize option order</span>
        </label>
        <label class="inline-flex items-center">
          <input
            :checked="localValue.settings.multiple_selection"
            @change="toggleMultipleSelection($event.target.checked)"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Allow multiple correct answers</span>
        </label>
      </div>
    </div>

    <!-- Debug info (remove in production) -->
    <div class="mt-4 p-2 bg-gray-100 rounded text-xs">
      <strong>Debug:</strong><br>
      Options: {{ localValue.options }}<br>
      Correct Answers: {{ localValue.correct_answers }}<br>
      Multiple Selection: {{ localValue.settings.multiple_selection }}
    </div>
  </div>
</template>

<script setup>
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    default: () => ({
      options: [],
      correct_answers: [],
      settings: {
        randomize: false,
        multiple_selection: false
      }
    })
  }
});

const emit = defineEmits(['update:modelValue']);

const localValue = computed({
  get() {
    // Ensure proper structure
    return {
      options: props.modelValue.options || [],
      correct_answers: props.modelValue.correct_answers || [],
      settings: {
        randomize: props.modelValue.settings?.randomize || false,
        multiple_selection: props.modelValue.settings?.multiple_selection || false,
        ...props.modelValue.settings
      }
    };
  },
  set(value) {
    emit('update:modelValue', { ...props.modelValue, ...value });
  }
});

const updateSingleCorrectAnswer = (index) => {
  const selectedOption = localValue.value.options[index];
  emit('update:modelValue', {
    ...props.modelValue,
    correct_answers: [selectedOption] // Store the option text/value instead of index
  });
};

const updateMultipleCorrectAnswers = (index, checked) => {
  const selectedOption = localValue.value.options[index];
  let newCorrectAnswers = Array.isArray(localValue.value.correct_answers) 
    ? [...localValue.value.correct_answers] 
    : [];
  
  if (checked) {
    if (!newCorrectAnswers.includes(selectedOption)) {
      newCorrectAnswers.push(selectedOption);
    }
  } else {
    newCorrectAnswers = newCorrectAnswers.filter(ans => ans !== selectedOption);
  }
  
  emit('update:modelValue', {
    ...props.modelValue,
    correct_answers: newCorrectAnswers
  });
};

const removeOption = (index) => {
  const newOptions = [...localValue.value.options];
  const removedOption = newOptions[index];
  newOptions.splice(index, 1);
  
  // Remove the deleted option from correct answers if it was selected
  let newCorrectAnswers = Array.isArray(localValue.value.correct_answers)
    ? [...localValue.value.correct_answers]
    : [];
    
  newCorrectAnswers = newCorrectAnswers.filter(ans => ans !== removedOption);
  
  emit('update:modelValue', {
    ...props.modelValue,
    options: newOptions,
    correct_answers: newCorrectAnswers
  });
};

const addOption = () => {
  const newOptions = [...localValue.value.options, ''];
  
  emit('update:modelValue', {
    ...props.modelValue,
    options: newOptions
  });
};


const updateSetting = (key, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    settings: {
      ...localValue.value.settings,
      [key]: value
    }
  });
};

const updateOption = (index, value) => {
  const newOptions = [...localValue.value.options];
  newOptions[index] = value;
  
  emit('update:modelValue', {
    ...props.modelValue,
    options: newOptions
  });
};

const toggleMultipleSelection = (isMultiple) => {
  // Always keep correct_answers as an array
  let newCorrectAnswers = Array.isArray(localValue.value.correct_answers)
    ? [...localValue.value.correct_answers]
    : [];
  
  if (!isMultiple && newCorrectAnswers.length > 1) {
    // For single selection, keep only the first selected answer
    newCorrectAnswers = newCorrectAnswers.slice(0, 1);
  }
  
  emit('update:modelValue', {
    ...props.modelValue,
    correct_answers: newCorrectAnswers,
    settings: {
      ...localValue.value.settings,
      multiple_selection: isMultiple
    }
  });
};
</script>