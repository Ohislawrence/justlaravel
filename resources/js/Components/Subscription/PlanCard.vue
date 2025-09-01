<template>
  <div
    class="bg-white rounded-lg shadow-md overflow-hidden border"
    :class="{
      'border-green-500 ring-2 ring-green-200': isCurrentPlan,
    }"
  >
    <div class="p-6">
      <!-- Plan Header -->
      <div class="flex justify-between items-start">
        <h3 class="text-2xl font-bold text-gray-900">{{ plan.name }}</h3>
        <span
          v-if="isCurrentPlan"
          class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full self-center font-medium"
        >
          Current Plan
        </span>
      </div>

      <!-- Pricing Display -->
      <div class="mt-6">
        <div class="text-center">
          <div v-if="isFreePlan">
            <span class="text-4xl font-bold text-green-700">FREE</span>
          </div>
          <div v-else>
            <!-- Display price based on selected billing cycle -->
            <span class="text-4xl font-bold text-green-700">
              ₦{{
                billingCycle === 'yearly'
                  ? yearlyPrice
                  : billingCycle === 'quarterly'
                  ? quarterlyPrice
                  : monthlyPrice
              }}
            </span>
            <span class="text-gray-600 ml-1">/{{ billingCycleDisplay }}</span>
          </div>
        </div>

        <!-- Yearly Discount Info -->
        <div
          v-if="plan.yearly_price && !isFreePlan"
          class="text-center text-sm text-green-600 mt-2 font-medium"
        >
          <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Save {{ yearlyDiscount }}% with yearly billing
          </span>
        </div>
      </div>

      <!-- Features List -->
      <div class="mt-6">
        <ul class="space-y-3">
          <li
            v-for="(value, feature) in plan.features"
            :key="feature"
            class="flex items-start"
          >
            <div class="flex-shrink-0 mt-1">
              <svg
                class="h-5 w-5 text-green-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>
            </div>
            <div class="ml-3">
              <span class="text-gray-700 block">
                <span class="font-semibold text-gray-900">{{ feature }}</span>
                <span class="text-gray-600">: {{ value }}</span>
              </span>
            </div>
          </li>
        </ul>
      </div>

      <!-- Billing Cycle Selector & Subscribe Button -->
      <div class="mt-8 space-y-4">
        <!-- Hide selector for free plans or if only one cycle exists -->
        <div v-if="!isFreePlan && hasMultipleBillingOptions" class="mt-4">
          <label for="billing-cycle" class="block text-sm font-medium text-gray-700 mb-2">Billing Cycle</label>
          <select
            id="billing-cycle"
            v-model="billingCycle"
            :disabled="isCurrentPlan"
            class="w-full border border-green-300 rounded-lg p-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:border-green-500 bg-white transition-all duration-200"
          >
            <option value="monthly" class="py-2">Monthly (₦{{ monthlyPrice }})</option>
            <option
              v-if="plan.quarterly_price"
              value="quarterly"
              class="py-2"
            >
              Quarterly (₦{{ quarterlyPrice }})
            </option>
            <option v-if="plan.yearly_price" value="yearly" class="py-2">
              Yearly (₦{{ yearlyPrice }})
            </option>
          </select>
        </div>

        <button
          @click="subscribe"
          class="w-full py-3 px-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          :class="{
            'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow-lg hover:shadow-xl': !isCurrentPlan,
            'bg-gray-100 text-gray-400 cursor-not-allowed': isCurrentPlan,
          }"
          :disabled="isCurrentPlan || isSubmitting"
        >
          <span v-if="isSubmitting" class="flex items-center justify-center">
            <svg
              class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Processing...
          </span>
          <span v-else class="flex items-center justify-center">
            <svg v-if="isCurrentPlan" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ isCurrentPlan ? 'Current Plan' : 'Subscribe Now' }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();
const isSubmitting = ref(false);

const props = defineProps({
  plan: {
    type: Object,
    required: true,
  },
  currentSubscription: {
    type: Object,
    default: null,
  },
});

const billingCycle = ref('monthly'); // Default to monthly

// --- Computed Properties ---
const monthlyPrice = computed(() => {
  return props.plan.monthly_price ? props.plan.monthly_price.toLocaleString() : '0';
});
const quarterlyPrice = computed(() => {
  return props.plan.quarterly_price ? props.plan.quarterly_price.toLocaleString() : '0';
});
const yearlyPrice = computed(() => {
  return props.plan.yearly_price ? props.plan.yearly_price.toLocaleString() : '0';
});

const yearlyDiscount = computed(() => {
  if (!props.plan.yearly_price || props.plan.monthly_price <= 0) return 0;
  const monthlyTotal = props.plan.monthly_price * 12;
  const discount = ((monthlyTotal - props.plan.yearly_price) / monthlyTotal) * 100;
  return Math.round(discount);
});

const isCurrentPlan = computed(() => {
  return props.currentSubscription?.plan_id === props.plan.id;
});

const isFreePlan = computed(() => {
  return (
    props.plan.monthly_price == 0 &&
    props.plan.quarterly_price == 0 &&
    props.plan.yearly_price == 0
  );
});

const billingCycleDisplay = computed(() => {
  switch (billingCycle.value) {
    case 'quarterly':
      return 'quarter';
    case 'yearly':
      return 'year';
    default:
      return 'month';
  }
});

const hasMultipleBillingOptions = computed(() => {
    const options = [
        props.plan.monthly_price,
        props.plan.quarterly_price,
        props.plan.yearly_price
    ].filter(price => price !== undefined && price !== null && price > 0);
    return options.length > 1;
});

// --- Methods ---
const subscribe = () => {
  if (isSubmitting.value || isCurrentPlan.value) {
    return;
  }

  isSubmitting.value = true;

  router.post(
    route('examiner.subscribe'),
    {
      plan_id: props.plan.id,
      billing_cycle: billingCycle.value,
    },
    {
      onFinish: () => {
        isSubmitting.value = false;
      },
    }
  );
};
</script>

<style scoped>
/* Add any specific scoped styles here if needed */
</style>