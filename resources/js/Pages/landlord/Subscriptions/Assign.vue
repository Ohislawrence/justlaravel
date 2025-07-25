<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    organization: Object,
    plans: Array,
});

const form = useForm({
    plan_id: props.organization.active_subscription?.plan?.id || '',
    billing_cycle: props.organization.active_subscription?.billing_cycle || 'monthly',
});

const submitForm = () => {
    form.put(route('landlord.subscriptions.assign', props.organization.id));
};
</script>

<template>
    <AppLayout :title="`Assign Subscription - ${organization.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Assign Subscription to {{ organization.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submitForm">
                            <!-- Plan Selection -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="plan_id">
                                    Subscription Plan *
                                </label>
                                <select
                                    v-model="form.plan_id"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="plan_id"
                                    required
                                    :class="{ 'border-red-500': form.errors.plan_id }"
                                >
                                    <option value="">Select a Plan</option>
                                    <option 
                                        v-for="plan in plans" 
                                        :key="plan.id" 
                                        :value="plan.id"
                                    >
                                        {{ plan.name }} - 
                                        Monthly: {{ formatPrice(plan.monthly_price) }}, 
                                        Quarterly: {{ formatPrice(plan.quarterly_price) }}, 
                                        Yearly: {{ formatPrice(plan.yearly_price) }}
                                    </option>
                                </select>
                                <p v-if="form.errors.plan_id" class="text-red-500 text-xs italic">
                                    {{ form.errors.plan_id }}
                                </p>
                            </div>

                            <!-- Billing Cycle -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="billing_cycle">
                                    Billing Cycle *
                                </label>
                                <select
                                    v-model="form.billing_cycle"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="billing_cycle"
                                    required
                                    :class="{ 'border-red-500': form.errors.billing_cycle }"
                                >
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly (10% discount)</option>
                                    <option value="yearly">Yearly (20% discount)</option>
                                </select>
                                <p v-if="form.errors.billing_cycle" class="text-red-500 text-xs italic">
                                    {{ form.errors.billing_cycle }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                                <Link 
                                    :href="route('landlord.subscriptions.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Assigning...</span>
                                    <span v-else>Assign Subscription</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    methods: {
        formatPrice(price) {
            return new Intl.NumberFormat('en-NG', { 
                style: 'currency', 
                currency: 'NGN' 
            }).format(price);
        }
    }
}
</script>