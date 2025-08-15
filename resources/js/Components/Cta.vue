<template>
    <section class="relative overflow-hidden bg-gradient-to-br from-green-900 via-emerald-900 to-green-800 text-white py-12 sm:py-16">
      <!-- Animated background elements -->
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-green-300 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
      </div>
      <!-- Grid pattern overlay -->
      <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E')] opacity-10"></div>
      
      <div class="container mx-auto px-4 sm:px-6 text-center relative z-10">
        <!-- Badge -->
        <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-xs font-medium mb-4 transform hover:scale-105 transition-all duration-300">
          <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5 animate-pulse"></span>
          Join 10,000+ Nigerian Educators
        </div>
        
        <!-- Heading with green gradient -->
        <h2 class="text-3xl sm:text-5xl font-bold mb-4 bg-gradient-to-r from-green-100 via-green-300 to-emerald-200 bg-clip-text text-transparent leading-tight">
          Ready to Transform<br />
          Your Assessments?
        </h2>
        
        <!-- Subtitle -->
        <p class="text-base sm:text-lg mb-8 max-w-2xl mx-auto text-gray-100 leading-relaxed">
          Experience the future of education with AI-powered quizzes, real-time analytics, and seamless collaboration tools.
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-3 mb-10"> <!-- Reduced gap and margin -->
          <Link
            :href="route('register')"
            class="group relative px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl font-semibold text-base shadow-lg hover:shadow-green-500/25 transition-all duration-300 transform hover:scale-[1.03] hover:-translate-y-0.5 overflow-hidden" 
          >
            <span class="relative z-10 flex items-center justify-center">
              <svg class="w-4 h-4 mr-1.5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> 
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              Start for Free
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-green-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </Link>
          <Link
            :href="route('contact')" 
            class="group px-6 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl font-semibold text-base hover:bg-white/20 transition-all duration-300 transform hover:scale-[1.03] hover:-translate-y-0.5" 
          >
            <span class="flex items-center justify-center">
              <svg class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> 
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              Contact Us
            </span>
          </Link>
        </div>
        
        <!-- Trust indicators -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6 text-xs text-gray-300 mb-2"> <!-- Smaller text, reduced gap, added margin -->
          <div class="flex items-center">
            <svg class="w-4 h-4 text-green-400 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"> <!-- Smaller icon -->
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            No Credit Card Required
          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 text-lime-400 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"> <!-- Smaller icon -->
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Instant Setup
          </div>
        </div>
      </div>
      
      <!-- Bottom wave decoration -->
      <div class="absolute bottom-0 left-0 right-0 overflow-hidden">
        <svg class="w-full h-16 text-green-700/30" fill="currentColor" viewBox="0 0 1200 120" preserveAspectRatio="none"> <!-- Reduced height, added opacity -->
          <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity="0.25"></path>
          <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05c51.72,39.22,117.31,38.95,176.89,19.53,31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" class="opacity-40"></path>
          <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"></path>
        </svg>
      </div>
    </section>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  // Removed unused imports
  </script>
  
  <style scoped>
  /* Ensure animation delays work if not globally defined */
  .animation-delay-2000 {
    animation-delay: 2s;
  }
  .animation-delay-4000 {
    animation-delay: 4s;
  }
  /* Adjust blob animation if needed */
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
  .animate-blob {
    animation: blob 7s infinite;
  }
  </style>