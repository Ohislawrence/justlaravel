<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

// track active tab
const activeTab = ref('profile');
</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    
                    <!-- Tabs Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-6">
                            <button 
                                @click="activeTab = 'profile'" 
                                :class="activeTab === 'profile' 
                                    ? 'border-indigo-500 text-indigo-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-2 px-1 border-b-2 font-medium text-sm"
                            >
                                Profile Info
                            </button>
                            
                            <button 
                                v-if="$page.props.jetstream?.canUpdatePassword"
                                @click="activeTab = 'password'" 
                                :class="activeTab === 'password' 
                                    ? 'border-indigo-500 text-indigo-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-2 px-1 border-b-2 font-medium text-sm"
                            >
                                Password
                            </button>

                            <button 
                                v-if="$page.props.jetstream?.canManageTwoFactorAuthentication"
                                @click="activeTab = 'twofactor'" 
                                :class="activeTab === 'twofactor' 
                                    ? 'border-indigo-500 text-indigo-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-2 px-1 border-b-2 font-medium text-sm"
                            >
                                Two-Factor
                            </button>

                            <button 
                                @click="activeTab = 'sessions'" 
                                :class="activeTab === 'sessions' 
                                    ? 'border-indigo-500 text-indigo-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-2 px-1 border-b-2 font-medium text-sm"
                            >
                                Browser Sessions
                            </button>

                            <button 
                                v-if="$page.props.jetstream?.hasAccountDeletionFeatures"
                                @click="activeTab = 'delete'" 
                                :class="activeTab === 'delete' 
                                    ? 'border-red-500 text-red-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-2 px-1 border-b-2 font-medium text-sm"
                            >
                                Delete Account
                            </button>
                        </nav>
                    </div>

                    <!-- Tabs Content -->
                    <div>
                        <div v-show="activeTab === 'profile'">
                            <UpdateProfileInformationForm :user="$page.props.auth.user" />
                        </div>

                        <div v-if="$page.props.jetstream?.canUpdatePassword" v-show="activeTab === 'password'">
                            <UpdatePasswordForm />
                        </div>

                        <div v-if="$page.props.jetstream?.canManageTwoFactorAuthentication" v-show="activeTab === 'twofactor'">
                            <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
                        </div>

                        <div v-show="activeTab === 'sessions'">
                            <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                        </div>

                        <div v-if="$page.props.jetstream?.hasAccountDeletionFeatures" v-show="activeTab === 'delete'">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
