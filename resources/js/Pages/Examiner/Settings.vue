<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue'; // Assuming you have a Checkbox component

const page = usePage();
const props = defineProps({
    user: Object,
    organization: Object, // Assuming organization data is passed as a prop
    // Add other relevant props like notification preferences if needed
    // e.g., notificationPreferences: Object
});

// --- State for Tabs ---
const activeTab = ref('general'); // Default to 'general' tab

// --- General Settings State (Profile & Password) ---
const profileForm = ref({
    name: '',
    email: '',
    // Add other profile fields as needed (e.g., bio)
});

const profileProcessing = ref(false);
const profileErrors = ref({});

const passwordForm = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const passwordProcessing = ref(false);
const passwordErrors = ref({});

// --- Notification Settings State (Example) ---
// This assumes you have a way to fetch and update notification preferences
// You might pass these as props or fetch them separately
const notificationForm = ref({
    email_quiz_results: page.props.userPreferences?.email_quiz_results ?? true,
    email_new_assignments: page.props.userPreferences?.email_new_assignments ?? true,
    email_reminders: page.props.userPreferences?.email_reminders ?? true,
    in_app_notifications: page.props.userPreferences?.in_app_notifications ?? true,
    // Add more notification settings as needed
});

const notificationProcessing = ref(false);
const notificationErrors = ref({});

// --- Organization Settings State (Example) ---
// This section might be read-only or allow specific edits depending on user role
// For now, let's assume it displays organization info, potentially with an edit option for admins
const organizationForm = ref({
    name: props.organization?.name || '',
    slug: props.organization?.slug || '',
    // Add other relevant, editable organization fields if applicable
    // description, contact_email, etc.
});

const organizationProcessing = ref(false);
const organizationErrors = ref({});

// --- Computed Properties ---
// (None specifically needed for tab logic in this simple case)

// --- Methods ---

const updateProfile = () => {
    profileProcessing.value = true;
    profileErrors.value = {};

    router.put(route('profile.update'), profileForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            // Profile updated successfully
            console.log('Profile updated');
        },
        onError: (errors) => {
            profileErrors.value = errors;
        },
        onFinish: () => {
            profileProcessing.value = false;
        }
    });
};

const updatePassword = () => {
    passwordProcessing.value = true;
    passwordErrors.value = {};

    router.put(route('password.update'), passwordForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            // Password updated successfully
            console.log('Password updated');
            // Clear the form fields
            passwordForm.value.current_password = '';
            passwordForm.value.password = '';
            passwordForm.value.password_confirmation = '';
        },
        onError: (errors) => {
            passwordErrors.value = errors;
        },
        onFinish: () => {
            passwordProcessing.value = false;
        }
    });
};

const updateNotifications = () => {
    notificationProcessing.value = true;
    notificationErrors.value = {};

    // Assuming a route like 'settings.notifications.update'
    router.put(route('settings.notifications.update'), notificationForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            // Notifications updated successfully
            console.log('Notifications updated');
        },
        onError: (errors) => {
            notificationErrors.value = errors;
        },
        onFinish: () => {
            notificationProcessing.value = false;
        }
    });
};

// Placeholder for organization update if needed
const updateOrganization = () => {
     // Implement organization update logic if applicable
     // This might involve checking user permissions
     console.log('Update organization logic would go here');
     // organizationProcessing.value = true;
     // ... router.put(route('organization.update', props.organization.id), organizationForm.value, {...})
};

</script>

<template>
    <AppLayout title="Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button
                                    @click="activeTab = 'general'"
                                    :class="[
                                        activeTab === 'general'
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                                    ]"
                                    :aria-current="activeTab === 'general' ? 'page' : undefined"
                                >
                                    General
                                </button>
                                <button
                                    @click="activeTab = 'notification'"
                                    :class="[
                                        activeTab === 'notification'
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                                    ]"
                                    :aria-current="activeTab === 'notification' ? 'page' : undefined"
                                >
                                    Notification
                                </button>
                                <button
                                    @click="activeTab = 'organization'"
                                    :class="[
                                        activeTab === 'organization'
                                            ? 'border-indigo-500 text-indigo-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                                    ]"
                                    :aria-current="activeTab === 'organization' ? 'page' : undefined"
                                >
                                    Organization
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div>

                            <!-- General Tab Content -->
                            <div v-show="activeTab === 'general'">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Profile Information</h3>
                                <form @submit.prevent="updateProfile" class="mb-10"> <!-- Added mb-10 for spacing -->
                                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                                        <div>
                                            <InputLabel for="profile_name" value="Name" />
                                            <TextInput
                                                id="profile_name"
                                                v-model="profileForm.name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                                autofocus
                                                autocomplete="name"
                                            />
                                            <InputError :message="profileErrors.name" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="profile_email" value="Email" />
                                            <TextInput
                                                id="profile_email"
                                                v-model="profileForm.email"
                                                type="email"
                                                class="mt-1 block w-full"
                                                required
                                                autocomplete="username"
                                            />
                                            <InputError :message="profileErrors.email" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end mt-6">
                                        <PrimaryButton :disabled="profileProcessing">
                                            <span v-if="profileProcessing">Saving...</span>
                                            <span v-else>Save Changes</span>
                                        </PrimaryButton>
                                    </div>
                                </form>

                                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Update Password</h3>
                                <form @submit.prevent="updatePassword">
                                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                                         <div class="sm:col-span-2">
                                            <InputLabel for="current_password" value="Current Password" />
                                            <TextInput
                                                id="current_password"
                                                v-model="passwordForm.current_password"
                                                type="password"
                                                class="mt-1 block w-full"
                                                required
                                                autocomplete="current-password"
                                            />
                                            <InputError :message="passwordErrors.current_password" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                                        <div>
                                            <InputLabel for="password" value="New Password" />
                                            <TextInput
                                                id="password"
                                                v-model="passwordForm.password"
                                                type="password"
                                                class="mt-1 block w-full"
                                                required
                                                autocomplete="new-password"
                                            />
                                            <InputError :message="passwordErrors.password" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="password_confirmation" value="Confirm Password" />
                                            <TextInput
                                                id="password_confirmation"
                                                v-model="passwordForm.password_confirmation"
                                                type="password"
                                                class="mt-1 block w-full"
                                                required
                                                autocomplete="new-password"
                                            />
                                            <InputError :message="passwordErrors.password_confirmation" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end mt-6">
                                        <PrimaryButton :disabled="passwordProcessing">
                                             <span v-if="passwordProcessing">Updating...</span>
                                             <span v-else>Update Password</span>
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>

                            <!-- Notification Tab Content -->
                            <div v-show="activeTab === 'notification'">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Notification Preferences</h3>
                                <form @submit.prevent="updateNotifications">
                                    <div class="space-y-4"> <!-- Use space-y for vertical spacing between items -->
                                        <div class="flex items-center">
                                            <Checkbox
                                                id="email_quiz_results"
                                                v-model:checked="notificationForm.email_quiz_results"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <InputLabel for="email_quiz_results" class="ml-2 block text-sm text-gray-700">
                                                Email quiz results to me
                                            </InputLabel>
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox
                                                id="email_new_assignments"
                                                v-model:checked="notificationForm.email_new_assignments"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <InputLabel for="email_new_assignments" class="ml-2 block text-sm text-gray-700">
                                                Email me when assigned new quizzes
                                            </InputLabel>
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox
                                                id="email_reminders"
                                                v-model:checked="notificationForm.email_reminders"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <InputLabel for="email_reminders" class="ml-2 block text-sm text-gray-700">
                                                Send email reminders for upcoming quizzes
                                            </InputLabel>
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox
                                                id="in_app_notifications"
                                                v-model:checked="notificationForm.in_app_notifications"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <InputLabel for="in_app_notifications" class="ml-2 block text-sm text-gray-700">
                                                Show in-app notifications
                                            </InputLabel>
                                        </div>

                                        <!-- Add more notification settings as needed -->
                                    </div>

                                    <div class="flex items-center justify-end mt-8"> <!-- Increased top margin -->
                                        <PrimaryButton :disabled="notificationProcessing">
                                             <span v-if="notificationProcessing">Saving...</span>
                                             <span v-else>Save Preferences</span>
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>

                            <!-- Organization Tab Content -->
                            <div v-show="activeTab === 'organization'">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Organization Details</h3>
                                <div class="bg-gray-50 p-4 rounded-md mb-6"> <!-- Info box styling -->
                                    <p class="text-sm text-gray-600 mb-2">You are currently viewing settings for:</p>
                                    <h4 class="text-base font-semibold text-gray-800">{{ props.organization?.name }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">ID: {{ props.organization?.slug }}</p>
                                </div>

                                <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel for="org_name" value="Organization Name" />
                                        <TextInput
                                            id="org_name"
                                            v-model="organizationForm.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            disabled
                                            readonly
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Organization name cannot be changed here.</p>
                                    </div>

                                    <div>
                                        <InputLabel for="org_slug" value="Organization Slug" />
                                        <TextInput
                                            id="org_slug"
                                            v-model="organizationForm.slug"
                                            type="text"
                                            class="mt-1 block w-full"
                                            disabled
                                            readonly
                                        />
                                        <p class="mt-1 text-xs text-gray-500">This is your organization's unique identifier.</p>
                                    </div>
                                </div>

                                <!-- Display other organization details if needed -->
                                <!--
                                <div class="mt-6">
                                    <h4 class="text-md font-medium text-gray-800 mb-2">Contact Information</h4>
                                    <p class="text-sm text-gray-600">Contact email: {{ props.organization?.contact_email }}</p>
                                </div>
                                -->

                                <!-- If organization settings are editable (e.g., by admins), add a form here -->
                                <!--
                                <form @submit.prevent="updateOrganization" class="mt-8">
                                    <h4 class="text-md font-medium text-gray-800 mb-2">Edit Organization (Admin Only)</h4>
                                     ... form fields for editable org details ...
                                    <div class="flex items-center justify-end mt-4">
                                        <PrimaryButton :disabled="organizationProcessing">
                                             <span v-if="organizationProcessing">Saving...</span>
                                             <span v-else>Save Organization</span>
                                        </PrimaryButton>
                                    </div>
                                </form>
                                -->
                            </div>

                        </div> <!-- End Tab Content -->

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Add any specific scoped styles for the settings page if needed */
</style>