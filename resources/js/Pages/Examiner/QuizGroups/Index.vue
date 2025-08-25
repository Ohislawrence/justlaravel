<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue'

const props = defineProps({
  organization: Object,
  groups: Array,
  groupLimit: Number,
  currentGroupCount: Number,
})

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

function createGroup() {
  const canAddGroup = props.currentGroupCount < props.groupLimit

  if (!canAddGroup) {
    openFeatureModal(
      'Add Group',
      'You have reached the group limit. Upgrade your plan to add more groups.'
    )
    return
  }

  router.visit(route('examiner.quiz-groups.create'))
}
</script>

<template>
  <AppLayout title="Quiz Groups">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Quiz Groups for {{ organization.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium">All Quiz Groups</h3>
              <button
                @click="createGroup"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
              >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create New Group
              </button>
            </div>

            <div v-if="groups.length > 0" class="space-y-4">
              <div v-for="group in groups" :key="group.id" class="border rounded-lg p-4 hover:bg-gray-50 transition-all duration-200">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <Link
                      :href="route('examiner.quiz-groups.show', { organization: organization.id, quiz_group: group.id })"
                      class="text-lg font-medium text-green-600 hover:text-green-800 transition-colors duration-150"
                    >
                      {{ group.name }}
                    </Link>
                    <p class="text-sm text-gray-500 mt-1">{{ group.description }}</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                      <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                        {{ group.quizzes_count }} quizzes
                      </span>
                      <span v-if="group.children_count > 0" class="text-xs bg-green-100 px-2 py-1 rounded text-green-800">
                        {{ group.children_count }} subgroups
                      </span>
                    </div>
                  </div>
                  <div class="flex space-x-2">
                    <Link
                      :href="route('examiner.quiz-groups.edit', { quiz_group: group.id })"
                      class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors duration-150 text-sm"
                    >
                      <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                      Edit
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
              <p class="mb-4">No quiz groups created yet.</p>
              <Link
                :href="route('examiner.quiz-groups.create', { organization: organization.id })"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition"
              >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create First Group
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <FeatureLimitModal 
      v-model="showModal"
      :featureName="modalFeature"
      :message="modalMessage"
    />
  </AppLayout>
</template>

<style scoped>
/* Enhanced green-themed styling */
.bg-green-600 {
    background-color: #10B981;
}

.bg-green-700:hover {
    background-color: #059669;
}

.focus\:ring-green-300:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Green-themed text links */
.text-green-600 {
    color: #10B981;
}

.text-green-800:hover {
    color: #059669;
}

/* Green-themed badges */
.bg-green-100 {
    background-color: #ecfdf5;
}

.text-green-800 {
    color: #065f46;
}

/* Hover effects */
.hover\:bg-green-200:hover {
    background-color: #d1fae5;
}

/* Card hover effect */
.hover\:bg-gray-50:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

/* Button hover effect */
.hover\:bg-green-700:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .flex {
        flex-direction: column;
    }
    
    .space-y-4 > *:not(:last-child) {
        margin-bottom: 1rem;
    }
}
</style>