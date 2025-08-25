<script setup>
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Create Button - Aligned to the right -->
                        <div class="flex justify-end mb-6">
                            <button 
                                @click="createUserGroup"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
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
                                    <tr v-for="group in groups.data" :key="group.id" class="hover:bg-gray-50 transition-colors duration-150">
                                        <!-- Standardized table cells -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link 
                                                :href="route('examiner.groups.show', {
                                                    organization: organization.slug,
                                                    group: group.id
                                                })" 
                                                class="text-green-600 hover:text-green-800 font-medium transition-colors duration-150"
                                            >
                                                {{ group.name }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ group.members_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <!-- Action Buttons - Styled like Quizzes -->
                                            <div class="flex items-center gap-2">
                                                <Link 
                                                    :href="route('examiner.groups.show', {
                                                        organization: organization.slug,
                                                        group: group.id
                                                    })"
                                                    class="inline-flex items-center text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Manage
                                                </Link>
                                                <button 
                                                    @click="deleteGroup(group)"
                                                    class="inline-flex items-center text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition duration-150 ease-in-out"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Pagination -->
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
                                    <button 
                                        @click="createUserGroup"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Create New Group
                                    </button>
                                </div>
                            </div>
                        </div>
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

/* Green-themed background */
.bg-green-50 {
    background-color: #ecfdf5;
}

/* Border styling */
.border-gray-100 {
    border-color: #f3f4f6;
}

/* Transition effects */
.transition-colors {
    transition: all 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .flex {
        flex-direction: column;
    }
    
    .gap-2 > *:not(:last-child) {
        margin-bottom: 0.5rem;
    }
}
</style>