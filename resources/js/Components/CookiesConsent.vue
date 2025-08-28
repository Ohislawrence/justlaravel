<!-- resources/js/Components/CookiesConsent.vue -->
<template>
    <transition name="slide-up">
      <div v-if="showConsent" class="fixed inset-x-0 bottom-0 z-50 px-4 pb-4 sm:pb-6 sm:px-6">
        <div class="max-w-6xl mx-auto">
          <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-emerald-100">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
              <!-- Cookie Icon -->
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2极狐6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
              </div>
              
              <!-- Content -->
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">We value your privacy</h3>
                <p class="text-gray-600 mb-4">
                  We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. 
                  By clicking "Accept All", you consent to our use of cookies. You can customize your preferences in the cookie settings.
                </p>
                
                <!-- Cookie Preferences -->
                <div v-if="showPreferences" class="mt-6 space-y-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="font-medium text-gray-900">Necessary Cookies</h4>
                      <p class="text-sm text-gray-500">Essential for the website to function properly.</p>
                    </div>
                    <div class="flex items-center">
                      <span class="text-sm text-gray-500 mr-3">Always active</span>
                      <div class="relative inline-block w-12 h-6 bg-emerald-200 rounded-full cursor-not-allowed">
                        <div class="absolute left-1 top-1 w-4 h-4 bg-emerald-600 rounded-full"></div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="font-medium text-gray-900">Analytics Cookies</h4>
                      <p class="text-sm text-gray-500">Help us understand how visitors interact with our website.</p>
                    </div>
                    <div class="flex items-center">
                      <button @click="toggleCookie('analytics')" :class="{'bg-emerald-600': cookies.analytics, 'bg-gray-300': !cookies.analytics}" class="relative inline-flex items-center h-6 rounded-full w-12 transition-colors duration-200 ease-in-out">
                        <span :class="{'translate-x-6': cookies.analytics, 'translate-x-1': !cookies.analytics}" class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200 ease-in-out"></span>
                      </button>
                    </div>
                  </div>
                  
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="font-medium text-gray-900">Marketing Cookies</h4>
                      <p class="text-sm text-gray-500">Used to track visitors across websites for advertising purposes.</p>
                    </div>
                    <div class="flex items-center">
                      <button @click="toggleCookie('marketing')" :class="{'bg-emerald-600': cookies.marketing, 'bg-gray-300': !cookies.marketing}" class="relative inline-flex items-center h-6 rounded-full w-12 transition-colors duration-200 ease-in-out">
                        <span :class="{'translate-x-6': cookies.marketing, 'translate-x-1': !cookies.marketing}" class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-200 ease-in-out"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Actions -->
            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
              <div class="flex items-center">
                <button @click="showPreferences = !showPreferences" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center">
                  <span>{{ showPreferences ? 'Hide preferences' : 'Cookie preferences' }}</span>
                  <svg :class="{'rotate-180': showPreferences}" class="w-4 h-4 ml-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
              </div>
              
              <div class="flex flex-col sm:flex-row gap-3">
                <button @click="acceptEssential" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg transition-colors">
                  Necessary only
                </button>
                <button @click="acceptAll" class="px-4 py-2 bg-emerald-600 text-sm font-medium text-white hover:bg-emerald-700 rounded-lg transition-colors shadow-sm">
                  Accept all
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  
  // State
  const showConsent = ref(false)
  const showPreferences = ref(false)
  const cookies = ref({
    necessary: true,
    analytics: false,
    marketing: false
  })
  
  // Check if user has already made a choice
  onMounted(() => {
    const consentGiven = localStorage.getItem('cookieConsent')
    if (!consentGiven) {
      // Show consent banner after a short delay
      setTimeout(() => {
        showConsent.value = true
      }, 1000)
    }
  })
  
  // Toggle cookie preference
  const toggleCookie = (type) => {
    if (type !== 'necessary') {
      cookies.value[type] = !cookies.value[type]
    }
  }
  
  // Accept only necessary cookies
  const acceptEssential = () => {
    cookies.value.analytics = false
    cookies.value.marketing = false
    savePreferences()
  }
  
  // Accept all cookies
  const acceptAll = () => {
    cookies.value.analytics = true
    cookies.value.marketing = true
    savePreferences()
  }
  
  // Save preferences and hide banner
  const savePreferences = () => {
    localStorage.setItem('cookieConsent', JSON.stringify(cookies.value))
    
    // Here you would typically set the actual cookies based on preferences
    setCookies()
    
    showConsent.value = false
  }
  
  // Function to set cookies based on preferences
  const setCookies = () => {
    // Set necessary cookies (always set)
    document.cookie = "necessary_cookies=true; max-age=31536000; path=/; samesite=lax"
    
    if (cookies.value.analytics) {
      document.cookie = "analytics_cookies=true; max-age=31536000; path=/; samesite=lax"
      // Initialize analytics scripts here
      console.log("Analytics cookies enabled")
    }
    
    if (cookies.value.marketing) {
      document.cookie = "marketing_cookies=true; max-age=31536000; path=/; samesite=lax"
      // Initialize marketing scripts here
      console.log("Marketing cookies enabled")
    }
  }
  </script>
  
  <style scoped>
  .slide-up-enter-active,
  .slide-up-leave-active {
    transition: all 0.5s ease;
  }
  
  .slide-up-enter-from,
  .slide-up-leave-to {
    transform: translateY(100%);
    opacity: 0;
  }
  </style>