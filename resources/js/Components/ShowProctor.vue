<template>
    <div class="proctor-output-container">
      <div class="header">
        <h2>Proctoring Monitor</h2>
        <div class="status-indicators">
          <div class="status-item" :class="{ 'active': isFullscreen }">
            <span class="indicator"></span>
            Fullscreen
          </div>
          <div class="status-item" :class="{ 'active': isTabFocused }">
            <span class="indicator"></span>
            Tab Focus
          </div>
          <div class="status-item" :class="{ 'active': !hasMultipleDisplays }">
            <span class="indicator"></span>
            Single Display
          </div>
        </div>
      </div>
  
      <div class="violations-section">
        <h3>Violation Log ({{ filteredViolations.length }})</h3>
        <div class="filter-controls">
          <select v-model="filterType" class="filter-select">
            <option value="all">All Violations</option>
            <option value="fullscreen_exit">Fullscreen Exit</option>
            <option value="tab_switch">Tab Switch</option>
            <option value="copy_paste">Copy/Paste</option>
            <option value="multiple_displays">Multiple Displays</option>
            <option value="inactivity">Inactivity</option>
          </select>
          <button @click="refreshViolations" class="refresh-btn">
            <i class="fas fa-sync-alt"></i> Refresh
          </button>
        </div>
  
        <div class="violations-list">
          <div v-if="loading" class="loading">Loading violations...</div>
          <div v-else-if="filteredViolations.length === 0" class="no-violations">
            No violations detected
          </div>
          <div v-else class="violation-items">
            <div v-for="violation in filteredViolations" :key="violation.id" class="violation-item">
              <div class="violation-type" :class="getViolationClass(violation.violation_type)">
                {{ formatViolationType(violation.violation_type) }}
              </div>
              <div class="violation-time">
                {{ formatTime(violation.violation_time) }}
              </div>
              <div class="violation-details">
                <template v-if="violation.violation_data">
                  <div v-for="(value, key) in violation.violation_data" :key="key">
                    <strong>{{ key }}:</strong> {{ value }}
                  </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <div class="activity-section">
        <h3>Activity Monitor</h3>
        <div class="activity-graph">
          <canvas ref="activityChart"></canvas>
        </div>
        <div class="activity-stats">
          <div class="stat-item">
            <div class="stat-value">{{ violationsPerMinute }}</div>
            <div class="stat-label">Violations/Min</div>
          </div>
          <div class="stat-item">
            <div class="stat-value">{{ lastActivityTime }}</div>
            <div class="stat-label">Last Active</div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
  import { router } from '@inertiajs/vue3';
  import Chart from 'chart.js/auto';
  import axios from 'axios';
  
  const props = defineProps({
    attemptId: {
      type: Number,
      required: true
    },
    realtime: {
      type: Boolean,
      default: false
    },
    initialViolations: {
    type: Array,
    default: () => []
    }
  });
  
  // State
  const violations = ref(props.initialViolations);
    const loading = ref(false);
    const error = ref(null);
  const filterType = ref('all');
  const isFullscreen = ref(document.fullscreenElement !== null);
  const isTabFocused = ref(document.visibilityState === 'visible');
  const hasMultipleDisplays = ref(false);
  const activityChart = ref(null);
  const chartInstance = ref(null);
  
  // Computed properties
  const filteredViolations = computed(() => {
    if (filterType.value === 'all') return violations.value;
    return violations.value.filter(v => v.violation_type === filterType.value);
  });
  
  const violationsPerMinute = computed(() => {
    if (violations.value.length < 2) return 0;
    const firstTime = new Date(violations.value[0].violation_time).getTime();
    const lastTime = new Date(violations.value[violations.value.length - 1].violation_time).getTime();
    const minutes = (lastTime - firstTime) / (1000 * 60);
    return minutes > 0 ? (violations.value.length / minutes).toFixed(1) : 0;
  });
  
  const lastActivityTime = computed(() => {
    if (violations.value.length === 0) return 'N/A';
    const lastTime = new Date(violations.value[violations.value.length - 1].violation_time);
    return lastTime.toLocaleTimeString();
  });
  
  // Methods
  const fetchViolations = async () => {
  try {
    loading.value = true;
    error.value = null;
    const response = await axios.get(`/api/proctoring/violations/${props.attemptId}`);
    violations.value = response.data;
  } catch (err) {
    console.error('Error:', err);
    error.value = 'Failed to load violations';
  } finally {
    loading.value = false;
  }
};
  const refreshViolations = () => {
    fetchViolations();
  };
  
  const formatViolationType = (type) => {
    const types = {
      'fullscreen_exit': 'Fullscreen Exit',
      'tab_switch': 'Tab Switch',
      'copy_paste': 'Copy/Paste Attempt',
      'multiple_displays': 'Multiple Displays',
      'inactivity': 'Inactivity'
    };
    return types[type] || type;
  };
  
  const getViolationClass = (type) => {
    return `violation-${type.replace('_', '-')}`;
  };
  
  const formatTime = (timeString) => {
    return new Date(timeString).toLocaleString();
  };
  
  const checkMultipleDisplays = () => {
    hasMultipleDisplays.value = window.screen.width < window.outerWidth || window.screen.height < window.outerHeight;
  };
  
  const setupChart = () => {
    if (chartInstance.value) {
      chartInstance.value.destroy();
    }
  
    const ctx = activityChart.value.getContext('2d');
    
    // Group violations by minute
    const violationsByMinute = {};
    violations.value.forEach(v => {
      const time = new Date(v.violation_time);
      const minuteKey = `${time.getHours()}:${time.getMinutes()}`;
      violationsByMinute[minuteKey] = (violationsByMinute[minuteKey] || 0) + 1;
    });
  
    const labels = Object.keys(violationsByMinute);
    const data = Object.values(violationsByMinute);
  
    chartInstance.value = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Violations per Minute',
          data: data,
          borderColor: 'rgb(255, 99, 132)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          tension: 0.1,
          fill: true
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Violations'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Time'
            }
          }
        }
      }
    });
  };
  
  // Event listeners
  const handleFullscreenChange = () => {
    isFullscreen.value = document.fullscreenElement !== null;
  };
  
  const handleVisibilityChange = () => {
    isTabFocused.value = document.visibilityState === 'visible';
  };
  
  // Lifecycle hooks
  onMounted(async () => {
    await fetchViolations();
    checkMultipleDisplays();
    
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('visibilitychange', handleVisibilityChange);
    
    if (props.realtime) {
      // Set up realtime updates (e.g., via Echo, WebSockets, or polling)
      setInterval(fetchViolations, 30000); // Refresh every 30 seconds
    }
  });
  
  onBeforeUnmount(() => {
    document.removeEventListener('fullscreenchange', handleFullscreenChange);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    
    if (chartInstance.value) {
      chartInstance.value.destroy();
    }
  });
  onMounted(() => {
    if (violations.value.length === 0) {
        fetchViolations();
    }
    });
  
  // Watch for violations changes to update chart
  watch(violations, () => {
    if (violations.value.length > 0) {
      setupChart();
    }
  }, { deep: true });
  </script>
  
  <style scoped>
  .proctor-output-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #dee2e6;
  }
  
  .status-indicators {
    display: flex;
    gap: 15px;
  }
  
  .status-item {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #6c757d;
  }
  
  .status-item.active {
    color: #28a745;
  }
  
  .indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
    background-color: #dc3545;
  }
  
  .status-item.active .indicator {
    background-color: #28a745;
  }
  
  .violations-section {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .filter-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
  }
  
  .filter-select {
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #ced4da;
    flex-grow: 1;
  }
  
  .refresh-btn {
    padding: 8px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  
  .refresh-btn:hover {
    background-color: #0069d9;
  }
  
  .violations-list {
    max-height: 300px;
    overflow-y: auto;
  }
  
  .loading, .no-violations {
    padding: 20px;
    text-align: center;
    color: #6c757d;
  }
  
  .violation-items {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  
  .violation-item {
    padding: 12px 15px;
    border-radius: 4px;
    background-color: #f8f9fa;
    border-left: 4px solid #dc3545;
  }
  
  .violation-type {
    font-weight: bold;
    margin-bottom: 5px;
  }
  
  .violation-time {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
  }
  
  .violation-details {
    font-size: 13px;
    color: #495057;
  }
  
  .violation-fullscreen-exit {
    border-left-color: #ffc107;
  }
  
  .violation-tab-switch {
    border-left-color: #fd7e14;
  }
  
  .violation-copy-paste {
    border-left-color: #6610f2;
  }
  
  .violation-multiple-displays {
    border-left-color: #6f42c1;
  }
  
  .violation-inactivity {
    border-left-color: #20c997;
  }
  
  .activity-section {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .activity-graph {
    margin-bottom: 20px;
  }
  
  .activity-stats {
    display: flex;
    justify-content: space-around;
    text-align: center;
  }
  
  .stat-item {
    padding: 10px;
  }
  
  .stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
  }
  
  .stat-label {
    font-size: 14px;
    color: #6c757d;
  }
  </style>