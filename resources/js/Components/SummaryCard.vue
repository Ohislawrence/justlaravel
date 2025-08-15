
<template>
  <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 transition-all duration-200 hover:shadow-md">
    <div class="px-3 py-4 sm:p-4">
      <div class="flex items-start"> <!-- Changed to items-start for better alignment -->
        <div class="flex-shrink-0 rounded-lg bg-blue-100 p-2.5"> <!-- Smaller, rounded-lg icon container -->
          <component 
            :is="iconComponent" 
            class="h-5 w-5 text-blue-600" 
            aria-hidden="true"
          />
        </div>
        <div class="ml-4 w-0 flex-1"> <!-- Adjusted margin -->
          <dt class="text-xs font-medium text-gray-500 truncate"> <!-- Smaller title font -->
            {{ title || 'Unknown Metric' }}
          </dt>
          <dd class="mt-1 flex items-baseline justify-between"> <!-- Flex between for value and trend -->
            <p class="text-xl font-semibold text-gray-900"> <!-- Smaller value font -->
              {{ formattedValue }}
            </p>
            <div 
              v-if="percentageChange !== null && !isNaN(percentageChange)" 
              class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full" <!-- Badge-style trend -->
              :class="percentageChange >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
            >
              <ArrowUpIcon v-if="percentageChange >= 0" class="h-3.5 w-3.5 flex-shrink-0 mr-1" aria-hidden="true" />
              <ArrowDownIcon v-else class="h-3.5 w-3.5 flex-shrink-0 mr-1" aria-hidden="true" />
              <span>{{ Math.abs(percentageChange) }}%</span>
            </div>
          </dd>
        </div>
      </div>
    </div>
    <!-- Optional Description Slot -->
    <div v-if="$slots.description" class="px-3 pb-3 sm:px-4 sm:pb-4 -mt-2"> 
       <slot name="description" />
    </div>
    <div 
      v-if="trendData && Array.isArray(trendData) && trendData.length > 0" 
      class="bg-gray-50 px-3 py-2.5 sm:px-4 sm:py-3 text-center border-t border-gray-100" <!-- Lighter footer, adjusted padding -->
    >
      <div class="text-xs"> <!-- Smaller footer text -->
        <button 
          @click="handleViewAll"
          class="font-medium text-blue-600 hover:text-blue-800 transition-colors duration-150 focus:outline-none focus:underline"
        >
          View Report<span class="sr-only"> for {{ title }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  CheckCircleIcon,
  UsersIcon,
  ChartBarIcon,
  ClockIcon,
  DocumentTextIcon, // Added document-text
  QuestionMarkCircleIcon, // Added question-mark-circle
  CalendarIcon, // Added calendar
  CreditCardIcon, // Added credit-card
  ArrowUpIcon,
  ArrowDownIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: String,
    default: 'chart-bar',
    // Updated validator to include new icons
    validator: (value) => [
      'users', 'chart-bar', 'check-circle', 'clock', 
      'document-text', 'question-mark-circle', 'calendar', 'credit-card'
    ].includes(value)
  },
  percentageChange: {
    type: Number,
    default: null
  },
  trendData: {
    type: Array,
    default: null
  }
});

const emit = defineEmits(['view-all']);

// Updated icon map
const iconComponent = computed(() => {
  const iconMap = {
    'users': UsersIcon,
    'chart-bar': ChartBarIcon,
    'check-circle': CheckCircleIcon,
    'clock': ClockIcon,
    'document-text': DocumentTextIcon,
    'question-mark-circle': QuestionMarkCircleIcon,
    'calendar': CalendarIcon,
    'credit-card': CreditCardIcon,
  };
  
  return iconMap[props.icon] || ChartBarIcon;
});

const formattedValue = computed(() => {
  // Handle null/undefined values
  if (props.value === null || props.value === undefined) {
    return '0';
  }
  
  if (typeof props.value === 'number') {
    // Handle NaN values
    if (isNaN(props.value)) {
      return '0';
    }
    
    // Format percentages (if the value itself is a percentage, not the change)
    // This assumes the value passed in is already the percentage number (e.g., 75 for 75%)
    // If your original logic was different, adjust accordingly.
    // The title check might be redundant now if value is always the number.
    // if (props.title?.toLowerCase().includes('%') || 
    //     props.title?.toLowerCase().includes('percent')) {
    //   return `${props.value}%`;
    // }
    
    // Format large numbers with commas
    return props.value.toLocaleString();
  }
  
  // If it's already a string with %, just return it
  return String(props.value);
});

const handleViewAll = () => {
  emit('view-all', {
    title: props.title,
    trendData: props.trendData
  });
};
</script>

<style scoped>
/* The scoped styles from the original are mostly handled by Tailwind classes now */
/* You can add custom transitions or animations here if needed */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms; /* Slightly longer for smoothness */
}

/* If bg-gray-25 is not available, simulate it */
.bg-gray-50 {
  background-color: #f9fafb; /* Light gray, adjust if needed */
}
</style>