<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    links: {
        type: Object,
        default: () => ({})
    }
});

const hasLinks = computed(() => {
    return props.links && 
           (props.links.first || 
            props.links.last || 
            props.links.prev || 
            props.links.next);
});
</script>

<template>
    <nav v-if="hasLinks" class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
        <div class="-mt-px flex w-0 flex-1">
            <Link 
                v-if="links.first"
                :href="links.first.url" 
                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': !links.first.url }"
                :disabled="!links.first.url"
                preserve-scroll
            >
                &laquo; First
            </Link>
            
            <Link 
                v-if="links.prev"
                :href="links.prev.url" 
                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': !links.prev.url }"
                :disabled="!links.prev.url"
                preserve-scroll
            >
                Previous
            </Link>
        </div>
        
        <div class="-mt-px flex w-0 flex-1 justify-end">
            <Link 
                v-if="links.next"
                :href="links.next.url" 
                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': !links.next.url }"
                :disabled="!links.next.url"
                preserve-scroll
            >
                Next
            </Link>
            
            <Link 
                v-if="links.last"
                :href="links.last.url" 
                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': !links.last.url }"
                :disabled="!links.last.url"
                preserve-scroll
            >
                Last &raquo;
            </Link>
        </div>
    </nav>
</template>