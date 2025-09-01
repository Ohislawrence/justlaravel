// composables/useOnboarding.js
import { reactive, ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Create a reactive object that can be shared across components
const onboardingState = reactive({
  isActive: false,
  currentStep: 0,
  currentTour: null,
  completedTours: [],
  isLoading: false,
  
  // Tours configuration for different roles
  tours: {
    examiner: [
        {
          title: 'Welcome to ExamPortal',
          content: 'Let us guide you through the main features of our platform.',
          target: '.app-brand-text', // Use existing element
          position: 'bottom'
        },
        {
          title: 'Dashboard Overview',
          content: 'Here you can see your key metrics and account usage statistics.',
          target: '.navbar-nav-right', // Use navigation area
          position: 'bottom'
        },
        {
          title: 'Exam Management',
          content: 'Access all your exams and question pools from the Exams dropdown.',
          target: '[href="javascript:void(0)"]:has(span:contains("Exams"))', // Better selector for Exams dropdown
          position: 'right'
        },
        {
          title: 'Create New Exam',
          content: 'Click here to create a new exam for your students.',
          target: '[href*="create"]', 
          position: 'left'
        },
        {
          title: 'User Management',
          content: 'Manage users and groups from the Members dropdown.',
          target: '[href="javascript:void(0)"]:has(span:contains("Members"))',
          position: 'right'
        },
        {
          title: 'Certificate Management',
          content: 'Design and manage certificates for your exams here.',
          target: '[href*="certificate-templates"]',
          position: 'right'
        }
      ],

    examinee: [
      {
        id: 'examinee-welcome',
        title: 'Welcome Examinee!',
        content: 'This is your personal dashboard for taking exams and tracking your progress.',
        target: '.app-brand-text',
        position: 'bottom',
        fallbackSelectors: ['.navbar-brand', '.logo']
      },
      {
        id: 'examinee-dashboard',
        title: 'Your Dashboard',
        content: 'View your upcoming exams, recent results, and overall performance statistics.',
        target: '.content-wrapper',
        position: 'bottom',
        fallbackSelectors: ['.container-p-y', 'main']
      },
      {
        id: 'examinee-my-exams',
        title: 'My Exams Section',
        content: 'Access all available exams and your exam history in this section.',
        target: '[href*="examinee.quizzes"]',
        position: 'right',
        fallbackSelectors: ['[href*="quiz"]', '.menu-item:has(span:contains("My Exam"))']
      },
      {
        id: 'examinee-certificates',
        title: 'Your Certificates',
        content: 'View and download certificates for exams you have successfully completed.',
        target: '[href*="certificates"]',
        position: 'right',
        fallbackSelectors: ['[href*="certificate"]', '.menu-item:has(span:contains("Certificate"))']
      }
    ],

    instructor: [
      {
        id: 'instructor-welcome',
        title: 'Welcome Instructor!',
        content: 'Manage your courses and monitor student progress from your dedicated dashboard.',
        target: '.app-brand-text',
        position: 'bottom',
        fallbackSelectors: ['.navbar-brand', '.logo']
      },
      {
        id: 'instructor-dashboard',
        title: 'Instructor Dashboard',
        content: 'Track student performance, course statistics, and manage your teaching materials.',
        target: '.content-wrapper',
        position: 'bottom',
        fallbackSelectors: ['.container-p-y', 'main']
      },
      {
        id: 'instructor-exams',
        title: 'Course Exams',
        content: 'Create and manage exams for your courses, set deadlines, and review results.',
        target: '[href*="instructor.quizzes"]',
        position: 'right',
        fallbackSelectors: ['[href*="quiz"]', '.menu-item:has(span:contains("My Exam"))']
      }
    ],

    landlord: [
      {
        id: 'landlord-welcome',
        title: 'Welcome System Administrator',
        content: 'Manage organizations, users, subscriptions, and system-wide settings.',
        target: '.app-brand-text',
        position: 'bottom',
        fallbackSelectors: ['.navbar-brand', '.logo']
      },
      {
        id: 'landlord-dashboard',
        title: 'Admin Dashboard',
        content: 'Monitor system health, user activity, and overall platform performance.',
        target: '.content-wrapper',
        position: 'bottom',
        fallbackSelectors: ['.container-p-y', 'main']
      },
      {
        id: 'landlord-organizations',
        title: 'Organization Management',
        content: 'Create and manage organizations, assign administrators, and set permissions.',
        target: '[href*="organizations"]',
        position: 'right',
        fallbackSelectors: ['[href*="organization"]', '.menu-item:has(span:contains("Organization"))']
      },
      {
        id: 'landlord-subscriptions',
        title: 'Subscription Management',
        content: 'Manage billing plans, subscriptions, and payment processing for organizations.',
        target: '[href*="subscriptions"]',
        position: 'right',
        fallbackSelectors: ['[href*="subscription"]', '.menu-item:has(span:contains("Subscription"))']
      }
    ]
  }
})

// Error types for better error handling
const OnboardingError = {
  NO_TOUR_FOR_ROLE: 'NO_TOUR_FOR_ROLE',
  ELEMENT_NOT_FOUND: 'ELEMENT_NOT_FOUND',
  TOUR_ALREADY_COMPLETED: 'TOUR_ALREADY_COMPLETED',
  INVALID_STEP: 'INVALID_STEP'
}

export function useOnboarding() {
  const page = usePage()
  const currentUserRole = computed(() => page.props.auth.user?.role)
  const error = ref(null)

  // Load completed tours from localStorage with validation
  const loadCompletedTours = () => {
    try {
      const stored = localStorage.getItem('completedOnboardingTours')
      if (stored) {
        const parsed = JSON.parse(stored)
        if (Array.isArray(parsed)) {
          onboardingState.completedTours = parsed
          console.log('Loaded completed tours:', parsed)
        }
      }
    } catch (e) {
      console.error('Error loading completed tours:', e)
      onboardingState.completedTours = []
    }
  }

  // Save completed tours to localStorage
  const saveCompletedTours = () => {
    try {
      localStorage.setItem('completedOnboardingTours', JSON.stringify(onboardingState.completedTours))
    } catch (e) {
      console.error('Error saving completed tours:', e)
    }
  }

  // Check if user has completed onboarding for their role
  const hasCompletedTour = (role) => {
    if (!role || !onboardingState.tours[role]) {
      return false
    }
    return onboardingState.completedTours.includes(role)
  }

  // Validate if tour exists for role
  const validateTour = (role) => {
    if (!onboardingState.tours[role]) {
      error.value = {
        type: OnboardingError.NO_TOUR_FOR_ROLE,
        message: `No onboarding tour configured for role: ${role}`
      }
      return false
    }
    return true
  }

  // Start tour with validation
  const startTour = (role) => {
    if (!role) {
      role = currentUserRole.value
    }

    if (!validateTour(role)) {
      return false
    }

    if (hasCompletedTour(role)) {
      error.value = {
        type: OnboardingError.TOUR_ALREADY_COMPLETED,
        message: `Tour for role ${role} has already been completed`
      }
      return false
    }

    onboardingState.isActive = true
    onboardingState.currentTour = role
    onboardingState.currentStep = 0
    onboardingState.isLoading = true
    error.value = null

    console.log(`Starting onboarding tour for role: ${role}`)
    return true
  }

  // Navigate to next step
  const nextStep = () => {
    if (!onboardingState.currentTour || onboardingState.currentStep >= onboardingState.tours[onboardingState.currentTour].length - 1) {
      completeTour()
      return
    }

    onboardingState.currentStep++
    onboardingState.isLoading = true
  }

  // Navigate to previous step
  const prevStep = () => {
    if (onboardingState.currentStep > 0) {
      onboardingState.currentStep--
      onboardingState.isLoading = true
    }
  }

  // Complete the current tour
  const completeTour = () => {
    if (!onboardingState.currentTour) return

    console.log('Completing tour:', onboardingState.currentTour)
    
    if (!onboardingState.completedTours.includes(onboardingState.currentTour)) {
      onboardingState.completedTours.push(onboardingState.currentTour)
      saveCompletedTours()
    }

    // Send analytics event
    trackTourCompletion(onboardingState.currentTour)

    onboardingState.isActive = false
    onboardingState.currentTour = null
    onboardingState.currentStep = 0
    onboardingState.isLoading = false

    // Clear any paused state
    sessionStorage.removeItem('pausedOnboarding')
  }

  // Skip the current tour
  const skipTour = () => {
    console.log('Skipping tour:', onboardingState.currentTour)
    
    // Send analytics event
    trackTourSkipped(onboardingState.currentTour, onboardingState.currentStep)

    onboardingState.isActive = false
    onboardingState.currentTour = null
    onboardingState.currentStep = 0
    onboardingState.isLoading = false

    // Clear paused state
    sessionStorage.removeItem('pausedOnboarding')
  }

  // Pause tour (for page navigation)
  const pauseTour = () => {
    if (!onboardingState.currentTour) return

    sessionStorage.setItem('pausedOnboarding', JSON.stringify({
      tour: onboardingState.currentTour,
      step: onboardingState.currentStep,
      timestamp: Date.now()
    }))

    onboardingState.isActive = false
    onboardingState.isLoading = false
  }

  // Resume paused tour
  const resumeTour = () => {
    try {
      const paused = sessionStorage.getItem('pausedOnboarding')
      if (paused) {
        const { tour, step, timestamp } = JSON.parse(paused)
        
        // Only resume if paused recently (within 5 minutes)
        if (Date.now() - timestamp < 5 * 60 * 1000) {
          if (validateTour(tour) && step < onboardingState.tours[tour].length) {
            onboardingState.currentTour = tour
            onboardingState.currentStep = step
            onboardingState.isActive = true
            onboardingState.isLoading = true
          }
        }
        
        sessionStorage.removeItem('pausedOnboarding')
      }
    } catch (e) {
      console.error('Error resuming tour:', e)
      sessionStorage.removeItem('pausedOnboarding')
    }
  }

  // Reset onboarding for a role (for testing/admin purposes)
  const resetTour = (role) => {
    const index = onboardingState.completedTours.indexOf(role)
    if (index > -1) {
      onboardingState.completedTours.splice(index, 1)
      saveCompletedTours()
    }
    sessionStorage.removeItem('pausedOnboarding')
  }

  // Analytics tracking
  const trackTourCompletion = (role) => {
    // Implement your analytics tracking here
    console.log(`Tour completed: ${role}`)
    // Example: axios.post('/api/analytics/tour-completed', { role })
  }

  const trackTourSkipped = (role, step) => {
    // Implement your analytics tracking here
    console.log(`Tour skipped: ${role} at step ${step}`)
    // Example: axios.post('/api/analytics/tour-skipped', { role, step })
  }

  const trackTourStarted = (role) => {
    // Implement your analytics tracking here
    console.log(`Tour started: ${role}`)
    // Example: axios.post('/api/analytics/tour-started', { role })
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

  // Initialize on import
  loadCompletedTours()

  return {
    onboardingState,
    currentUserRole,
    error,
    loadCompletedTours,
    hasCompletedTour,
    startTour,
    nextStep,
    prevStep,
    completeTour,
    skipTour,
    pauseTour,
    resumeTour,
    resetTour,
    OnboardingError
  }
}