<template>
  <div class="min-h-screen bg-white flex flex-col">
    <!-- Gradient Top Bar -->
    <div class="w-full h-1 bg-gradient-to-r from-emerald-500 via-white to-emerald-500"></div>

    <!-- Minimalist Header (reduced height) -->
    <header class="container mx-auto px-4 sm:px-6 py-3 sticky top-0 bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
          <Link :href="route('home')" class="flex items-center">
            <img 
              src="/images/logo.png" 
              alt="ExamPortal Logo" 
              class="w-14 h-14 sm:w-16 sm:h-16 md:w-40 md:h-18 object-contain transition-all duration-200 hover:scale-110"
            />
          </Link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex space-x-1">
          <Link :href="route('features')" class="px-3 py-1.5 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Features</Link>
          <Link :href="route('pricing')" class="px-3 py-1.5 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Pricing</Link>
          <Link :href="route('blogs.index')" class="px-3 py-1.5 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Blog</Link>
          <Link :href="route('contact')" class="px-3 py-1.5 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Contact</Link>
        </nav>

        <!-- Auth Buttons -->
        <div class="flex items-center space-x-2">
          <Link
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm hover:shadow text-xs font-medium">
            Dashboard
          </Link>
          <template v-else>
            <Link :href="route('login')" class="px-3 py-1.5 text-gray-700 hover:text-emerald-600 transition text-xs font-medium">
              Sign In
            </Link>
            <Link :href="route('register')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm hover:shadow text-xs font-medium">
              Get Started
            </Link>
          </template>

          <!-- Mobile Menu Button -->
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-500 hover:text-emerald-600 ml-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div v-if="mobileMenuOpen" class="md:hidden mt-3 py-3 border-t border-gray-100">
        <nav class="flex flex-col space-y-2">
          <Link :href="route('features')" class="px-3 py-2 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Features</Link>
          <Link :href="route('pricing')"  class="px-3 py-2 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Pricing</Link>
          <Link :href="route('blogs.index')" class="px-3 py-2 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Blog</Link>
          <Link :href="route('contact')" class="px-3 py-2 text-gray-600 hover:text-emerald-600 transition rounded-lg hover:bg-emerald-50 text-sm">Contact</Link>
        </nav>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Enhanced Footer -->
    <footer class="relative bg-gradient-to-br from-slate-900 via-gray-900 to-slate-800 text-gray-300 overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
      </div>
      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
      <div class="relative">
        <!-- Main Footer Content -->
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            <!-- Company Info Section -->
            <div class="lg:col-span-5 space-y-6">
              <!-- Logo -->
              <div class="group">
                <div class="flex items-center space-x-3 mb-6">
                  <Link :href="route('home')" class="flex items-center">
                    <img 
                      src="/images/logo.png" 
                      alt="ExamPortal Logo" 
                      class="w-14 h-14 sm:w-16 sm:h-10 md:w-40 md:h-18 object-contain transition-all duration-200 hover:scale-110"
                    />
                  </Link>
                </div>
              </div>
              <!-- Description -->
              <p class="text-gray-400 text-lg leading-relaxed max-w-md">
                The complete exam platform designed for Nigerian educational institutions, corporations, and training organizations.
              </p>
              <!-- Social Links -->
              <div class="flex space-x-4">
                <a href="#" target="_blank" class="group relative p-3 bg-gray-800/50 hover:bg-gradient-to-br hover:from-emerald-600 hover:to-teal-600 rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-emerald-500/25">
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                  </svg>
                </a>
                <a href="#" target="_blank" class="group relative p-3 bg-gray-800/50 hover:bg-gradient-to-br hover:from-pink-600 hover:to-rose-600 rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-pink-500/25">
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                </a>
                <a href="https://www.linkedin.com/company/examportalonline" target="_blank" class="group relative p-3 bg-gray-800/50 hover:bg-gradient-to-br hover:from-blue-600 hover:to-blue-700 rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-blue-500/25">
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                  </svg>
                </a>
                <a href="https://wa.me/2348117297730?text=" target="_blank" class="group relative p-3 bg-gray-800/50 hover:bg-gradient-to-br hover:from-green-600 hover:to-green-700 rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-green-500/25">
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-1.75-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                  </svg>
                </a>
              </div>
            </div>

            <!-- Navigation Links -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-3 gap-8 lg:gap-12">
              <!-- Product Links -->
              <div class="space-y-4">
                <h3 class="text-white text-lg font-semibold mb-6 relative">
                  Product
                  <div class="absolute -bottom-2 left-0 w-8 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full"></div>
                </h3>
                <nav class="space-y-3">
                  <Link :href="route('features')" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Features
                  </Link>
                  <Link :href="route('pricing')" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Pricing
                  </Link>
                  <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Integrations
                  </a>
                </nav>
              </div>

              <!-- Company Links -->
              <div class="space-y-4">
                <h3 class="text-white text-lg font-semibold mb-6 relative">
                  Company
                  <div class="absolute -bottom-2 left-0 w-8 h-0.5 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-full"></div>
                </h3>
                <nav class="space-y-3">
                  <Link :href="route('aboutus')" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-teal-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    About Us
                  </Link>
                  <Link :href="route('blogs.index')" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-teal-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Blog
                  </Link>
                  <Link :href="route('contact')" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-teal-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Contact
                  </Link>
                </nav>
              </div>

              <!-- Support Links -->
              <div class="space-y-4">
                <h3 class="text-white text-lg font-semibold mb-6 relative">
                  Support
                  <div class="absolute -bottom-2 left-0 w-8 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full"></div>
                </h3>
                <nav class="space-y-3">
                  <Link :href="route('help.index')" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Help Center
                  </Link>
                  <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Contact Support
                  </a>
                  <Link :href="route('help.index')" class="group flex items-center text-gray-400 hover:text-white transition-all duration-200 hover:translate-x-1">
                    <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    Tutorials
                  </Link>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800/50 backdrop-blur-sm">
          <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
              <div class="flex items-center space-x-4 text-sm text-gray-400">
                <span>&copy; 2025 ExamPortal Nigeria. All rights reserved.</span>
                <div class="hidden sm:flex items-center space-x-2">
                  <span>•</span>
                  <span class="flex items-center">
                    Made with
                    <svg class="w-4 h-4 text-red-500 mx-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    in Nigeria
                  </span>
                </div>
              </div>
              <div class="flex flex-wrap justify-center gap-6 text-sm">
                <Link :href="route('policy')" class="text-gray-400 hover:text-white transition-colors duration-200 hover:underline underline-offset-4">Privacy Policy</Link>
                <Link :href="route('tos')" class="text-gray-400 hover:text-white transition-colors duration-200 hover:underline underline-offset-4">Terms of Service</Link>
                <Link :href="route('cookie')" class="text-gray-400 hover:text-white transition-colors duration-200 hover:underline underline-offset-4">Cookie Policy</Link>
                <Link :href="route('policy')" class="text-gray-400 hover:text-white transition-colors duration-200 hover:underline underline-offset-4">Accessibility</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <WhatsAppFloat />
  </div>
  <!-- Cookie Consent -->
  <CookiesConsent />
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import WhatsAppFloat from '@/Components/WhatsAppFloat.vue'
import CookiesConsent from '@/Components/CookiesConsent.vue'

// state
const mobileMenuOpen = ref(false)

// access Inertia page object
const page = usePage()

// close mobile menu when route changes
watch(
  () => page.url,
  () => {
    mobileMenuOpen.value = false
  }
)
</script>

<style scoped>
@keyframes blob {
  0% {
      transform: translate(0px, 0px) scale(1);
  }
  33% {
      transform: translate(30px, -50px) scale(1.1);
  }
  66% {
      transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
      transform: translate(0px, 0px) scale(1);
  }
}
</style>