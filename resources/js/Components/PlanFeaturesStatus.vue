<!-- resources/js/Components/PlanFeaturesStatus.vue -->
<template>
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Plan Features</h3>
        
        <div class="space-y-4">
            <div v-for="(feature, key) in features" :key="key" class="flex items-start">
                <div class="flex-shrink-0">
                    <CheckCircleIcon v-if="feature.available" class="h-5 w-5 text-green-500" />
                    <XCircleIcon v-else class="h-5 w-5 text-red-500" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ feature.name }}</p>
                    <p v-if="feature.limit" class="text-sm text-gray-500">
                        {{ feature.used }}/{{ feature.limit }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <Link 
                :href="upgradeLink"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
                Upgrade plan
            </Link>
        </div>
    </div>
</template>

<script setup>
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const organization = computed(() => usePage().props.organization);

const features = computed(() => {
    const features = organization.value?.active_subscription?.plan?.features || {};
    
    // Convert to simple object if it's an array
    const featureObj = Array.isArray(features) 
        ? features.reduce((acc, feature) => {
            acc[feature.slug] = feature.pivot.value;
            return acc;
        }, {})
        : features;

    return [
        {
            name: 'AI Question Generation',
            available: !!featureObj['ai_question_generation'],
            key: 'ai'
        },
        {
            name: 'Question Pools',
            available: true,
            used: 0,
            limit: featureObj['question_pool_limit'] || 1,
            key: 'question_pools'
        },
        // Add other features...
    ];
});
</script>