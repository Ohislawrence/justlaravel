<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0 rounded-md bg-blue-500 p-3">
          <component 
            :is="iconComponent" 
            class="h-6 w-6 text-white" 
            aria-hidden="true"
          />
        </div>
        <div class="ml-5 w-0 flex-1">
          <dt class="text-sm font-medium text-gray-500 truncate">
            {{ title || 'Unknown Metric' }}
          </dt>
          <dd class="flex items-baseline">
            <p class="text-2xl font-semibold text-gray-900">
              {{ formattedValue }}
            </p>
            <div v-if="percentageChange !== null && !isNaN(percentageChange)" 
                 class="ml-2 flex items-baseline text-sm font-semibold"
                 :class="percentageChange >= 0 ? 'text-green-600' : 'text-red-600'">
              <ArrowUpIcon v-if="percentageChange >= 0" class="h-4 w-4 flex-shrink-0 self-center text-green-500" aria-hidden="true" />
              <ArrowDownIcon v-else class="h-4 w-4 flex-shrink-0 self-center text-red-500" aria-hidden="true" />
              <span class="sr-only">{{ percentageChange >= 0 ? 'Increased' : 'Decreased' }} by</span>
              {{ Math.abs(percentageChange) }}%
            </div>
          </dd>
        </div>
      </div>
    </div>
    <div v-if="trendData && Array.isArray(trendData) && trendData.length > 0" class="bg-gray-50 px-4 py-4 sm:px-6">
      <div class="text-sm">
        <button 
          @click="handleViewAll"
          class="font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          View all<span class="sr-only"> {{ title }} stats</span>
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
    validator: (value) => ['users', 'chart-bar', 'check-circle', 'clock'].includes(value)
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

const iconComponent = computed(() => {
  const iconMap = {
    'users': UsersIcon,
    'chart-bar': ChartBarIcon,
    'check-circle': CheckCircleIcon,
    'clock': ClockIcon
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
    
    // Format percentages
    if (props.title?.toLowerCase().includes('%') || 
        props.title?.toLowerCase().includes('percent')) {
      return `${props.value}%`;
    }
    
    // Format large numbers with commas
    return props.value.toLocaleString();
  }
  
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
/* Custom transitions for hover effects */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Animation for the trend indicator */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>