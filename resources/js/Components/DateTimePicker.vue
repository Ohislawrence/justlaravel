<script setup>
import { ref, computed, watch } from 'vue';
import { format, parseISO } from 'date-fns';

const props = defineProps({
  modelValue: [String, Date],
  id: String,
  label: String,
  required: Boolean,
  disabled: Boolean,
  minDate: [String, Date],
  maxDate: [String, Date],
});

const emit = defineEmits(['update:modelValue']);

const showPicker = ref(false);
const dateInput = ref(null);
const timeInput = ref(null);

// Parse the modelValue into date and time components
const parsedValue = computed(() => {
  if (!props.modelValue) return { date: '', time: '' };
  
  const date = props.modelValue instanceof Date 
    ? props.modelValue 
    : parseISO(props.modelValue);
  
  return {
    date: format(date, 'yyyy-MM-dd'),
    time: format(date, 'HH:mm'),
  };
});

// Combined date and time
const combinedDateTime = computed(() => {
  if (!parsedValue.value.date || !parsedValue.value.time) return null;
  
  const dateTime = new Date(`${parsedValue.value.date}T${parsedValue.value.time}`);
  return dateTime.toISOString();
});

// Watch for changes and emit updates
watch(combinedDateTime, (newValue) => {
  if (newValue) {
    emit('update:modelValue', newValue);
  }
});

// Close picker when clicking outside
const onClickOutside = (event) => {
  if (!event.target.closest('.datetime-picker-container')) {
    showPicker.value = false;
  }
};

// Set current date/time
const setCurrentDateTime = () => {
  const now = new Date();
  parsedValue.value = {
    date: format(now, 'yyyy-MM-dd'),
    time: format(now, 'HH:mm'),
  };
};

// Clear selection
const clearSelection = () => {
  parsedValue.value = { date: '', time: '' };
  emit('update:modelValue', null);
};
</script>

<template>
  <div class="datetime-picker-container relative">
    <InputLabel v-if="label" :for="id" :value="label" class="mb-1" />
    
    <div class="relative">
      <input
        :id="id"
        type="text"
        readonly
        :value="modelValue ? format(parseISO(modelValue), 'MMM d, yyyy h:mm a') : ''"
        @click="showPicker = !showPicker"
        :class="[
          'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
          'bg-white dark:bg-gray-800',
          disabled ? 'cursor-not-allowed bg-gray-100 dark:bg-gray-700' : 'cursor-pointer'
        ]"
        :disabled="disabled"
      />
      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
    </div>

    <div 
      v-if="showPicker"
      class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700"
    >
      <div class="p-4 space-y-4">
        <!-- Date Picker -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
          <input
            ref="dateInput"
            type="date"
            v-model="parsedValue.date"
            :min="minDate"
            :max="maxDate"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <!-- Time Picker -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
          <input
            ref="timeInput"
            type="time"
            v-model="parsedValue.time"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between pt-2">
          <button
            type="button"
            @click="setCurrentDateTime"
            class="text-xs text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
          >
            Now
          </button>
          <div class="space-x-2">
            <button
              type="button"
              @click="clearSelection"
              class="text-xs text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
            >
              Clear
            </button>
            <button
              type="button"
              @click="showPicker = false"
              class="text-xs text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              Done
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>