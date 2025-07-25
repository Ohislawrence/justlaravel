<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    monthly_price: 0,
    quarterly_price: 0,
    yearly_price: 0,
    trial_days: 14,
    is_active: true,
});

const submitForm = () => {
    form.post(route('landlord.subscriptions.store'));
};
</script>

<template>
    <AppLayout title="Create Subscription Plan">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Subscription Plan
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submitForm">
                            <!-- Name -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                    Plan Name *
                                </label>
                                <input
                                    v-model="form.name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name"
                                    type="text"
                                    required
                                >
                            </div>

                            <!-- Pricing -->
                            <div class="grid md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="monthly_price">
                                        Monthly Price (₦)
                                    </label>
                                    <input
                                        v-model="form.monthly_price"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="monthly_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="quarterly_price">
                                        Quarterly Price (₦)
                                    </label>
                                    <input
                                        v-model="form.quarterly_price"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="quarterly_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="yearly_price">
                                        Yearly Price (₦)
                                    </label>
                                    <input
                                        v-model="form.yearly_price"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="yearly_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Trial Days -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="trial_days">
                                    Trial Days
                                </label>
                                <input
                                    v-model="form.trial_days"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="trial_days"
                                    type="number"
                                    min="0"
                                    required
                                >
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Status
                                </label>
                                <div class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        id="is_active"
                                        name="is_active"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    >
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Active Plan
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200">
                                <Link 
                                    :href="route('landlord.subscriptions.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="ml-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Plan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>