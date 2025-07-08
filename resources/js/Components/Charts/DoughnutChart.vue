<template>
    <div class="chart-container relative">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
  import { Chart, registerables } from 'chart.js';
  
  // Register all Chart.js components
  Chart.register(...registerables);
  
  const props = defineProps({
    data: {
      type: Object,
      required: true,
      validator: (value) => {
        return Array.isArray(value.labels) && Array.isArray(value.datasets);
      }
    },
    options: {
      type: Object,
      default: () => ({
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            position: 'right'
          }
        }
      })
    },
    aspectRatio: {
      type: Number,
      default: 1.5 // More square than bar/line charts
    }
  });
  
  const chartCanvas = ref(null);
  let chartInstance = null;
  
  // Default colors for doughnut segments
  const defaultColors = [
    '#3B82F6', // blue-500
    '#10B981', // emerald-500
    '#F59E0B', // amber-500
    '#EF4444', // red-500
    '#8B5CF6', // violet-500
    '#EC4899', // pink-500
    '#14B8A6', // teal-500
    '#F97316'  // orange-500
  ];
  
  const initChart = () => {
    if (!chartCanvas.value) return;
  
    // Prepare datasets with default colors if not provided
    const datasets = props.data.datasets.map((dataset, index) => ({
      ...dataset,
      backgroundColor: dataset.backgroundColor || defaultColors.slice(0, props.data.labels.length),
      borderColor: dataset.borderColor || '#FFFFFF',
      borderWidth: dataset.borderWidth || 1,
      hoverOffset: dataset.hoverOffset || 8
    }));
  
    const chartData = {
      labels: props.data.labels,
      datasets
    };
  
    // Destroy previous instance if exists
    if (chartInstance) {
      chartInstance.destroy();
    }
  
    chartInstance = new Chart(chartCanvas.value, {
      type: 'doughnut',
      data: chartData,
      options: {
        ...props.options,
        aspectRatio: props.aspectRatio,
        plugins: {
          ...props.options.plugins,
          legend: {
            position: 'right',
            labels: {
              boxWidth: 12,
              padding: 20,
              usePointStyle: true,
              pointStyle: 'circle'
            },
            ...props.options.plugins?.legend
          },
          tooltip: {
            enabled: true,
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.raw || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = Math.round((value / total) * 100);
                return `${label}: ${value} (${percentage}%)`;
              }
            }
          }
        },
        animation: {
          animateScale: true,
          animateRotate: true
        }
      }
    });
  };
  
  // Watch for data changes
  watch(() => props.data, (newData) => {
    if (chartInstance) {
      chartInstance.data.labels = newData.labels;
      chartInstance.data.datasets = newData.datasets.map((dataset, index) => ({
        ...dataset,
        backgroundColor: dataset.backgroundColor || defaultColors.slice(0, newData.labels.length)
      }));
      chartInstance.update();
    }
  }, { deep: true });
  
  // Watch for options changes
  watch(() => props.options, (newOptions) => {
    if (chartInstance) {
      chartInstance.options = {
        ...chartInstance.options,
        ...newOptions
      };
      chartInstance.update();
    }
  }, { deep: true });
  
  // Initialize on mount
  onMounted(() => {
    initChart();
  });
  
  // Clean up before unmount
  onBeforeUnmount(() => {
    if (chartInstance) {
      chartInstance.destroy();
    }
  });
  </script>
  
  <style scoped>
  .chart-container {
    position: relative;
    width: 100%;
    min-height: 300px;
    max-height: 500px;
  }
  
  @media (min-width: 640px) {
    .chart-container {
      min-height: 350px;
    }
  }
  
  @media (min-width: 1024px) {
    .chart-container {
      min-height: 400px;
    }
  }
  </style>