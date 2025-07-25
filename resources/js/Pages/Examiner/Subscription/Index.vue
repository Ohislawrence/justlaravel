<template>
    <AppLayout title="Subscription Plans">
        <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Choose a Subscription Plan
        </h2>
        <div v-if="activeSubscription" class="text-sm text-green-600">
          Current Plan: {{ activeSubscription.plan.name }}
          <span v-if="activeSubscription.plan.slug !== 'free-tier'">
            (Expires {{ formatDate(activeSubscription.ends_at) }})
          </span>
          <span v-else>(Limited)</span>
        </div>
      </div>
    </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <PlanCard 
              v-for="plan in plans"
              :key="plan.id"
              :plan="plan"
              :current-subscription="activeSubscription"
            />
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PlanCard from '@/Components/Subscription/PlanCard.vue';

const props = defineProps({
  plans: Array,
  organization: Object
});

const activeSubscription = computed(() => {
  return props.organization.active_subscription || props.organization.current_subscription;
});


const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};
</script>