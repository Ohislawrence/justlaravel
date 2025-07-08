<template>
  <div class="space-y-6">
    <!-- Instructions -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Instructions</label>
      <input
        :value="localValue.settings.instructions"
        @input="updateSetting('instructions', $event.target.value)"
        type="text"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        placeholder="e.g., 'Match the items on the left with the correct options on the right'"
      >
    </div>

    <!-- Match Items -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-medium text-gray-700">Match Items</h3>
        <button
          @click="addPair"
          type="button"
          class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-0.5 mr-1 h-4 w-4" />
          Add Pair
        </button>
      </div>

      <div v-for="(pair, index) in localValue.options" :key="index" class="flex items-center space-x-4">
        <div class="flex-1">
          <input
            :value="pair.left"
            @input="updatePair(index, 'left', $event.target.value)"
            type="text"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Left item"
          >
        </div>
        <div class="flex items-center">
          <ArrowRightIcon class="h-5 w-5 text-gray-400" />
        </div>
        <div class="flex-1">
          <input
            :value="pair.right"
            @input="updatePair(index, 'right', $event.target.value)"
            type="text"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Right item"
          >
        </div>
        <button
          @click="removePair(index)"
          type="button"
          class="ml-2 inline-flex items-center p-1 border border-transparent rounded-full text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          <XMarkIcon class="h-4 w-4" />
        </button>
      </div>
    </div>

    <!-- Distractors (Extra Options) -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Extra Options (Distractors)</label>
      <div class="mt-2 space-y-2">
        <div v-for="(distractor, index) in localValue.settings.distractors" :key="'distractor-'+index" class="flex items-center">
          <input
            :value="distractor"
            @input="updateDistractor(index, $event.target.value)"
            type="text"
            class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Extra option"
          >
          <button
            @click="removeDistractor(index)"
            type="button"
            class="ml-2 inline-flex items-center p-1 border border-transparent rounded-full text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            <XMarkIcon class="h-4 w-4" />
          </button>
        </div>
        <button
          @click="addDistractor"
          type="button"
          class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-0.5 mr-1 h-4 w-4" />
          Add Distractor
        </button>
      </div>
    </div>

    <!-- Settings -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Settings</label>
      <div class="mt-2 space-y-2">
        <label class="inline-flex items-center">
          <input
            :checked="localValue.settings.randomize_left"
            @change="updateSetting('randomize_left', $event.target.checked)"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Randomize left column</span>
        </label>
        <label class="inline-flex items-center">
          <input
            :checked="localValue.settings.randomize_right"
            @change="updateSetting('randomize_right', $event.target.checked)"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Randomize right column</span>
        </label>
        <label class="inline-flex items-center">
          <input
            :checked="localValue.settings.show_instructions"
            @change="updateSetting('show_instructions', $event.target.checked)"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Show instructions to students</span>
        </label>
      </div>
    </div>

    <!-- Scoring -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Points per correct match</label>
        <input
          :value="localValue.settings.points_per_match"
          @input="updateSetting('points_per_match', parseFloat($event.target.value) || 0)"
          type="number"
          min="0"
          step="0.5"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        >
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Deduction per wrong match</label>
        <input
          :value="localValue.settings.deduction_per_wrong"
          @input="updateSetting('deduction_per_wrong', parseFloat($event.target.value) || 0)"
          type="number"
          min="0"
          step="0.5"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        >
      </div>
    </div>

    <!-- Debug info (remove in production) -->
    <div class="mt-4 p-2 bg-gray-100 rounded text-xs">
      <strong>Debug:</strong><br>
      Pairs: {{ localValue.options }}<br>
      Distractors: {{ localValue.settings.distractors }}<br>
      Settings: {{ localValue.settings }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { PlusIcon, XMarkIcon, ArrowRightIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    default: () => ({
      options: [],
      settings: {
        instructions: '',
        distractors: [],
        randomize_left: false,
        randomize_right: false,
        show_instructions: true,
        points_per_match: 1,
        deduction_per_wrong: 0
      }
    })
  }
});

const emit = defineEmits(['update:modelValue']);

// Create a local computed value to work with
const localValue = computed({
  get() {
    // Ensure proper structure with defaults
    return {
      options: props.modelValue.options || [],
      settings: {
        instructions: '',
        distractors: [],
        randomize_left: false,
        randomize_right: false,
        show_instructions: true,
        points_per_match: 1,
        deduction_per_wrong: 0,
        ...props.modelValue.settings
      }
    };
  },
  set(value) {
    emit('update:modelValue', { ...props.modelValue, ...value });
  }
});

// Update a specific pair's property
const updatePair = (index, property, value) => {
  const newPairs = [...localValue.value.options];
  newPairs[index] = { ...newPairs[index], [property]: value };
  
  emit('update:modelValue', {
    ...props.modelValue,
    options: newPairs
  });
};

// Add a new pair
const addPair = () => {
  const newPairs = [...localValue.value.options, { left: '', right: '' }];
  
  emit('update:modelValue', {
    ...props.modelValue,
    options: newPairs
  });
};

// Remove pair by index
const removePair = (index) => {
  const newPairs = [...localValue.value.options];
  newPairs.splice(index, 1);
  
  emit('update:modelValue', {
    ...props.modelValue,
    options: newPairs
  });
};

// Update a specific distractor
const updateDistractor = (index, value) => {
  const newDistractors = [...localValue.value.settings.distractors];
  newDistractors[index] = value;
  
  emit('update:modelValue', {
    ...props.modelValue,
    settings: {
      ...localValue.value.settings,
      distractors: newDistractors
    }
  });
};

// Add distractor
const addDistractor = () => {
  const newDistractors = [...localValue.value.settings.distractors, ''];
  
  emit('update:modelValue', {
    ...props.modelValue,
    settings: {
      ...localValue.value.settings,
      distractors: newDistractors
    }
  });
};

// Remove distractor
const removeDistractor = (index) => {
  const newDistractors = [...localValue.value.settings.distractors];
  newDistractors.splice(index, 1);
  
  emit('update:modelValue', {
    ...props.modelValue,
    settings: {
      ...localValue.value.settings,
      distractors: newDistractors
    }
  });
};

// Update a setting
const updateSetting = (key, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    settings: {
      ...localValue.value.settings,
      [key]: value
    }
  });
};
</script>