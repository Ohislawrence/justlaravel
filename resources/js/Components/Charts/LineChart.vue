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
        scales: {
          y: {
            beginAtZero: true
          }
        }
      })
    },
    aspectRatio: {
      type: Number,
      default: 2 // width/height ratio
    }
  });
  
  const chartCanvas = ref(null);
  let chartInstance = null;
  
  // Default colors if not provided
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
      borderColor: dataset.borderColor || defaultColors[index % defaultColors.length],
      backgroundColor: dataset.backgroundColor || 'rgba(255, 255, 255, 0.1)',
      borderWidth: dataset.borderWidth || 2,
      pointBackgroundColor: dataset.pointBackgroundColor || defaultColors[index % defaultColors.length],
      pointRadius: dataset.pointRadius || 3,
      pointHoverRadius: dataset.pointHoverRadius || 5,
      tension: dataset.tension || 0.1,
      fill: dataset.fill || false
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
      type: 'line',
      data: chartData,
      options: {
        ...props.options,
        aspectRatio: props.aspectRatio,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              boxWidth: 12,
              padding: 20
            }
          },
          tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.parsed.y !== null) {
                  label += context.parsed.y;
                }
                return label;
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              drawBorder: false
            },
            ticks: {
              precision: 0
            }
          }
        },
        animation: {
          duration: 800,
          easing: 'easeOutQuart'
        },
        responsive: true,
        maintainAspectRatio: false
      }
    });
  };
  
  // Watch for data changes
  watch(() => props.data, (newData) => {
    if (chartInstance) {
      chartInstance.data.labels = newData.labels;
      chartInstance.data.datasets = newData.datasets.map((dataset, index) => ({
        ...dataset,
        borderColor: dataset.borderColor || defaultColors[index % defaultColors.length]
      }));
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