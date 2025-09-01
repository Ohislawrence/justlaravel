// composables/useOnboarding.js
import { reactive, ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Create a reactive object that can be shared across components
const onboardingState = reactive({
  isActive: false,
  currentStep: 0,
  currentTour: null,
  completedTours: [],
  
  // Tours configuration for different roles
  tours: {
    examiner: [
        {
          title: 'Welcome to ExamPortal',
          content: 'Let us guide you through the main features of our platform.',
          target: 'h2.font-semibold', // Keep this simple
          position: 'bottom'
        },
        {
          title: 'Dashboard Overview',
          content: 'Here you can see your key metrics and account usage statistics.',
          target: 'a.bg-green-600', // Use a class that exists on the dashboard
          position: 'bottom'
        },
        {
          title: 'Create Your First Exam',
          content: 'Click here to create a new exam for your students.',
          target: 'a[href*="create"]', // More generic selector
          position: 'left'
        },
        {
          title: 'Exam Management',
          content: 'Access all your exams and question pools from the Exams dropdown.',
          target: '#layout-menu', // Target the entire menu
          position: 'right'
        },
        {
          title: 'Member Management',
          content: 'Add users and create groups to organize your students.',
          target: '#layout-menu', // Target the entire menu
          position: 'right'
        },
        {
          title: 'Certificate Creation',
          content: 'Design and manage certificates for your exams here.',
          target: '#layout-menu', // Target the entire menu
          position: 'right'
        }
      ],
    // ... other roles
 
    // Add tours for instructor and landlord as needed
  }
})

export function useOnboarding() {
  const page = usePage()
  const currentUserRole = computed(() => page.props.auth.user.role)
  
  // Load completed tours from localStorage
  const loadCompletedTours = () => {
    console.log('Loading completed tours...')
    const stored = localStorage.getItem('completedOnboardingTours')
    if (stored) {
      onboardingState.completedTours = JSON.parse(stored)
      console.log('Completed tours:', onboardingState.completedTours)
    }
    
    // Check for paused tour
    const paused = sessionStorage.getItem('pausedOnboarding')
    if (paused) {
      console.log('Found paused tour:', paused)
      const { tour, step } = JSON.parse(paused)
      // Small delay to ensure page is loaded
      setTimeout(() => {
        onboardingState.currentTour = tour
        onboardingState.currentStep = step
        onboardingState.isActive = true
      }, 500)
    }
  }
  
  // Check if user has completed onboarding for their role
  const hasCompletedTour = (role) => {
    const completed = onboardingState.completedTours.includes(role)
    console.log(`Has user completed ${role} tour?`, completed)
    return completed
  }
  
  const startTour = (role) => {
    console.log('Starting tour for role:', role)
    onboardingState.isActive = true
    onboardingState.currentTour = role
    onboardingState.currentStep = 0
  }
  
  const nextStep = () => {
    if (onboardingState.currentStep < onboardingState.tours[onboardingState.currentTour].length - 1) {
      onboardingState.currentStep++
    } else {
      completeTour()
    }
  }
  
  const prevStep = () => {
    if (onboardingState.currentStep > 0) {
      onboardingState.currentStep--
    }
  }
  
  const completeTour = () => {
    console.log('Completing tour:', onboardingState.currentTour)
    onboardingState.completedTours.push(onboardingState.currentTour)
    onboardingState.isActive = false
    onboardingState.currentTour = null
    onboardingState.currentStep = 0
    
    // Save to localStorage
    localStorage.setItem('completedOnboardingTours', JSON.stringify(onboardingState.completedTours))
  }
  
  const skipTour = () => {
    console.log('Skipping tour')
    onboardingState.isActive = false
    onboardingState.currentTour = null
    onboardingState.currentStep = 0
  }
  
  const pauseTour = () => {
    // Store the current state
    sessionStorage.setItem('pausedOnboarding', JSON.stringify({
      tour: onboardingState.currentTour,
      step: onboardingState.currentStep
    }))
    onboardingState.isActive = false
  }
  
  const resumeTour = () => {
    const paused = sessionStorage.getItem('pausedOnboarding')
    if (paused) {
      const { tour, step } = JSON.parse(paused)
      onboardingState.currentTour = tour
      onboardingState.currentStep = step
      onboardingState.isActive = true
      sessionStorage.removeItem('pausedOnboarding')
    }
  }
  
  // Watch for page changes to pause/resume tour
  watch(() => page.url, (newUrl, oldUrl) => {
    if (onboardingState.isActive && newUrl !== oldUrl) {
      pauseTour()
      
      // Resume after navigation is complete
      setTimeout(() => {
        resumeTour()
      }, 300)
    }
  })
  
  return {
    onboardingState,
    currentUserRole,
    loadCompletedTours,
    hasCompletedTour,
    startTour,
    nextStep,
    prevStep,
    completeTour,
    skipTour,
    pauseTour,
    resumeTour
  }
}