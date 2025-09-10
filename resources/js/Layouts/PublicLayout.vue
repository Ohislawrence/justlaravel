<script setup>
import { ref, computed } from 'vue'; // Added computed import
import { Head, Link, usePage } from '@inertiajs/vue3';
import MobileMenu from '@/Components/MobileMenu.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import { Icon } from '@iconify/vue';
import FacebookIcon from '@iconify-icons/mdi/facebook';
import TwitterIcon from '@iconify-icons/mdi/twitter';

defineProps({
    title: String,
    organisation: String,
});

const page = usePage();
const mobileMenuOpen = ref(false);

// Helper: Check if user is logged in
const isLoggedIn = computed(() => !!page.props.auth?.user);
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Navigation Bar -->
        <header class="bg-white shadow-sm z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo/Brand -->
                    <div class="flex-shrink-0 flex items-center">
                        <Link :href="route('home')" class="text-xl font-bold text-green-600 hover:text-green-800 transition-colors duration-150">
                            <div class="px-4 py-4 flex items-center">
                            <!-- Responsive image: full height on desktop, smaller on mobile -->
                            <img 
                                class="h-4 sm:h-8 w-auto object-contain" 
                                src="/images/logo.png" 
                                alt="ExamPortal" 
                            />
                            </div>
                        </Link>
                    </div>

                    <!-- Desktop Navigation Links (Visible only to logged-in users) -->
                    <nav v-if="isLoggedIn" class="hidden md:flex md:space-x-1"> 
                        <Link
                            :href="route('examinee.dashboard')"
                            :class="[
                                'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
                                route().current('examinee.dashboard')
                                    ? 'bg-green-50 text-green-700'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-green-800'
                            ]"
                        >
                            My Dashboard
                        </Link>
                        <Link
                            :href="route('examinee.quizzes.index')"
                            :class="[
                                'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
                                route().current('examinee.quizzes.index')
                                    ? 'bg-green-50 text-green-700'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-green-800'
                            ]"
                        >
                            My Quizzes
                        </Link>
                        <Link
                            :href="route('examinee.history')"
                            :class="[
                                'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
                                route().current('examinee.history')
                                    ? 'bg-green-50 text-green-700'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-green-800'
                            ]"
                        >
                            My History
                        </Link>
                    </nav>

                    <!-- Auth Links -->
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <template v-if="isLoggedIn"> <!-- Fixed v-if -->
                            <span class="text-sm font-medium text-gray-700">
                                Hi, <span class="font-semibold">{{ page.props.auth.user.name }}</span>
                            </span>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-gray-600 hover:text-gray-900 text-sm font-medium px-3 py-2 rounded-md hover:bg-gray-50 transition-colors duration-150"
                            >
                                Sign in
                            </Link>
                            <Link
                                :href="route('register')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150"
                            >
                                Sign up
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button
                            @click="mobileMenuOpen = true"
                            type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-green-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500"
                            :aria-expanded="mobileMenuOpen" 
                            aria-controls="mobile-menu" 
                        >
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Menu -->
        <MobileMenu :show="mobileMenuOpen" @close="mobileMenuOpen = false" />

        <!-- Page Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <FlashMessages class="mb-6" />
                <header v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </header>
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex justify-center space-x-6 md:order-2">
                        <a href="https://facebook.com/examportalonline" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-green-600">
                            <span class="sr-only">Facebook</span>
                            <Icon :icon="FacebookIcon" class="h-5 w-5" />
                        </a>
                        <a href="https://twitter.com/examportalonline" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-green-600">
                            <span class="sr-only">Twitter</span>
                            <Icon :icon="TwitterIcon" class="h-5 w-5" />
                        </a>
                    </div>
                    <div class="mt-8 md:mt-0 md:order-1">
                        <p class="text-center text-sm text-gray-500">
                            &copy; {{ new Date().getFullYear() }} Powered by <Link :href="route('home')" class="text-green-600 hover:underline">ExamPortal.online</Link>. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Optional: Add subtle green focus glow */
:focus-visible {
    outline: 2px solid transparent;
    outline-offset: 2px;
}
</style>