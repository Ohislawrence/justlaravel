<template>
  <div
    class="bg-white rounded-lg shadow-md overflow-hidden border"
    :class="{
      'border-blue-500 ring-2 ring-blue-200': isCurrentPlan,
    }"
  >
    <div class="p-6">
      <!-- Plan Header -->
      <div class="flex justify-between items-start">
        <h3 class="text-2xl font-bold">{{ plan.name }}</h3>
        <span
          v-if="isCurrentPlan"
          class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full self-center"
        >
          Current Plan
        </span>
      </div>

      <!-- Pricing Display -->
      <div class="mt-6">
        <div class="text-center">
          <div v-if="isFreePlan">
            <span class="text-4xl font-bold">FREE</span>
          </div>
          <div v-else>
            <!-- Display price based on selected billing cycle -->
            <span class="text-4xl font-bold">
              ₦{{
                billingCycle === 'yearly'
                  ? yearlyPrice
                  : billingCycle === 'quarterly'
                  ? quarterlyPrice
                  : monthlyPrice
              }}
            </span>
            <span class="text-gray-600">/{{ billingCycleDisplay }}</span>
          </div>
        </div>

        <!-- Yearly Discount Info -->
        <div
          v-if="plan.yearly_price && !isFreePlan"
          class="text-center text-sm text-gray-500 mt-2"
        >
          Billed yearly: ₦{{ yearlyPrice }} (Save {{ yearlyDiscount }}%)
        </div>
      </div>

      <!-- Features List -->
      <div class="mt-6">
        <ul class="space-y-2">
          <li
            v-for="(value, feature) in plan.features"
            :key="feature"
            class="flex items-start"
          >
            <svg
              class="h-5 w-5 text-green-500 mt-0.5 flex-shrink-0"
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
            <span class="ml-2 text-gray-700"
              ><span class="font-medium">{{ feature }}</span
              >: {{ value }}</span
            >
          </li>
        </ul>
      </div>

      <!-- Billing Cycle Selector & Subscribe Button -->
      <div class="mt-8 space-y-4">
        <!-- Hide selector for free plans or if only one cycle exists -->
        <div v-if="!isFreePlan && hasMultipleBillingOptions" class="mt-4">
          <label for="billing-cycle" class="block text-sm font-medium text-gray-700 mb-1">Billing Cycle</label>
          <select
            id="billing-cycle"
            v-model="billingCycle"
            :disabled="isCurrentPlan"
            class="w-full border border-gray-300 rounded-md p-2 shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="monthly">Monthly (₦{{ monthlyPrice }})</option>
            <option
              v-if="plan.quarterly_price"
              value="quarterly"
            >
              Quarterly (₦{{ quarterlyPrice }})
            </option>
            <option v-if="plan.yearly_price" value="yearly">
              Yearly (₦{{ yearlyPrice }})
            </option>
          </select>
        </div>

        <button
          @click="subscribe"
          class="w-full py-3 px-4 rounded-md font-medium transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :class="{
            'bg-blue-600 hover:bg-blue-700 text-white': !isCurrentPlan,
            'bg-gray-200 text-gray-600 cursor-not-allowed': isCurrentPlan,
          }"
          :disabled="isCurrentPlan || isSubmitting"
        >
          <span v-if="isSubmitting">
            <svg
              class="animate-spin -ml-1 mr-2 h-4 w-4 inline text-white"
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
          <span v-else>{{ isCurrentPlan ? 'Current Plan' : 'Subscribe Now' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();
// const csrfToken = computed(() => page.props.csrf_token); // Not needed if using Inertia router.post
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
  // More robust check for a free plan
  return (
    props.plan.monthly_price == 0 &&
    props.plan.quarterly_price == 0 &&
    props.plan.yearly_price == 0
  );
});

// Determine display text for the billing period (e.g., "month", "year")
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

// Check if there are multiple billing options to show the selector
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
  // Prevent action if already submitting or on the current plan
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
      // Consider adding onError or onSuccess if specific UI updates are needed
    }
  );
};
</script>

<style scoped>
/* Add any specific scoped styles here if needed */
</style>