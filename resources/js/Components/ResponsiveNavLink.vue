<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
        ? 'block w-full px-4 py-2 rounded-lg border-l-4 border-green-500 text-start text-base font-semibold text-green-700 bg-green-50 shadow-sm focus:outline-none focus:text-green-800 focus:bg-green-100 focus:border-green-600 transition duration-200 ease-in-out'
        : 'block w-full px-4 py-2 rounded-lg border-l-4 border-transparent text-start text-base font-medium text-gray-700 hover:text-green-700 hover:bg-green-50 hover:border-green-400 focus:outline-none focus:text-green-800 focus:bg-green-100 focus:border-green-500 transition duration-200 ease-in-out';
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <a v-else-if="as == 'a'" :class="classes" class="w-full text-start" :href="href">
            <slot />
        </a>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
