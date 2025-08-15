<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    organization: Object,
    group: Object,
    availableUsers: Array,
    availableQuizzes: Array,
});

// Refs to hold selected items in the multi-selects
const selectedUserIds = ref([]);
const selectedQuizIds = ref([]);

// Ref to manage the active tab
const activeTab = ref('members'); // Default to 'members' tab

// Computed properties to check if selections are empty (for disabling buttons)
const isAddMembersDisabled = computed(() => selectedUserIds.value.length === 0);
const isAssignQuizzesDisabled = computed(() => selectedQuizIds.value.length === 0);

const addMembers = () => {
    router.post(route('examiner.groups.addMembers', {
        organization: props.organization.slug,
        group: props.group.id
    }), { user_ids: selectedUserIds.value });
};

const assignQuizzes = () => {
    router.post(route('examiner.groups.assignQuizzes', {
        organization: props.organization.slug,
        group: props.group.id
    }), { quiz_ids: selectedQuizIds.value });
};

const removeMember = (userId) => {
    router.delete(route('examiner.groups.removeMember', {
        organization: props.organization.slug,
        group: props.group.id,
        user: userId
    }));
};

const removeQuiz = (quizId) => {
    router.delete(route('examiner.groups.removeQuiz', {
        organization: props.organization.slug,
        group: props.group.id,
        quiz: quizId
    }));
};

// Add these new refs for the sharing functionality
const showShareModal = ref(false);
const emailInvite = ref('');
const isEmailValid = computed(() => {
    const re = /\S+@\S+\.\S+/;
    return re.test(emailInvite.value);
});

// Generate the registration link
const registrationLink = computed(() => {
    return `${window.location.origin}/${props.organization.slug}/member/${props.group.slug}/register`;
});

// Copy to clipboard function
const copyLink = () => {
    // Create a temporary input element
    const tempInput = document.createElement('input');
    tempInput.value = registrationLink.value;
    document.body.appendChild(tempInput);
    
    // Select the text
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        // Try using the modern clipboard API first
        if (navigator.clipboard) {
            navigator.clipboard.writeText(registrationLink.value)
                .then(() => {
                    alert('Link copied to clipboard!');
                })
                .catch(() => {
                    fallbackCopy();
                });
        } else {
            fallbackCopy();
        }
    } catch (err) {
        fallbackCopy();
    } finally {
        // Clean up
        document.body.removeChild(tempInput);
    }

    function fallbackCopy() {
        try {
            // Fallback using deprecated execCommand
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        } catch (err) {
            alert('Failed to copy link. Please copy it manually.');
            // Select the text in the input so user can manually copy
            tempInput.select();
        }
    }
};

// Email invite refs
const emailInvites = ref('');
const isSending = ref(false);
const sendError = ref(null);
const sendSuccess = ref(false);
const skippedEmails = ref(0);

// Validate multiple emails
const isValidEmails = computed(() => {
    if (!emailInvites.value.trim()) return false;
    
    const emails = emailInvites.value.split(',')
        .map(email => email.trim())
        .filter(email => email.length > 0);
    
    if (emails.length === 0) return false;
    
    const emailRegex = /\S+@\S+\.\S+/;
    return emails.every(email => emailRegex.test(email));
});

// Format emails for display
const formattedEmails = computed(() => {
    return emailInvites.value.split(',')
        .map(email => email.trim())
        .filter(email => email.length > 0);
});

// Send email invites function
const sendEmailInvites = async () => {
    isSending.value = true;
    sendError.value = null;
    sendSuccess.value = false;
    skippedEmails.value = 0;
    
    try {
        const response = await router.post(route('examiner.groups.sendInvite', {
            organization: props.organization.slug,
            group: props.group.id
        }), { 
            emails: formattedEmails.value 
        });
        
        if (response?.data?.skipped_count > 0) {
            skippedEmails.value = response.data.skipped_count;
        }
        
        sendSuccess.value = true;
        emailInvites.value = '';
        setTimeout(() => {
            sendSuccess.value = false;
            skippedEmails.value = 0;
        }, 5000);
    } catch (error) {
        sendError.value = error.response?.data?.message || 'Failed to send invitations';
    } finally {
        isSending.value = false;
    }
};
</script>

<template>
    <AppLayout title="Manage Group">
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="font-semibold text-2xl text-gray-900">
                    Manage Group: {{ group.name }}
                </h2>
                <button @click="showShareModal = true"
                    class="mt-3 md:mt-0 inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Share Registration Link
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        
                        <!-- Group Info Section -->
                        <div class="mb-10">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-100">Group Information</h3>
                            <form @submit.prevent="router.put(route('examiner.groups.update', {
                                organization: organization.slug,
                                group: group.id
                            }), group)">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                        <input v-model="group.name" type="text" 
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <input v-model="group.description" type="text" 
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    </div>
                                </div>
                                <button type="submit" 
                                    class="mt-6 inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Update Group
                                </button>
                            </form>
                        </div>

                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200 mb-8">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button
                                    @click="activeTab = 'members'"
                                    :class="[
                                        activeTab === 'members'
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base transition-colors duration-200'
                                    ]"
                                    :aria-current="activeTab === 'members' ? 'page' : undefined"
                                >
                                    Members
                                </button>
                                <button
                                    @click="activeTab = 'quizzes'"
                                    :class="[
                                        activeTab === 'quizzes'
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base transition-colors duration-200'
                                    ]"
                                    :aria-current="activeTab === 'quizzes' ? 'page' : undefined"
                                >
                                    Quizzes
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div>

                            <!-- Members Tab Content -->
                            <div v-show="activeTab === 'members'">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                    <h3 class="text-xl font-semibold text-gray-900">Group Members</h3>
                                    <button @click="addMembers" 
                                        :disabled="isAddMembersDisabled"
                                        :class="{ 'opacity-50 cursor-not-allowed': isAddMembersDisabled }"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Selected Members
                                    </button>
                                </div>

                                <!-- Available Members Card -->
                                <div class="bg-gray-50 rounded-xl p-5 mb-6 border border-gray-200">
                                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Available Members
                                    </h4>
                                    <div v-if="availableUsers.length > 0">
                                        <!-- Multi-Select Dropdown -->
                                        <select 
                                            v-model="selectedUserIds" 
                                            multiple 
                                            size="5"
                                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                        >
                                            <option 
                                                v-for="user in availableUsers" 
                                                :key="user.id" 
                                                :value="user.id"
                                                class="py-2 px-3 hover:bg-indigo-50 cursor-pointer"
                                            >
                                                {{ user.name }}
                                            </option>
                                        </select>
                                        <p class="mt-3 text-sm text-gray-500 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Hold Ctrl (Cmd on Mac) to select multiple members.
                                        </p>
                                    </div>
                                    <div v-else class="py-8 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="mt-3 text-sm text-gray-500">
                                            No available members to add
                                        </p>
                                    </div>
                                </div>

                                <!-- Current Members Card -->
                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Current Members
                                    </h4>
                                    <div v-if="group.members.length > 0" class="space-y-3 max-h-80 overflow-y-auto p-1">
                                        <div v-for="member in group.members" :key="member.id" 
                                            class="flex justify-between items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-indigo-800 font-medium">{{ member.name.charAt(0) }}</span>
                                                </div>
                                                <span class="ml-3 text-sm font-medium text-gray-800">{{ member.name }}</span>
                                            </div>
                                            <button @click="removeMember(member.id)" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div v-else class="py-8 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="mt-3 text-sm text-gray-500">
                                            No members in this group
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quizzes Tab Content -->
                            <div v-show="activeTab === 'quizzes'">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                    <h3 class="text-xl font-semibold text-gray-900">Assigned Quizzes</h3>
                                    <button @click="assignQuizzes" 
                                        :disabled="isAssignQuizzesDisabled"
                                        :class="{ 'opacity-50 cursor-not-allowed': isAssignQuizzesDisabled }"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Assign Selected Quizzes
                                    </button>
                                </div>

                                <!-- Available Quizzes Card -->
                                <div class="bg-gray-50 rounded-xl p-5 mb-6 border border-gray-200">
                                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Available Quizzes
                                    </h4>
                                    <div v-if="availableQuizzes.length > 0">
                                        <!-- Multi-Select Dropdown -->
                                        <select 
                                            v-model="selectedQuizIds" 
                                            multiple 
                                            size="5"
                                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                        >
                                            <option 
                                                v-for="quiz in availableQuizzes" 
                                                :key="quiz.id" 
                                                :value="quiz.id"
                                                class="py-2 px-3 hover:bg-indigo-50 cursor-pointer"
                                            >
                                                {{ quiz.title }}
                                            </option>
                                        </select>
                                        <p class="mt-3 text-sm text-gray-500 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Hold Ctrl (Cmd on Mac) to select multiple quizzes.
                                        </p>
                                    </div>
                                    <div v-else class="py-8 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-3 text-sm text-gray-500">
                                            No available quizzes to assign
                                        </p>
                                    </div>
                                </div>

                                <!-- Assigned Quizzes Card -->
                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Assigned Quizzes
                                    </h4>
                                    <div v-if="group.quizzes.length > 0" class="space-y-3 max-h-80 overflow-y-auto p-1">
                                        <div v-for="quiz in group.quizzes" :key="quiz.id" 
                                            class="flex justify-between items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <span class="ml-3 text-sm font-medium text-gray-800">{{ quiz.title }}</span>
                                            </div>
                                            <button @click="removeQuiz(quiz.id)" 
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div v-else class="py-8 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-3 text-sm text-gray-500">
                                            No quizzes assigned to this group
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- End Tab Content -->

                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div v-if="showShareModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md transform transition-all duration-200">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Share Registration Link
                        </h3>
                        <button @click="showShareModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="px-6 py-5">
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registration Link</label>
                        <div class="flex">
                            <input type="text" :value="registrationLink" readonly
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-l-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 truncate">
                            <button @click="copyLink"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-r-lg transition-all duration-200 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Or send via email (comma-separated)
                        </label>
                        <div class="flex flex-col">
                            <textarea v-model="emailInvites" 
                                placeholder="Enter email addresses, separated by commas"
                                rows="3"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            </textarea>
                            
                            <!-- Email preview -->
                            <div v-if="formattedEmails.length > 0" class="mt-3">
                                <p class="text-sm text-gray-600 mb-2">Will send to:</p>
                                <div class="flex flex-wrap gap-2">
                                    <span 
                                        v-for="(email, index) in formattedEmails" 
                                        :key="index"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                    >
                                        {{ email }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Validation error -->
                            <p v-if="formattedEmails.length > 0 && !isValidEmails" class="mt-2 text-sm text-red-600 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Please enter valid email addresses
                            </p>
                            
                            <!-- Send button -->
                            <button @click="sendEmailInvites" 
                                :disabled="!isValidEmails || isSending"
                                :class="{
                                    'bg-indigo-600 hover:bg-indigo-700': isValidEmails && !isSending,
                                    'bg-indigo-400 cursor-not-allowed': !isValidEmails || isSending
                                }"
                                class="mt-3 inline-flex items-center justify-center w-full text-white px-4 py-2.5 rounded-lg transition-all duration-200">
                                <svg v-if="isSending" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span v-if="isSending">Sending...</span>
                                <span v-else>Send Invitations</span>
                            </button>
                            
                            <!-- Success/error messages -->
                            <div v-if="sendSuccess" class="mt-3 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                                <div class="flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p>Invitations sent successfully!</p>
                                        <p v-if="skippedEmails > 0" class="mt-1 text-green-800">
                                            {{ skippedEmails }} email(s) were already invited or members
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="sendError" class="mt-3 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                                <div class="flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p>{{ sendError }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end">
                    <button @click="showShareModal = false"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>