<template>
    <div v-if="hasAccess">
        <slot />
    </div>
    <div v-else class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <ExclamationIcon class="h-5 w-5 text-yellow-400" />
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    {{ message }}
                    <a v-if="upgradeLink" :href="upgradeLink" class="font-medium underline text-yellow-700 hover:text-yellow-600">
                        Upgrade your plan
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ExclamationIcon } from '@heroicons/vue/outline';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    feature: {
        type: String,
        required: true
    },
    message: {
        type: String,
        default: 'This feature is not available in your current plan.'
    },
    upgradeLink: {
        type: String,
        default: '/examiner/subscription/plans'
    }
});

const organization = computed(() => usePage().props.organization);
const hasAccess = computed(() => {
    switch(props.feature) {
        case 'ai':
            return organization.value?.canUseAI();
        case 'question_pools':
            return organization.value?.canCreateQuestionPool();
        // Add other features
        default:
            return true;
    }
});
</script>