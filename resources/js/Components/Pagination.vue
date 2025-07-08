<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    links: Array,
});

const visibleLinks = computed(() => {
    return props.links.filter((link, index) => {
        // Always show first, last, and current page
        if (index === 0 || index === props.links.length - 1) return true;
        if (link.active) return true;
        
        // Show some pages around current page
        const currentIndex = props.links.findIndex(l => l.active);
        return Math.abs(index - currentIndex) <= 2;
    });
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
        <div class="-mt-px flex w-0 flex-1">
            <Link 
                :href="links[0].url" 
                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': !links[0].url }"
                :disabled="!links[0].url"
                preserve-scroll
            >
                &laquo; First
            </Link>
        </div>
        
        <div class="hidden md:-mt-px md:flex">
            <template v-for="(link, index) in visibleLinks">
                <Link
                    v-if="index !== 0 && index !== links.length - 1"
                    :key="index"
                    :href="link.url"
                    class="inline-flex items-center border-t-2 px-4 pt-4 text-sm font-medium"
                    :class="{
                        'border-indigo-500 text-indigo-600': link.active,
                        'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700': !link.active,
                        'opacity-50 cursor-not-allowed': !link.url
                    }"
                    :disabled="!link.url"
                    preserve-scroll
                >
                    {{ link.label }}
                </Link>
            </template>
        </div>
        
        <div class="-mt-px flex w-0 flex-1 justify-end">
            <Link 
                :href="links[links.length - 1].url" 
                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
                :class="{ 'opacity-50 cursor-not-allowed': !links[links.length - 1].url }"
                :disabled="!links[links.length - 1].url"
                preserve-scroll
            >
                Last &raquo;
            </Link>
        </div>
    </nav>
</template>