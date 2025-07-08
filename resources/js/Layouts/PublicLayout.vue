<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
      <!-- Navigation Bar -->
      <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16 items-center">
            <!-- Logo/Brand -->
            <div class="flex-shrink-0 flex items-center">
              Tracklia
            </div>
  
            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-8">
              <Link
                :href="route('home')"
                class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium"
                :class="{ 'text-blue-600': $page.url === '/' }"
              >
                Home
              </Link>
              <Link
                :href="route('home')"
                class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium"
                :class="{ 'text-blue-600': $page.url.startsWith('/home') }"
              >
                About
              </Link>
              <Link
                :href="route('home')"
                class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium"
                :class="{ 'text-blue-600': $page.url.startsWith('/quizzes') }"
              >
                Quizzes
              </Link>
            </nav>
  
            <!-- Auth Links -->
            <div class="flex items-center space-x-4">
              <template v-if="$page.props.auth.user">
                <Link
                  :href="route('dashboard')"
                  class="text-gray-500 hover:text-gray-700 text-sm font-medium"
                >
                  Dashboard
                </Link>
              </template>
              <template v-else>
                <Link
                  :href="route('login')"
                  class="text-gray-500 hover:text-gray-700 text-sm font-medium"
                >
                  Sign in
                </Link>
                <Link
                  :href="route('register')"
                  class="ml-4 btn btn-primary btn-sm"
                >
                  Sign up
                </Link>
              </template>
            </div>
          </div>
        </div>
      </header>
  
      <!-- Mobile Menu -->
      <MobileMenu :show="mobileMenuOpen" @close="mobileMenuOpen = false" />
  
      <!-- Page Content -->
      <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <!-- Flash Messages -->
          <FlashMessages />
  
          <!-- Page Heading -->
          <header v-if="$slots.header" class="mb-6">
            <slot header="header" />
          </header>
  
          <!-- Main Content -->
          <div class="bg-white rounded-lg shadow overflow-hidden">
            <slot />
          </div>
        </div>
      </main>
  
      <!-- Footer -->
      <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="md:flex md:items-center md:justify-between">
            <div class="flex justify-center space-x-6 md:order-2">
              <a href="#" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Facebook</span>
                <FacebookIcon class="h-6 w-6" />
              </a>
              <a href="#" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Twitter</span>
                <TwitterIcon class="h-6 w-6" />
              </a>
            </div>
            <div class="mt-8 md:mt-0 md:order-1">
              <p class="text-center text-sm text-gray-500">
                &copy; {{ new Date().getFullYear() }} Powered by Tracklia.com . All rights reserved.
              </p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import MobileMenu from '@/Components/MobileMenu.vue';
  import { Icon } from '@iconify/vue'
  import FacebookIcon from '@iconify-icons/mdi/facebook'
  import TwitterIcon from '@iconify-icons/mdi/twitter'
  
  const mobileMenuOpen = ref(false);
  </script>
  
  <style scoped>
  /* Add any custom styles here */
  .router-link-active {
    @apply text-blue-600 font-medium;
  }
  </style>