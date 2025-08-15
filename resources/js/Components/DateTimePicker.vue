<script setup>
import { ref, computed, watch } from 'vue';
import { format, parseISO, isValid } from 'date-fns';
import { formatInTimeZone } from 'date-fns-tz';

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
const dateInput = ref('');
const timeInput = ref('');

// Handle initial value
const initializeValues = () => {
  if (!props.modelValue) {
    dateInput.value = '';
    timeInput.value = '';
    return;
  }

  try {
    const date = props.modelValue instanceof Date 
      ? props.modelValue 
      : parseISO(props.modelValue);
    
    if (isValid(date)) {
      dateInput.value = format(date, 'yyyy-MM-dd');
      timeInput.value = format(date, 'HH:mm');
    }
  } catch (e) {
    console.error('Date parsing error:', e);
    dateInput.value = '';
    timeInput.value = '';
  }
};

// Initialize on mount
initializeValues();

// Watch for external modelValue changes
watch(() => props.modelValue, initializeValues);

// Combine date and time and emit updates
watch([dateInput, timeInput], ([date, time]) => {
  if (date && time) {
    try {
      const dateTime = new Date(`${date}T${time}`);
      if (isValid(dateTime)) {
        // Format as "YYYY-MM-DD HH:mm:ss" for Laravel/MySQL
        const formatted = format(dateTime, 'yyyy-MM-dd HH:mm:ss');
        emit('update:modelValue', formatted);
      }
    } catch (e) {
      console.error('Date combination error:', e);
    }
  } else {
    emit('update:modelValue', null);
  }
});

const formatDisplayValue = (value) => {
  if (!value) return '';
  try {
    const date = value instanceof Date ? value : parseISO(value);
    return isValid(date) ? format(date, 'MMM d, yyyy h:mm a') : '';
  } catch {
    return '';
  }
};

const setCurrentDateTime = () => {
  const now = new Date();
  dateInput.value = format(now, 'yyyy-MM-dd');
  timeInput.value = format(now, 'HH:mm');
};

const clearSelection = () => {
  dateInput.value = '';
  timeInput.value = '';
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
        :value="formatDisplayValue(modelValue)"
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
            type="date"
            v-model="dateInput"
            :min="minDate"
            :max="maxDate"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
        </div>

        <!-- Time Picker -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
          <input
            type="time"
            v-model="timeInput"
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