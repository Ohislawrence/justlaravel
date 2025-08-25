<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue'; // Assuming you have a Checkbox component
import GradingComponent from '@/Components/GradingComponent.vue';

const page = usePage();
const props = defineProps({
    user: Object,
    organization: Object, 
});

// --- State for Tabs ---
const activeTab = ref('organization'); // Default to 'organization' tab

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
    description: props.organization?.description || '',
    website: props.organization?.website || '',
    industry: props.organization?.industry || '',
    office_address: props.organization?.office_address || '',
    official_phone_number: props.organization?.official_phone_number || '',
    official_email: props.organization?.official_email || '',
    official_Whatsapp_contact: props.organization?.official_Whatsapp_contact || '',
    logo: null, // For file upload
    certificate_seal: null, // For file upload
});

const currentLogoUrl = computed(() => {
    return props.organization?.logo 
        ? `/storage/${props.organization.logo}`
        : '/images/default-organization-logo.png';
});

const currentSealUrl = computed(() => {
    return props.organization?.certificate_seal 
        ? `/storage/${props.organization.certificate_seal}`
        : '/images/default-seal.png';
});

const organizationProcessing = ref(false);
const organizationErrors = ref({});

// --- Computed Properties ---
// (None specifically needed for tab logic in this simple case)

// --- Methods ---


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
    organizationProcessing.value = true;
    organizationErrors.value = {};

    // Create FormData to handle file uploads
    const formData = new FormData();
    
    // Append all fields to formData
    Object.keys(organizationForm.value).forEach(key => {
        if (organizationForm.value[key] !== null) {
            formData.append(key, organizationForm.value[key]);
        }
    });

    router.post(route('examiner.settings.organization.update'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            // Reset file inputs after successful upload
            organizationForm.value.logo = null;
            organizationForm.value.certificate_seal = null;
        },
        onError: (errors) => {
            organizationErrors.value = errors;
        },
        onFinish: () => {
            organizationProcessing.value = false;
        },
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });
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
                                    @click="activeTab = 'organization'"
                                    :class="[
                                        activeTab === 'organization'
                                            ? 'border-green-500 text-green-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                                    ]"
                                    :aria-current="activeTab === 'organization' ? 'page' : undefined"
                                >
                                    Organization
                                </button>
                                <button
                                    @click="activeTab = 'notification'"
                                    :class="[
                                        activeTab === 'notification'
                                            ? 'border-green-500 text-green-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                                    ]"
                                    :aria-current="activeTab === 'notification' ? 'page' : undefined"
                                >
                                    Notification
                                </button>
                                
                                
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div>

                            

                            <!-- Notification Tab Content -->
                            <div v-show="activeTab === 'notification'">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Notification Preferences</h3>
                                <form @submit.prevent="updateNotifications">
                                    <div class="space-y-4"> <!-- Use space-y for vertical spacing between items -->
                                        <div class="flex items-center">
                                            <Checkbox
                                                id="email_quiz_results"
                                                v-model:checked="notificationForm.email_quiz_results"
                                                class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                            />
                                            <InputLabel for="email_quiz_results" class="ml-2 block text-sm text-gray-700">
                                                Email quiz results to me
                                            </InputLabel>
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox
                                                id="email_new_assignments"
                                                v-model:checked="notificationForm.email_new_assignments"
                                                class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                            />
                                            <InputLabel for="email_new_assignments" class="ml-2 block text-sm text-gray-700">
                                                Email me when assigned new quizzes
                                            </InputLabel>
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox
                                                id="email_reminders"
                                                v-model:checked="notificationForm.email_reminders"
                                                class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                            />
                                            <InputLabel for="email_reminders" class="ml-2 block text-sm text-gray-700">
                                                Send email reminders for upcoming quizzes
                                            </InputLabel>
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox
                                                id="in_app_notifications"
                                                v-model:checked="notificationForm.in_app_notifications"
                                                class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
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

                                <form @submit.prevent="updateOrganization">
                                    <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                                        <div>
                                            <InputLabel for="org_name" value="Organization Name" />
                                            <TextInput
                                                id="org_name"
                                                v-model="organizationForm.name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                            <InputError :message="organizationErrors.name" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_description" value="Description" />
                                            <textarea
                                                id="org_description"
                                                v-model="organizationForm.description"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                                                rows="3"
                                            ></textarea>
                                            <InputError :message="organizationErrors.description" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_website" value="Website" />
                                            <TextInput
                                                id="org_website"
                                                v-model="organizationForm.website"
                                                type="url"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError :message="organizationErrors.website" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_industry" value="Industry" />
                                            <TextInput
                                                id="org_industry"
                                                v-model="organizationForm.industry"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError :message="organizationErrors.industry" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_address" value="Office Address" />
                                            <TextInput
                                                id="org_address"
                                                v-model="organizationForm.office_address"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError :message="organizationErrors.office_address" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_phone" value="Official Phone Number" />
                                            <TextInput
                                                id="org_phone"
                                                v-model="organizationForm.official_phone_number"
                                                type="tel"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError :message="organizationErrors.official_phone_number" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_email" value="Official Email" />
                                            <TextInput
                                                id="org_email"
                                                v-model="organizationForm.official_email"
                                                type="email"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError :message="organizationErrors.official_email" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_whatsapp" value="WhatsApp Contact" />
                                            <TextInput
                                                id="org_whatsapp"
                                                v-model="organizationForm.official_Whatsapp_contact"
                                                type="tel"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError :message="organizationErrors.official_Whatsapp_contact" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="org_logo" value="Organization Logo" />
                                            <div class="mt-2 flex items-center gap-4">
                                                <img :src="currentLogoUrl" alt="Current logo" class="h-16 w-16 rounded-full object-cover">
                                                <input
                                                    id="org_logo"
                                                    type="file"
                                                    @change="organizationForm.logo = $event.target.files[0]"
                                                    accept="image/*"
                                                    class="block w-full text-sm text-gray-500
                                                        file:mr-4 file:py-2 file:px-4
                                                        file:rounded-md file:border-0
                                                        file:text-sm file:font-semibold
                                                        file:bg-green-50 file:text-green-700
                                                        hover:file:bg-green-100"
                                                />
                                            </div>
                                            <InputError :message="organizationErrors.logo" class="mt-2" />
                                            <p class="mt-1 text-xs text-gray-500">Recommended size: 256x256 pixels. Max 2MB.</p>
                                        </div>

                                        <!-- Certificate Seal Upload -->
                                        <div>
                                            <InputLabel for="org_seal" value="Certificate Seal" />
                                            <div class="mt-2 flex items-center gap-4">
                                                <img :src="currentSealUrl" alt="Current seal" class="h-16 w-16 rounded-full object-cover">
                                                <input
                                                    id="org_seal"
                                                    type="file"
                                                    @change="organizationForm.certificate_seal = $event.target.files[0]"
                                                    accept="image/*"
                                                    class="block w-full text-sm text-gray-500
                                                        file:mr-4 file:py-2 file:px-4
                                                        file:rounded-md file:border-0
                                                        file:text-sm file:font-semibold
                                                        file:bg-green-50 file:text-green-700
                                                        hover:file:bg-green-100"
                                                />
                                            </div>
                                            <InputError :message="organizationErrors.certificate_seal" class="mt-2" />
                                            <p class="mt-1 text-xs text-gray-500">Recommended size: 200x200 pixels. Max 2MB.</p>
                                        </div>
                                    
                                    </div>

                                    <div class="flex items-center justify-end mt-6">
                                        <PrimaryButton :disabled="organizationProcessing">
                                            <span v-if="organizationProcessing">Saving...</span>
                                            <span v-else>Save Changes</span>
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                            <!-- Organization Tab Content -->
                            

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