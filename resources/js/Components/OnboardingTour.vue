<!-- Components/OnboardingTour.vue -->
<template>
    <div v-if="onboardingState.isActive" class="fixed inset-0 z-[10050] overflow-hidden">
      <!-- Overlay with reduced opacity and skip instruction -->
      <div 
        class="absolute inset-0 bg-black transition-opacity duration-300"
        :class="overlayClass"
        @click="handleOverlayClick"
      >
        <div class="absolute bottom-6 right-6">
          <button
            @click.stop="skipTour"
            class="bg-white/90 hover:bg-white text-gray-700 hover:text-gray-900 px-4 py-2 rounded-lg shadow-lg text-sm font-medium transition-all duration-200 flex items-center space-x-2 border border-gray-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>Skip Tutorial</span>
          </button>
        </div>
      </div>
  
      <!-- Highlight element with smooth transitions -->
      <div 
        v-if="currentStepElement && !isLoading"
        class="absolute border-3 border-blue-400 rounded-lg shadow-2xl z-10 transition-all duration-500 ease-out bg-white/5 backdrop-blur-sm"
        :style="highlightStyle"
      >
        <div class="absolute -top-7 -left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full shadow-md">
          <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          Focus
        </div>
      </div>
  
      <!-- Tooltip Container -->
      <div 
        v-if="currentStepConfig && !isLoading"
        class="absolute bg-white rounded-xl shadow-2xl z-20 p-6 max-w-sm border border-gray-100 transform transition-all duration-300 ease-out"
        :class="tooltipAnimationClass"
        :style="tooltipStyle"
      >
        <!-- Close button -->
        <button
          @click="skipTour"
          class="absolute -top-3 -right-3 bg-red-500 hover:bg-red-600 text-white p-1 rounded-full shadow-md transition-colors duration-200"
          aria-label="Close tutorial"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
  
        <!-- Content -->
        <div class="mb-4">
          <h3 class="font-semibold text-lg text-gray-900 mb-2 flex items-center">
            <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            {{ currentStepConfig.title }}
          </h3>
          <p class="text-gray-600 text-sm leading-relaxed">{{ currentStepConfig.content }}</p>
        </div>
        
        <!-- Navigation and Progress -->
        <div class="flex items-center justify-between space-x-4">
          <!-- Progress indicator -->
          <div class="flex-1">
            <div class="flex space-x-1 mb-2">
              <div 
                v-for="(step, index) in tourSteps" 
                :key="step.id"
                class="h-1 rounded-full flex-1 transition-all duration-300"
                :class="index < onboardingState.currentStep 
                  ? 'bg-green-400' 
                  : index === onboardingState.currentStep 
                  ? 'bg-blue-500' 
                  : 'bg-gray-200'"
              ></div>
            </div>
            <div class="text-xs text-gray-500">
              Step {{ onboardingState.currentStep + 1 }} of {{ tourSteps.length }}
            </div>
          </div>
  
          <!-- Navigation buttons -->
          <div class="flex space-x-2">
            <button 
              v-if="onboardingState.currentStep > 0"
              @click="prevStep" 
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-all duration-200 border border-gray-300 rounded-lg hover:border-gray-400 active:scale-95 flex items-center"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Back
            </button>
            
            <button 
              @click="nextStep" 
              class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 active:scale-95 flex items-center font-medium shadow-md hover:shadow-lg"
            >
              {{ onboardingState.currentStep === tourSteps.length - 1 ? 'Finish' : 'Next' }}
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
      </div>
  
      <!-- Loading state -->
      <div 
        v-if="isLoading"
        class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-30"
      >
        <div class="bg-white rounded-lg p-6 shadow-lg flex items-center space-x-3">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
          <span class="text-gray-600 text-sm">Loading next step...</span>
        </div>
      </div>
  
      <!-- Error state -->
      <div 
        v-if="error && !isLoading"
        class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-30 bg-white p-6 rounded-xl shadow-xl max-w-md border-2 border-red-200"
      >
        <div class="text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Oops! Something went wrong</h3>
          <p class="text-gray-600 text-sm mb-4">
            We couldn't load this part of the tutorial. You can continue to the next step or skip the tutorial.
          </p>
          <div class="flex space-x-3 justify-center">
            <button 
              @click="retryCurrentStep"
              class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Try Again
            </button>
            <button 
              @click="nextStep"
              class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors"
            >
              Continue
            </button>
            <button 
              @click="skipTour"
              class="px-4 py-2 text-sm text-red-600 hover:text-red-700 transition-colors"
            >
              Skip Tutorial
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { useOnboarding } from '@/composables/useOnboarding'
  import { computed, ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
  
  const { 
    onboardingState, 
    nextStep, 
    prevStep, 
    skipTour,
    error
  } = useOnboarding()
  
  const currentStepElement = ref(null)
  const isLoading = ref(false)
  const tooltipDirection = ref('enter')
  const retryCount = ref(0)
  
  const tourSteps = computed(() => {
    if (!onboardingState.currentTour) return []
    return onboardingState.tours[onboardingState.currentTour] || []
  })
  
  const currentStepConfig = computed(() => {
    if (!tourSteps.value.length) return null
    return tourSteps.value[onboardingState.currentStep]
  })
  
  const overlayClass = computed(() => ({
    'bg-opacity-25': currentStepElement.value && !isLoading.value,
    'bg-opacity-30': !currentStepElement.value || isLoading.value,
    'cursor-pointer': true,
    'pointer-events-auto': true
  }))
  
  const tooltipAnimationClass = computed(() => ({
    'scale-95 opacity-0': tooltipDirection.value === 'enter',
    'scale-100 opacity-100': tooltipDirection.value === 'entered',
    'scale-95 opacity-0': tooltipDirection.value === 'exit'
  }))
  
  const highlightStyle = computed(() => {
    if (!currentStepElement.value) return {}
    
    const rect = currentStepElement.value.getBoundingClientRect()
    const padding = 8
    
    return {
      top: `${rect.top - padding}px`,
      left: `${rect.left - padding}px`,
      width: `${rect.width + padding * 2}px`,
      height: `${rect.height + padding * 2}px`,
    }
  })
  
  const tooltipStyle = computed(() => {
    if (!currentStepConfig.value) return {}
    
    if (currentStepElement.value) {
      const rect = currentStepElement.value.getBoundingClientRect()
      const position = currentStepConfig.value.position || 'bottom'
      const offset = 20
      const viewportPadding = 20
      
      let top, left
      
      switch (position) {
        case 'top':
          top = Math.max(viewportPadding, rect.top - offset)
          left = Math.max(viewportPadding, Math.min(window.innerWidth - viewportPadding - 320, rect.left + rect.width / 2))
          return { 
            top: `${top}px`, 
            left: `${left}px`, 
            transform: 'translate(-50%, -100%)' 
          }
        case 'bottom':
          top = Math.min(window.innerHeight - viewportPadding, rect.bottom + offset)
          left = Math.max(viewportPadding, Math.min(window.innerWidth - viewportPadding - 320, rect.left + rect.width / 2))
          return { 
            top: `${top}px`, 
            left: `${left}px`, 
            transform: 'translate(-50%, 0)' 
          }
        case 'left':
          top = Math.max(viewportPadding, Math.min(window.innerHeight - viewportPadding, rect.top + rect.height / 2))
          left = Math.max(viewportPadding, rect.left - offset)
          return { 
            top: `${top}px`, 
            left: `${left}px`, 
            transform: 'translate(-100%, -50%)' 
          }
        case 'right':
          top = Math.max(viewportPadding, Math.min(window.innerHeight - viewportPadding, rect.top + rect.height / 2))
          left = Math.min(window.innerWidth - viewportPadding, rect.right + offset)
          return { 
            top: `${top}px`, 
            left: `${left}px`, 
            transform: 'translate(0, -50%)' 
          }
        default:
          return {}
      }
    }
    
    // Fallback position
    return {
      top: '50%',
      left: '50%',
      transform: 'translate(-50%, -50%)'
    }
  })
  
  const handleOverlayClick = (event) => {
    // Only skip if clicking directly on overlay, not on any child elements
    if (event.target === event.currentTarget) {
      skipTour()
    }
  }
  
  const retryCurrentStep = () => {
    retryCount.value++
    findTargetElement()
  }
  
  const findTargetElement = async () => {
    if (!currentStepConfig.value?.target) {
      currentStepElement.value = null
      isLoading.value = false
      return
    }
  
    isLoading.value = true
    tooltipDirection.value = 'exit'
  
    try {
      await new Promise(resolve => setTimeout(resolve, 300)) // Wait for exit animation
      
      let element = null
      const maxAttempts = 3
      let attempt = 0
  
      const attemptFind = () => {
        attempt++
        
        // Try primary selector
        element = document.querySelector(currentStepConfig.value.target)
        
        // Try fallback selectors
        if (!element && currentStepConfig.value.fallbackSelectors) {
          for (const selector of currentStepConfig.value.fallbackSelectors) {
            element = document.querySelector(selector)
            if (element) break
          }
        }
  
        if (element) {
          currentStepElement.value = element
          isLoading.value = false
          tooltipDirection.value = 'enter'
          
          // Scroll element into view
          setTimeout(() => {
            element.scrollIntoView({ 
              behavior: 'smooth', 
              block: 'center',
              inline: 'center'
            })
          }, 100)
  
          // Animate tooltip in
          setTimeout(() => {
            tooltipDirection.value = 'entered'
          }, 50)
          
        } else if (attempt < maxAttempts) {
          setTimeout(attemptFind, 500 * attempt)
        } else {
          console.warn('Target element not found after multiple attempts:', currentStepConfig.value.target)
          currentStepElement.value = null
          isLoading.value = false
          tooltipDirection.value = 'entered'
        }
      }
  
      attemptFind()
  
    } catch (err) {
      console.error('Error finding target element:', err)
      currentStepElement.value = null
      isLoading.value = false
      tooltipDirection.value = 'entered'
    }
  }
  
  // Watch for step changes
  watch(() => onboardingState.currentStep, (newStep, oldStep) => {
    if (newStep !== oldStep) {
      retryCount.value = 0
      findTargetElement()
    }
  })
  
  watch(() => onboardingState.isActive, (active) => {
    if (active) {
      retryCount.value = 0
      setTimeout(findTargetElement, 100)
    } else {
      currentStepElement.value = null
    }
  })
  
  watch(() => error.value, (newError) => {
    if (newError) {
      console.error('Onboarding error:', newError)
    }
  })
  
  onMounted(() => {
    if (onboardingState.isActive) {
      setTimeout(findTargetElement, 100)
    }
  })
  
  onUnmounted(() => {
    currentStepElement.value = null
  })
  </script>
  
  <style scoped>
  /* Smooth transitions for all interactive elements */
  button {
    transition: all 0.2s ease-in-out;
  }
  
  button:active {
    transform: scale(0.98);
  }
  
  /* Ensure high z-index and visibility */
  .fixed {
    z-index: 10050 !important;
  }
  
  .absolute {
    z-index: 10060 !important;
  }
  
  /* Custom scrollbar for better UX */
  ::-webkit-scrollbar {
    width: 6px;
  }
  
  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }
  
  ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
  }
  
  /* Focus states for accessibility */
  button:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
  }
  
  /* Reduced motion support */
  @media (prefers-reduced-motion: reduce) {
    * {
      animation-duration: 0.01ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.01ms !important;
    }
  }
  </style>