<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    plan: Object,
    features: Array,
});

const form = useForm({
    name: props.plan.name,
    description: props.plan.description,
    monthly_price: props.plan.monthly_price,
    quarterly_price: props.plan.quarterly_price,
    yearly_price: props.plan.yearly_price,
    trial_days: props.plan.trial_days,
    is_active: props.plan.is_active,
    features: props.plan.features.reduce((acc, feature) => {
        acc[feature.slug] = feature.pivot.value;
        return acc;
    }, {})
});

const submitForm = () => {
    form.put(route('landlord.subscriptions.update', props.plan.id));
};
</script>

<template>
    <AppLayout title="Edit Subscription Plan">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Subscription Plan - {{ plan.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submitForm">
                            <!-- Basic Plan Info -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-4">Plan Information</h3>
                                
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
                                        :class="{ 'border-red-500': form.errors.name }"
                                    >
                                    <p v-if="form.errors.name" class="text-red-500 text-xs italic mt-1">
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                        Description
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="description"
                                        rows="3"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-4">Pricing</h3>
                                
                                <div class="grid md:grid-cols-3 gap-4">
                                    <!-- Monthly Price -->
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
                                            :class="{ 'border-red-500': form.errors.monthly_price }"
                                        >
                                        <p v-if="form.errors.monthly_price" class="text-red-500 text-xs italic mt-1">
                                            {{ form.errors.monthly_price }}
                                        </p>
                                    </div>

                                    <!-- Quarterly Price -->
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
                                            :class="{ 'border-red-500': form.errors.quarterly_price }"
                                        >
                                        <p v-if="form.errors.quarterly_price" class="text-red-500 text-xs italic mt-1">
                                            {{ form.errors.quarterly_price }}
                                        </p>
                                    </div>

                                    <!-- Yearly Price -->
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
                                            :class="{ 'border-red-500': form.errors.yearly_price }"
                                        >
                                        <p v-if="form.errors.yearly_price" class="text-red-500 text-xs italic mt-1">
                                            {{ form.errors.yearly_price }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Trial Days -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-4">Trial Settings</h3>
                                
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
                                        :class="{ 'border-red-500': form.errors.trial_days }"
                                    >
                                    <p v-if="form.errors.trial_days" class="text-red-500 text-xs italic mt-1">
                                        {{ form.errors.trial_days }}
                                    </p>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-4">Plan Features</h3>
                                
                                <div class="space-y-4">
                                    <div v-for="feature in features" :key="feature.id" class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input
                                                v-if="feature.type === 'boolean'"
                                                v-model="form.features[feature.slug]"
                                                :id="`feature-${feature.slug}`"
                                                :name="`features[${feature.slug}]`"
                                                type="checkbox"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                :true-value="true"
                                                :false-value="false"
                                            >
                                            <input
                                                v-else
                                                v-model="form.features[feature.slug]"
                                                :id="`feature-${feature.slug}`"
                                                :name="`features[${feature.slug}]`"
                                                type="number"
                                                min="0"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline max-w-xs"
                                            >
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label :for="`feature-${feature.slug}`" class="font-medium text-gray-700">
                                                {{ feature.name }}
                                            </label>
                                            <p class="text-gray-500">{{ feature.description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-4">Plan Status</h3>
                                
                                <div class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        id="is_active"
                                        name="is_active"
                                        type="checkbox"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                    >
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Active Plan
                                    </label>
                                </div>
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
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update Plan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>