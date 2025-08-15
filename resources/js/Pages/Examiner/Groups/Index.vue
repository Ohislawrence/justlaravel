\<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue'

const props = defineProps({
    organization: Object,
    groups: Object,
    currentUserGroupCount: Number,
    userGroupLimit: Number,
});

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

const deleteGroup = (group) => {
    if (confirm('Are you sure you want to delete this group?')) {
        router.delete(route('examiner.groups.destroy', {
            organization: props.organization.slug,
            group: group.id
        }));
    }
};

function createUserGroup() {
  const canAddUserGroup = props.currentUserGroupCount < props.userGroupLimit

  if (!canAddUserGroup) {
    openFeatureModal(
      'Add User Group',
      `You have reached the user group limit (${props.currentUserGroupCount}). Upgrade your plan to add more user groups.`
    )
    return
  }

  router.visit(route('examiner.groups.create', { organization: props.organization.slug }))
}
</script>

<template>
    <AppLayout title="Groups">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Groups in {{ organization.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Create Button - Aligned to the right -->
                        <div class="flex justify-end mb-6">
                            <button @click ="createUserGroup"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                Create New Group
                        </button>
                        </div>

                        <!-- Check if groups exist and handle empty state -->
                        <div v-if="groups.data && groups.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <!-- Standardized table headers -->
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Members
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="group in groups.data" :key="group.id" class="hover:bg-gray-50">
                                        <!-- Standardized table cells -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="route('examiner.groups.show', {
                                                organization: organization.slug,
                                                group: group.id
                                            })" class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ group.name }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ group.members_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <!-- Action Buttons - Styled like Quizzes -->
                                            <div class="flex items-center gap-2">
                                                <Link :href="route('examiner.groups.show', {
                                                    organization: organization.slug,
                                                    group: group.id
                                                })"
                                                    class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition duration-150 ease-in-out">
                                                    Manage
                                                </Link>
                                                <button @click="deleteGroup(group)"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition duration-150 ease-in-out">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Pagination - Updated to match Quizzes style -->
                            <Pagination :links="groups.links" class="mt-6" />
                        </div>
                        <div v-else class="text-center py-12">
                            <div class="text-gray-500">
                                <!-- Simple icon for empty state -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-lg mb-2">No groups found</p>
                                <p class="text-sm">
                                    Get started by creating your first group.
                                </p>
                                <!-- Offer the create button again in the empty state -->
                                <div class="mt-4">
                                    <Link :href="route('examiner.groups.create', organization.slug)"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                        Create New Group
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <!-- The standalone Pagination component below the table/empty state is removed
                             as it's now integrated into the table section or not needed for empty state -->
                    </div>
                </div>
            </div>
        </div>
        <FeatureLimitModal 
        v-model="showModal"
        :featureName="modalFeature"
        :message="modalMessage"
        />
    </AppLayout>
</template>