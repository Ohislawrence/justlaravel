<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Respondent
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Device & Location
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Completion Date
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Time Spent
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Actions
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="attempt in attempts.data" :key="attempt.id">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ attempt.user?.name || attempt.guest_name || 'Anonymous' }}
            </div>
            <div class="text-sm text-gray-500">
              {{ attempt.user?.email || attempt.guest_email || 'No email' }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-900">
              <div class="flex items-center space-x-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                  {{ getDeviceIcon(attempt.device_type) }} {{ attempt.device_type }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                  {{ attempt.browser }}
                </span>
              </div>
              <div class="text-xs text-gray-500 mt-1">
                {{ attempt.city }}, {{ attempt.region }}, {{ attempt.country }}
              </div>
              <div class="text-xs text-gray-400">
                {{ attempt.platform }}
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ formatDate(attempt.completed_at) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ formatTime(attempt.time_spent) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  quiz: Object,
  attempts: Object
});

const getDeviceIcon = (deviceType) => {
  const icons = {
    'desktop': '🖥️',
    'mobile': '📱',
    'tablet': '📟'
  };
  return icons[deviceType] || '💻';
};

const formatDate = (dateString) => {
  if (!dateString) return 'Incomplete';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatTime = (seconds) => {
  if (!seconds) return 'N/A';
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes}m ${remainingSeconds}s`;
};
</script>