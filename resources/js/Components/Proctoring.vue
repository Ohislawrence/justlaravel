<template>
    <div v-if="proctoringActive" class="proctoring-overlay">
      <div class="proctoring-indicator" :class="{ 'warning-mode': warningOnly, 'delayed-mode': delayViolationReporting }">
        <span class="status-dot" :class="{ 'active': isMonitoring }"></span>
        <span class="indicator-text">
          {{ getIndicatorText }}
          <span v-if="violationCount > 0" class="violation-count">({{ violationCount }})</span>
        </span>
        <button v-if="showDetails" class="info-btn" @click="toggleDetails">ℹ️</button>
      </div>
      
      <div v-if="showDetails" class="proctoring-details">
        <h4>Proctoring Status</h4>
        <p>Mode: {{ getModeText }}</p>
        <p>Violations detected: {{ violationCount }}</p>
        <p v-if="delayViolationReporting" class="storage-info">
          <small>Violations are stored locally and will be submitted with your quiz</small>
        </p>
        <div v-if="recentViolations.length > 0">
          <h5>Recent violations:</h5>
          <ul>
            <li v-for="(violation, index) in recentViolations" :key="index">
              {{ formatViolationType(violation.type) }} - {{ formatTime(violation.timestamp) }}
            </li>
          </ul>
        </div>
        <button class="close-btn" @click="showDetails = false">Close</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onBeforeUnmount, watch, computed } from 'vue'
  import FingerprintJS from '@fingerprintjs/fingerprintjs'
  
  // Props
  const props = defineProps({
    quizAttemptId: {
      type: [String, Number],
      required: true
    },
    isActive: {
      type: Boolean,
      default: false
    },
    warningOnly: {
      type: Boolean,
      default: false
    },
    delayViolationReporting: {
      type: Boolean,
      default: false
    },
    preventCheating: {
      type: Boolean,
      default: true
    }
  })
  
  // Emits
  const emit = defineEmits([
    'violation-detected', 
    'violations-ready', 
    'cheating-prevented',
    'fingerprint-generated',
    'proctoring-data-update'
  ])
  
  // Reactive data
  const proctoringActive = ref(props.isActive)
  const isMonitoring = ref(true)
  const fingerprint = ref(null)
  const fingerprintComponents = ref(null)
  const violationCount = ref(0)
  const recentViolations = ref([])
  const showDetails = ref(false)
  const pendingViolations = ref([]) // Store violations for delayed reporting
  
  // Store cleanup functions instead of overriding global events
  let cleanupFunctions = []
  
  // Computed properties
  const getIndicatorText = computed(() => {
    if (props.warningOnly) return 'Monitoring'
    if (props.delayViolationReporting) return 'Proctoring (Local)'
    return 'Proctoring Active'
  })
  
  const getModeText = computed(() => {
    if (props.warningOnly) return 'Warning Only'
    if (props.delayViolationReporting) return 'Delayed Reporting'
    return 'Full Monitoring'
  })
  
  // Watch for changes in props
  watch(() => props.isActive, (newVal) => {
    proctoringActive.value = newVal
    if (newVal) {
      initializeProctoring()
    } else {
      cleanup()
    }
  })
  
  watch(() => props.warningOnly, (newVal) => {
    console.log(`Warning only mode: ${newVal}`)
  })
  
  watch(() => props.delayViolationReporting, (newVal) => {
    console.log(`Delayed violation reporting: ${newVal}`)
    // If switching from delayed to immediate, send all pending violations
    if (!newVal && pendingViolations.value.length > 0) {
      sendPendingViolations()
    }
  })
  
  // Active prevention methods
  const preventCopyPaste = () => {
    const handleCopy = (e) => {
      if (props.preventCheating) {
        e.preventDefault()
        emit('cheating-prevented', 'copy')
        showWarning('Copying is disabled during the quiz')
      }
      recordViolation('copy_attempt', { 
        selection: window.getSelection().toString(),
        timestamp: new Date().toISOString()
      })
    }
  
    const handleCut = (e) => {
      if (props.preventCheating) {
        e.preventDefault()
        emit('cheating-prevented', 'cut')
        showWarning('Cutting is disabled during the quiz')
      }
      recordViolation('cut_attempt', { 
        selection: window.getSelection().toString(),
        timestamp: new Date().toISOString()
      })
    }
  
    const handlePaste = (e) => {
      if (props.preventCheating) {
        e.preventDefault()
        emit('cheating-prevented', 'paste')
        showWarning('Pasting is disabled during the quiz')
      }
      recordViolation('paste_attempt', { 
        timestamp: new Date().toISOString()
      })
    }
  
    document.addEventListener('copy', handleCopy)
    document.addEventListener('cut', handleCut)
    document.addEventListener('paste', handlePaste)
  
    cleanupFunctions.push(() => {
      document.removeEventListener('copy', handleCopy)
      document.removeEventListener('cut', handleCut)
      document.removeEventListener('paste', handlePaste)
    })
  }
  
  const preventContextMenu = () => {
    const handleContextMenu = (e) => {
      if (props.preventCheating) {
        e.preventDefault()
        emit('cheating-prevented', 'right_click')
        showWarning('Right-click is disabled during the quiz')
      }
      recordViolation('right_click', { 
        timestamp: new Date().toISOString(),
        x: e.clientX,
        y: e.clientY
      })
    }
  
    document.addEventListener('contextmenu', handleContextMenu)
    cleanupFunctions.push(() => {
      document.removeEventListener('contextmenu', handleContextMenu)
    })
  }
  
  const preventDevTools = () => {
    const handleKeyDown = (e) => {
      // Prevent F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, Ctrl+U
      const devToolsShortcuts = [
        { key: 'F12', prevent: true },
        { ctrl: true, shift: true, key: 'I', prevent: true },
        { ctrl: true, shift: true, key: 'J', prevent: true },
        { ctrl: true, shift: true, key: 'C', prevent: true },
        { ctrl: true, key: 'U', prevent: true }
      ]
  
      const isDevToolShortcut = devToolsShortcuts.some(combo => {
        return (!combo.ctrl || e.ctrlKey) &&
               (!combo.shift || e.shiftKey) &&
               e.key.toLowerCase() === combo.key.toLowerCase()
      })
  
      if (isDevToolShortcut && props.preventCheating) {
        e.preventDefault()
        emit('cheating-prevented', 'dev_tools')
        showWarning('Developer tools are disabled during the quiz')
      }
  
      // Log forbidden key combinations but don't prevent them (except dev tools)
      const forbiddenKeys = [
        { ctrl: true, key: 'a' },
        { ctrl: true, key: 'c' },
        { ctrl: true, key: 'v' },
        { ctrl: true, key: 'x' }
      ]
  
      const isForbidden = forbiddenKeys.some(combo => {
        return (!combo.ctrl || e.ctrlKey) &&
               (!combo.shift || e.shiftKey) &&
               e.key.toLowerCase() === combo.key.toLowerCase()
      })
  
      if (isForbidden) {
        recordViolation('forbidden_key_combination', { 
          key: e.key,
          ctrlKey: e.ctrlKey,
          shiftKey: e.shiftKey,
          altKey: e.altKey,
          timestamp: new Date().toISOString()
        })
      }
    }
  
    document.addEventListener('keydown', handleKeyDown)
    cleanupFunctions.push(() => {
      document.removeEventListener('keydown', handleKeyDown)
    })
  }
  
  const enforceFullscreen = () => {
    const handleFullscreenChange = () => {
      if (!document.fullscreenElement && proctoringActive.value) {
        if (props.preventCheating) {
          activateFullscreen()
          showWarning('Please stay in fullscreen mode during the quiz')
        }
        recordViolation('fullscreen_exit', { 
          timestamp: new Date().toISOString()
        })
      }
    }
  
    document.addEventListener('fullscreenchange', handleFullscreenChange)
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange)
    document.addEventListener('mozfullscreenchange', handleFullscreenChange)
    document.addEventListener('MSFullscreenChange', handleFullscreenChange)
  
    cleanupFunctions.push(() => {
      document.removeEventListener('fullscreenchange', handleFullscreenChange)
      document.removeEventListener('webkitfullscreenchange', handleFullscreenChange)
      document.removeEventListener('mozfullscreenchange', handleFullscreenChange)
      document.removeEventListener('MSFullscreenChange', handleFullscreenChange)
    })
  }
  
  const monitorTabSwitch = () => {
    let isTabActive = true
    
    const handleVisibilityChange = () => {
      if (document.hidden && isTabActive) {
        isTabActive = false
        if (props.preventCheating) {
          showWarning('Please do not switch tabs during the quiz')
        }
        recordViolation('tab_switch', { 
          timestamp: new Date().toISOString(),
          visibilityState: document.visibilityState
        })
      } else if (!document.hidden) {
        isTabActive = true
      }
    }
  
    const handleBlur = () => {
      if (isTabActive) {
        if (props.preventCheating) {
          showWarning('Please stay focused on the quiz window')
        }
        recordViolation('window_blur', { 
          timestamp: new Date().toISOString()
        })
      }
    }
  
    document.addEventListener('visibilitychange', handleVisibilityChange)
    window.addEventListener('blur', handleBlur)
  
    cleanupFunctions.push(() => {
      document.removeEventListener('visibilitychange', handleVisibilityChange)
      window.removeEventListener('blur', handleBlur)
    })
  }
  
  const recordViolation = async (violationType, violationData = {}) => {
    try {
      violationCount.value++
      
      // Store recent violation for display
      recentViolations.value.unshift({
        type: violationType,
        timestamp: violationData.timestamp || new Date().toISOString()
      })
      
      // Keep only the 5 most recent violations
      if (recentViolations.value.length > 5) {
        recentViolations.value.pop()
      }
      
      // Create violation object
      const violation = {
        type: violationType, 
        data: violationData, 
        count: violationCount.value,
        warningOnly: props.warningOnly,
        timestamp: new Date().toISOString()
      }
      
      emit('violation-detected', violation)
      
      if (props.delayViolationReporting) {
        // Store violation for later submission
        pendingViolations.value.push(violation)
        saveViolationsToStorage()
      } else if (!props.warningOnly) {
        // Send violation immediately to parent
        emit('proctoring-data-update', {
          type: 'violation',
          data: {
            quiz_attempt_id: props.quizAttemptId,
            violation_type: violationType,
            violation_data: {
              ...violationData,
              fingerprint: fingerprint.value,
              url: window.location.href
            }
          }
        })
      }
    } catch (error) {
      console.error('Error recording violation:', error)
    }
  }
  
  const sendPendingViolations = async () => {
    if (pendingViolations.value.length === 0) return
    
    try {
      // Prepare violations for sending
      const violationsToSend = pendingViolations.value.map(violation => ({
        quiz_attempt_id: props.quizAttemptId,
        violation_type: violation.type,
        violation_data: {
          ...violation.data,
          fingerprint: fingerprint.value,
          url: window.location.href,
          delayed: true
        }
      }))
  
      // Send all violations to parent component
      emit('proctoring-data-update', {
        type: 'violations_batch',
        data: {
          violations: violationsToSend
        }
      })
      
      console.log(`Successfully sent ${pendingViolations.value.length} violations to parent`)
      pendingViolations.value = []
      localStorage.removeItem(`proctoring_violations_${props.quizAttemptId}`)
    } catch (error) {
      console.error('Error sending pending violations:', error)
    }
  }
  
  // Method to be called when quiz is submitted
  const submitViolations = async () => {
    if (props.delayViolationReporting && pendingViolations.value.length > 0) {
      await sendPendingViolations()
    }
    
    // Send final proctoring data to parent
    emit('proctoring-data-update', {
      type: 'final_data',
      data: {
        quiz_attempt_id: props.quizAttemptId,
        violation_count: violationCount.value,
        fingerprint: fingerprint.value,
        fingerprint_components: fingerprintComponents.value,
        fingerprint_recorded_at: new Date().toISOString(),
        proctoring_data: {
          violations: recentViolations.value,
          total_violations: violationCount.value
        }
      }
    })
  }
  
  // Expose the submitViolations method to parent component
  defineExpose({
    submitViolations,
    getProctoringData: () => ({
      violation_count: violationCount.value,
      fingerprint: fingerprint.value,
      fingerprint_components: fingerprintComponents.value,
      violations: recentViolations.value
    })
  })
  
  const showWarning = (message) => {
    const warning = document.createElement('div')
    warning.className = 'proctoring-warning-message'
    warning.innerHTML = `⚠️ ${message}`
    warning.style.cssText = `
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(220, 53, 69, 0.9);
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      font-size: 16px;
      z-index: 10000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      animation: fadeInOut 3s ease-in-out;
    `
    
    document.body.appendChild(warning)
    
    setTimeout(() => {
      if (warning.parentNode) {
        warning.parentNode.removeChild(warning)
      }
    }, 3000)
  }
    
  const showSubtleWarning = (violationType) => {
    const existingIndicator = document.querySelector('.proctoring-subtle-warning')
    if (existingIndicator) {
      existingIndicator.remove()
    }
  
    const warning = document.createElement('div')
    warning.className = 'proctoring-subtle-warning'
    warning.innerHTML = `⚠️ ${formatViolationType(violationType)} detected`
    warning.style.cssText = `
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: rgba(220, 53, 69, 0.1);
      color: #dc3545;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 12px;
      z-index: 9999;
      border: 1px solid #dc3545;
      opacity: 0;
      animation: fadeInOut 4s ease-in-out;
    `
    
    document.body.appendChild(warning)
    
    setTimeout(() => {
      if (warning.parentNode) {
        warning.parentNode.removeChild(warning)
      }
    }, 4000)
  }
  
  const formatViolationType = (type) => {
    const types = {
      'copy_attempt': 'Copy attempt',
      'cut_attempt': 'Cut attempt',
      'paste_attempt': 'Paste attempt',
      'tab_switch': 'Tab switch',
      'window_blur': 'Window blur',
      'fullscreen_exit': 'Fullscreen exit',
      'right_click': 'Right click',
      'forbidden_key_combination': 'Keyboard shortcut',
      'multiple_sessions_detected': 'Multiple sessions'
    }
    
    return types[type] || type
  }
  
  const formatTime = (timestamp) => {
    return new Date(timestamp).toLocaleTimeString()
  }
  
  const generateFingerprint = async () => {
    try {
      const fp = await FingerprintJS.load()
      const result = await fp.get()
      fingerprint.value = result.visitorId
      fingerprintComponents.value = result.components
      
      // Send fingerprint data to parent component
      emit('fingerprint-generated', {
        quiz_attempt_id: props.quizAttemptId,
        fingerprint: result.visitorId,
        components: result.components,
        recorded_at: new Date().toISOString()
      })
      
      // Also send via proctoring data update
      emit('proctoring-data-update', {
        type: 'fingerprint',
        data: {
          quiz_attempt_id: props.quizAttemptId,
          fingerprint: result.visitorId,
          fingerprint_components: result.components,
          fingerprint_recorded_at: new Date().toISOString()
        }
      })
    } catch (error) {
      console.error('Error generating fingerprint:', error)
    }
  }
  
  const activateFullscreen = () => {
    if (!props.warningOnly) {
      const elem = document.documentElement
      if (elem.requestFullscreen) {
        elem.requestFullscreen().catch(err => {
          console.warn('Could not activate fullscreen:', err)
        })
      }
    }
  }
  
  const toggleDetails = () => {
    showDetails.value = !showDetails.value
  }
  
  // Save violations to localStorage
  const saveViolationsToStorage = () => {
    if (props.delayViolationReporting && pendingViolations.value.length > 0) {
      localStorage.setItem(`proctoring_violations_${props.quizAttemptId}`, 
        JSON.stringify(pendingViolations.value))
    }
  }
  
  // Load violations from localStorage
  const loadViolationsFromStorage = () => {
    if (props.delayViolationReporting) {
      const storedViolations = localStorage.getItem(`proctoring_violations_${props.quizAttemptId}`)
      if (storedViolations) {
        try {
          pendingViolations.value = JSON.parse(storedViolations)
          violationCount.value = pendingViolations.value.length
        } catch (e) {
          console.error('Error loading violations from storage:', e)
        }
      }
    }
  }
  
  // Cleanup function
  const cleanup = () => {
    cleanupFunctions.forEach(cleanupFn => cleanupFn())
    cleanupFunctions = []
    saveViolationsToStorage()
  }
  
  // Initialize proctoring
  const initializeProctoring = async () => {
    if (!proctoringActive.value) return
  
    try {
      loadViolationsFromStorage()
      await generateFingerprint()
      activateFullscreen()
      
      // Active prevention methods
      preventCopyPaste()
      preventContextMenu()
      preventDevTools()
      enforceFullscreen()
      monitorTabSwitch()
      
      // Set up periodic saving of violations
      const saveInterval = setInterval(saveViolationsToStorage, 30000)
      cleanupFunctions.push(() => clearInterval(saveInterval))
      
    } catch (error) {
      console.error('Error initializing proctoring:', error)
    }
  }
  
  // Lifecycle hooks
  onMounted(() => {
    if (proctoringActive.value) {
      initializeProctoring()
    }
    
    window.addEventListener('beforeunload', saveViolationsToStorage)
    cleanupFunctions.push(() => {
      window.removeEventListener('beforeunload', saveViolationsToStorage)
    })
  })
  
  onBeforeUnmount(() => {
    cleanup()
  })
  </script>
  
  <style scoped>
  .proctoring-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9999;
  }
  
  .proctoring-indicator {
    position: fixed;
    top: 10px;
    left: 10px;
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
    z-index: 10000;
    border: 1px solid #dc3545;
    pointer-events: auto;
    transition: all 0.3s ease;
  }
  
  .proctoring-indicator.warning-mode {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    border: 1px solid #ffc107;
  }
  
  .proctoring-indicator.delayed-mode {
    background: rgba(0, 123, 255, 0.1);
    color: #007bff;
    border: 1px solid #007bff;
  }
  
  .indicator-text {
    display: flex;
    align-items: center;
    gap: 5px;
  }
  
  .violation-count {
    font-weight: bold;
  }
  
  .info-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    padding: 0;
    margin: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
  
  .info-btn:hover {
    background: rgba(0, 0, 0, 0.1);
  }
  
  .proctoring-details {
    position: fixed;
    top: 50px;
    left: 10px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 10001;
    pointer-events: auto;
    max-width: 300px;
  }
  
  .proctoring-details h4, .proctoring-details h5 {
    margin: 0 0 10px 0;
  }
  
  .proctoring-details ul {
    margin: 5px 0;
    padding-left: 20px;
  }
  
  .proctoring-details li {
    font-size: 11px;
    margin-bottom: 3px;
  }
  
  .storage-info {
    color: #6c757d;
    font-style: italic;
    margin: 5px 0;
  }
  
  .close-btn {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 3px;
    padding: 3px 8px;
    font-size: 11px;
    cursor: pointer;
    margin-top: 10px;
  }
  
  .close-btn:hover {
    background: #e9ecef;
  }
  
  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #6c757d;
  }
  
  .status-dot.active {
    background: #28a745;
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
  }
  
  @keyframes fadeInOut {
    0% { opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { opacity: 0; }
  }
  
  .proctoring-subtle-warning {
    animation: fadeInOut 4s ease-in-out;
  }
  </style>