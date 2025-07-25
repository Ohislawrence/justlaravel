<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden border" :class="{
      'border-blue-500 ring-2 ring-blue-200': isCurrentPlan
    }">
      <div class="p-6">
        <div class="flex justify-between items-start">
          <h3 class="text-2xl font-bold">{{ plan.name }}</h3>
          <span v-if="isCurrentPlan" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
            Current Plan
          </span>
        </div>
        
        <div class="mt-4">
          <div class="text-center">
            <span class="text-4xl font-bold">₦{{ monthlyPrice }}</span>
            <span class="text-gray-600">/month</span>
          </div>
          
          <div v-if="plan.yearly_price" class="text-center text-sm text-gray-500 mt-1">
            or ₦{{ yearlyPrice }} billed yearly ({{ yearlyDiscount }}% off)
          </div>
        </div>
        
        <div class="mt-6">
          <ul class="space-y-2">
            <li v-for="(value, feature) in plan.features" :key="feature" class="flex items-start">
              <svg class="h-5 w-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span class="ml-2">{{ feature }}: {{ value }}</span>
            </li>
          </ul>
        </div>

        <div class="mt-4">
          <div v-if="plan.is_free" class="text-center">
            <span class="text-4xl font-bold">FREE</span>
          </div>
          <div v-else class="text-center">
            <span class="text-4xl font-bold">₦{{ monthlyPrice }}</span>
            <span class="text-gray-600">/month</span>
          </div>
        </div>
        
        <div class="mt-8">
          <select v-model="billingCycle" class="w-full border rounded-md p-2 mb-4">
            <option value="monthly">Monthly (₦{{ monthlyPrice }})</option>
            <option v-if="plan.quarterly_price" value="quarterly">Quarterly (₦{{ quarterlyPrice }})</option>
            <option v-if="plan.yearly_price" value="yearly">Yearly (₦{{ yearlyPrice }})</option>
          </select>
          
          <form :action="route('examiner.subscribe')" method="POST" @submit.prevent="handleSubmit">
            <input type="hidden" name="_token" :value="csrfToken">
            <input type="hidden" name="plan_id" :value="plan.id">
            <input type="hidden" name="billing_cycle" :value="billingCycle">
            
            <button 
              @click="subscribe"
              class="w-full py-2 px-4 rounded font-medium"
              :class="{
                'bg-blue-600 hover:bg-blue-700 text-white': !isCurrentPlan,
                'bg-gray-200 text-gray-600 cursor-not-allowed': isCurrentPlan
              }"
              :disabled="isCurrentPlan || isSubmitting"
            >
              <span v-if="isSubmitting">Processing...</span>
              <span v-else>{{ isCurrentPlan ? 'Current Plan' : 'Subscribe Now' }}</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { router,usePage } from '@inertiajs/vue3';
  

  const page = usePage();
  const csrfToken = computed(() => page.props.csrf_token);
  const isSubmitting = ref(false);

  const props = defineProps({
    plan: Object,
    currentSubscription: Object
  });
  
  const billingCycle = ref('monthly');
  
  
  const monthlyPrice = computed(() => (props.plan.monthly_price ).toLocaleString());
  const quarterlyPrice = computed(() => (props.plan.quarterly_price ).toLocaleString());
  const yearlyPrice = computed(() => (props.plan.yearly_price ).toLocaleString());
  
  const yearlyDiscount = computed(() => {
    if (!props.plan.yearly_price) return 0;
    const monthlyTotal = props.plan.monthly_price * 12;
    return Math.round((monthlyTotal - props.plan.yearly_price) / monthlyTotal * 100);
  });
  
  const isCurrentPlan = computed(() => {
    return props.currentSubscription?.plan_id === props.plan.id;
  });
  
  const subscribe = () => {
  if (isSubmitting.value || props.currentSubscription?.plan_id === props.plan.id) return;
  
  isSubmitting.value = true;
  
  router.post(route('examiner.subscribe'), {
    plan_id: props.plan.id,
    billing_cycle: billingCycle.value
  }, {
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

  const isFreePlan = computed(() => {
    return props.plan.monthly_price === 0 && 
          props.plan.quarterly_price === 0 && 
          props.plan.yearly_price === 0;
  });

  const handleSubmit = (e) => {
    isSubmitting.value = true;
    e.target.submit(); // Submit the form normally
  };
  </script>