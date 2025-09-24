<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Respondent
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
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ formatDate(attempt.completed_at) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ formatTime(attempt.time_spent) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            Action
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200" v-if="attempts.links && attempts.links.length > 3">
      <div class="flex justify-between items-center">
        <div class="text-sm text-gray-700">
          Showing {{ attempts.from }} to {{ attempts.to }} of {{ attempts.total }} results
        </div>
        <div class="flex space-x-1">
          <Link 
            v-for="link in attempts.links" 
            :key="link.label"
            :href="link.url || '#'"
            class="px-3 py-1 rounded-md text-sm"
            :class="{
              'bg-green-100 text-green-700': link.active,
              'text-gray-500 hover:text-gray-700': !link.active && link.url,
              'text-gray-300 cursor-not-allowed': !link.url
            }"
            v-html="link.label"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  quiz: Object,
  attempts: Object
});

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