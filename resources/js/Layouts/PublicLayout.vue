<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import MobileMenu from '@/Components/MobileMenu.vue'; // Ensure this component exists and functions as expected
import FlashMessages from '@/Components/FlashMessages.vue'; // Ensure this component exists
import { Icon } from '@iconify/vue';
import FacebookIcon from '@iconify-icons/mdi/facebook';
import TwitterIcon from '@iconify-icons/mdi/twitter';

// Define the title prop
defineProps({
    title: String,
    organisation:String,
});
const page = usePage();



const mobileMenuOpen = ref(false);
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
                        <Link :href="route('home')" class="text-xl font-bold text-indigo-600 hover:text-indigo-800 transition-colors duration-150">
                            QuizPortal
                        </Link>
                    </div>

                    <!-- Desktop Navigation Links -->
                    <nav class="hidden md:flex md:space-x-1"> <!-- Reduced space-x -->
                        <Link
                            :href="route('examinee.dashboard')"
                            :class="[
                                'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
                                route().current('examinee.dashboard') 
                                    ? 'bg-indigo-50 text-indigo-700' 
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                        >
                            My Dashboard
                        </Link>
                        <Link
                            :href="route('examinee.quizzes.index')"
                            :class="[
                                'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
                                route().current('examinee.quizzes.index') 
                                    ? 'bg-indigo-50 text-indigo-700' 
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                        >
                            My Quizzes
                        </Link>
                        <Link
                            :href="route('examinee.history')"
                            :class="[
                                'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
                                route().current('examinee.history') 
                                    ? 'bg-indigo-50 text-indigo-700' 
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                        >
                            My History
                        </Link>
                    </nav>

                    <!-- Auth Links -->
                    <div class="flex items-center space-x-3 md:space-x-4"> <!-- Consistent spacing -->
                        <template v-if="page.props.auth?.user">
                            <Link
                                :href="route('dashboard')"
                                class="text-gray-600 hover:text-gray-900 text-sm font-medium px-3 py-2 rounded-md hover:bg-gray-50 transition-colors duration-150"
                            >
                                {{ $page.props.auth.user.name }}
                            </Link>
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
                                class="ml-1 md:ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150" <!-- Standard primary button -->
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
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                            aria-expanded="false"
                        >
                            <span class="sr-only">Open main menu</span>
                            <!-- Heroicon name: outline/menu -->
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Menu Component -->
        <MobileMenu :show="mobileMenuOpen" @close="mobileMenuOpen = false" />

        <!-- Page Content -->
        <main class="flex-grow">
            <!-- Using a structure similar to AppLayout for consistency -->
            <div class="container-xxl flex-grow-1 container-p-y"> 
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Flash Messages - Placed inside the main content area -->
                    <FlashMessages class="mb-6" /> 

                    <!-- Page Heading Slot -->
                    <header v-if="$slots.header" class="mb-6">
                        <slot name="header" /> 
                    </header>

                    <!-- Main Content Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                             <slot /> 
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12"> <!-- Added margin-top -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex justify-center space-x-6 md:order-2">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Facebook</span>
                            <Icon :icon="FacebookIcon" class="h-5 w-5" />
                        </a>
                        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Twitter</span>
                            <Icon :icon="TwitterIcon" class="h-5 w-5" />
                        </a>
                        <!-- Add more social links as needed -->
                    </div>
                    <div class="mt-8 md:mt-0 md:order-1">
                        <p class="text-center text-sm text-gray-500">
                            &copy; {{ new Date().getFullYear() }} Powered by QuizPortal.online. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Scoped styles remain unchanged */
</style>