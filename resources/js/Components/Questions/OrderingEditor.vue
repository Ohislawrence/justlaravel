<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Items to Order</label>
      <p class="text-xs text-gray-500 mb-2">Enter the items in the correct order.</p>

      <draggable
        v-model="orderedItems"
        @end="onDragEnd"
        item-key="id"
        class="space-y-2"
      >
        <template #item="{ element, index }">
          <div class="flex items-center bg-gray-50 p-2 rounded-md shadow-sm">
            <!-- Handle for dragging -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-gray-400 cursor-move mr-3">
              <line x1="9" y1="18" x2="9" y2="6"></line>
              <line x1="15" y1="18" x2="15" y2="6"></line>
            </svg>

            <!-- Input field -->
            <input
              v-model="element.text"
              @input="updateData"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              placeholder="Enter an item"
            >

            <!-- Remove button -->
            <button
              @click="removeItem(index)"
              type="button"
              class="ml-2 inline-flex items-center p-1 border border-transparent rounded-full text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              <XMarkIcon class="h-4 w-4" />
            </button>
          </div>
        </template>
      </draggable>

      <button
        @click="addItem"
        type="button"
        class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <PlusIcon class="-ml-0.5 mr-2 h-4 w-4" />
        Add Item
      </button>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Preview - Correct Order</label>
      <ol class="mt-2 space-y-1 pl-5 list-decimal text-sm text-gray-700">
        <li v-for="(item, index) in orderedItems" :key="item.id">{{ item.text }}</li>
      </ol>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Settings</label>
      <div class="mt-2 space-y-2">
        <label class="inline-flex items-center">
          <input
            v-model="modelValue.settings.randomize"
            @change="updateData"
            type="checkbox"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          >
          <span class="ml-2 text-sm text-gray-600">Randomize item order when displayed</span>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import draggable from 'vuedraggable';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    default: () => ({
      options: [], // Array of { id, text }
      correct_answers: [], // Array of indexes representing correct order
      settings: {
        randomize: false,
      },
    }),
  },
});

const emit = defineEmits(['update:modelValue']);

// Computed to manage current items
const orderedItems = computed({
  get: () => props.modelValue.options || [],
  set: (value) => {
    const updated = { 
      ...props.modelValue, 
      options: value
    };
    // Update correct_answers as array of indexes based on current order
    updated.correct_answers = value
      .map((item, index) => item.text.trim() ? index : null)
      .filter(index => index !== null);
    
    emit('update:modelValue', updated);
  },
});

// Add new item
const addItem = () => {
  const newItem = {
    id: Date.now(), // unique ID for Vue key
    text: '',
  };
  
  const updatedOptions = [...(props.modelValue.options || []), newItem];
  const updated = { 
    ...props.modelValue, 
    options: updatedOptions,
    correct_answers: updatedOptions
      .map((item, index) => item.text.trim() ? index : null)
      .filter(index => index !== null)
  };
  
  emit('update:modelValue', updated);
};

// Remove item at index
const removeItem = (index) => {
  const updatedOptions = [...(props.modelValue.options || [])];
  updatedOptions.splice(index, 1);
  
  const updated = { 
    ...props.modelValue, 
    options: updatedOptions,
    correct_answers: updatedOptions
      .map((item, idx) => item.text.trim() ? idx : null)
      .filter(idx => idx !== null)
  };
  
  emit('update:modelValue', updated);
};

// Update data when input changes
const updateData = () => {
  const updated = {
    ...props.modelValue,
    correct_answers: (props.modelValue.options || [])
      .map((item, index) => item.text.trim() ? index : null)
      .filter(index => index !== null)
  };
  
  emit('update:modelValue', updated);
};

// On drag end, update correct answers based on new order
const onDragEnd = () => {
  const updated = {
    ...props.modelValue,
    correct_answers: (props.modelValue.options || [])
      .map((item, index) => item.text.trim() ? index : null)
      .filter(index => index !== null)
  };
  
  emit('update:modelValue', updated);
};
</script>