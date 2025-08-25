<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Proctoring Violations</h3>
        <p class="text-sm text-gray-500 mt-1">Monitoring of exam integrity violations</p>
      </div>
      
      <div class="p-6">
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-red-800">Total Violations</p>
            <p class="text-2xl font-bold text-red-900">{{ totalViolations }}</p>
          </div>
          <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-yellow-800">Tab Switch Violations</p>
            <p class="text-2xl font-bold text-yellow-900">{{ tabSwitchCount }}</p>
          </div>
          <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-blue-800">Other Violations</p>
            <p class="text-2xl font-bold text-blue-900">{{ otherViolationsCount }}</p>
          </div>
        </div>
  
        <!-- Violations Timeline -->
        <div v-if="violations.length > 0" class="mb-6">
          <h4 class="text-md font-medium mb-3">Violation Timeline</h4>
          <div class="space-y-3">
            <div v-for="(violation, index) in sortedViolations" :key="index" 
                 class="flex items-start p-3 border border-gray-200 rounded-lg">
              <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full"
                      :class="getViolationIconClass(violation.type)">
                  <span class="text-xs font-medium text-white">
                    {{ getViolationAbbreviation(violation.type) }}
                  </span>
                </span>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">
                  {{ formatViolationType(violation.type) }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ formatDateTime(violation.timestamp) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                  Attempt #{{ violation.attempt_number }} by {{ violation.user_name }}
                </p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Violation Types Breakdown -->
        <div v-if="violationTypes.length > 0" class="mb-6">
          <h4 class="text-md font-medium mb-3">Violation Types</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div v-for="(type, index) in violationTypes" :key="index" 
                 class="p-3 border border-gray-200 rounded-lg">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-900">
                  {{ formatViolationType(type.type) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ type.count }}
                </span>
              </div>
            </div>
          </div>
        </div>
  
        <div v-if="violations.length === 0" class="text-center py-8 text-gray-500">
          No proctoring violations detected
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref,computed } from 'vue';
  
  const props = defineProps({
    attempts: {
      type: [Array, Object],
      required: true,
      default: () => [],
    },
  });
  
  // Extract attempts array from the props
  const attemptsArray = computed(() => {
    if (Array.isArray(props.attempts)) {
      return props.attempts;
    } else if (props.attempts && Array.isArray(props.attempts.data)) {
      return props.attempts.data;
    }
    return [];
  });
  
  // Debug information
  const attemptsWithProctoringData = computed(() => {
    return attemptsArray.value.filter(attempt => attempt.proctoring_data).length;
  });
  
  const sampleProctoringData = computed(() => {
    const attemptWithData = attemptsArray.value.find(attempt => attempt.proctoring_data);
    return attemptWithData ? attemptWithData.proctoring_data.substring(0, 100) + '...' : 'None';
  });
  
  // Parse proctoring_data from attempts
  const parsedViolations = computed(() => {
    const allViolations = [];
    
    attemptsArray.value.forEach(attempt => {
      // Check if proctoring_data exists and try to parse it
      if (attempt.proctoring_data) {
        try {
          // Try to parse as JSON
          const violations = JSON.parse(attempt.proctoring_data);
          if (Array.isArray(violations)) {
            violations.forEach(violation => {
              // Add attempt information to each violation for context
              allViolations.push({
                ...violation,
                attempt_id: attempt.id,
                user_id: attempt.user_id,
                user_name: attempt.user?.name || 'Unknown User',
                attempt_number: attempt.attempt_number,
                score: attempt.score,
                percentage: attempt.percentage
              });
            });
          }
        } catch (error) {
          console.error('Failed to parse proctoring_data:', error, attempt.proctoring_data);
        }
      }
    });
    
    return allViolations;
  });
  
  // Get all violations from all attempts
  const violations = computed(() => {
    return parsedViolations.value;
  });
  
  // Get violation types with counts
  const violationTypes = computed(() => {
    const typeCounts = {};
    
    violations.value.forEach(violation => {
      if (!typeCounts[violation.type]) {
        typeCounts[violation.type] = 0;
      }
      typeCounts[violation.type]++;
    });
    
    return Object.entries(typeCounts).map(([type, count]) => ({
      type,
      count
    })).sort((a, b) => b.count - a.count);
  });
  
  // Total violations count
  const totalViolations = computed(() => violations.value.length);
  
  // Tab switch violations count
  const tabSwitchCount = computed(() => {
    return violations.value.filter(v => v.type === 'tab_switch').length;
  });
  
  // Other violations count
  const otherViolationsCount = computed(() => {
    return violations.value.filter(v => v.type !== 'tab_switch').length;
  });
  
  // Violations sorted by timestamp (newest first)
  const sortedViolations = computed(() => {
    return [...violations.value].sort((a, b) => {
      return new Date(b.timestamp) - new Date(a.timestamp);
    });
  });
  
  // Helper methods
  const formatViolationType = (type) => {
    const types = {
      'tab_switch': 'Tab Switch',
      'face_not_visible': 'Face Not Visible',
      'multiple_faces': 'Multiple Faces Detected',
      'voice_detected': 'Voice Detected',
      'mobile_phone': 'Mobile Phone Detected',
      'person_left': 'Person Left Camera View',
      'paste_attempt': 'Paste Attempt',
      'copy_attempt': 'Copy Attempt',
      'forbidden_key_combination': 'Forbidden Key Combination',
      // Add more violation types as needed
    };
    
    return types[type] || type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
  };
  
  const getViolationIconClass = (type) => {
    const classes = {
      'tab_switch': 'bg-red-500',
      'face_not_visible': 'bg-yellow-500',
      'multiple_faces': 'bg-orange-500',
      'voice_detected': 'bg-purple-500',
      'mobile_phone': 'bg-blue-500',
      'person_left': 'bg-indigo-500',
      'paste_attempt': 'bg-pink-500',
      'copy_attempt': 'bg-teal-500',
      'forbidden_key_combination': 'bg-cyan-500',
    };
    
    return classes[type] || 'bg-gray-500';
  };
  
  const getViolationAbbreviation = (type) => {
    const abbreviations = {
      'tab_switch': 'TS',
      'face_not_visible': 'FNV',
      'multiple_faces': 'MF',
      'voice_detected': 'VD',
      'mobile_phone': 'MP',
      'person_left': 'PL',
      'paste_attempt': 'PA',
      'copy_attempt': 'CA',
      'forbidden_key_combination': 'FK',
    };
    
    return abbreviations[type] || type.substring(0, 2).toUpperCase();
  };
  
  const formatDateTime = (timestamp) => {
    if (!timestamp) return 'Unknown time';
    
    try {
      const date = new Date(timestamp);
      return date.toLocaleString();
    } catch (error) {
      return timestamp;
    }
  };
  
  // For debugging
  const debugInfo = ref(true);
  </script>