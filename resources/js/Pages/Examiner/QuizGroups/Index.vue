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
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
              >
                Create New Group
              </button>
            </div>

            <div v-if="groups.length > 0" class="space-y-4">
              <div v-for="group in groups" :key="group.id" class="border rounded-lg p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <Link
                      :href="route('examiner.quiz-groups.show', { organization: organization.id, quiz_group: group.id })"
                      class="text-lg font-medium text-blue-600 hover:text-blue-800"
                    >
                      {{ group.name }}
                    </Link>
                    <p class="text-sm text-gray-500 mt-1">{{ group.description }}</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                      <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                        {{ group.quizzes_count }} quizzes
                      </span>
                      <span v-if="group.children_count > 0" class="text-xs bg-blue-100 px-2 py-1 rounded">
                        {{ group.children_count }} subgroups
                      </span>
                    </div>
                  </div>
                  <div class="flex space-x-2">
                    <Link
                      :href="route('examiner.quiz-groups.edit', { quiz_group: group.id })"
                      class="text-indigo-600 hover:text-indigo-900 text-sm"
                    >
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
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition"
              >
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
