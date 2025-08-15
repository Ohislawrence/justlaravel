<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
// Removed unused imports: ActionMessage, ActionSection, DialogModal, PrimaryButton, SecondaryButton
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;
    form.reset(); // Reset form errors and values when opening modal
    // Focus is handled by the modal's mounted/updated hook or a watcher
    // setTimeout(() => passwordInput.value?.focus(), 250); // Can be kept if needed
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {
             // Ensure the modal is still open before trying to focus
             if (confirmingLogout.value && passwordInput.value) {
                // Use $nextTick or a small timeout if necessary to ensure DOM update
                setTimeout(() => passwordInput.value.focus(), 10);
             }
        },
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.reset();
};
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="max-w-3xl">
                        <!-- Header -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Browser Sessions</h2>
                        <p class="text-gray-600 mb-6">Manage and log out your active sessions on other browsers and devices.</p>

                        <!-- Description -->
                        <div class="max-w-xl text-sm text-gray-600 mb-6">
                            If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.
                        </div>

                        <!-- Other Browser Sessions -->
                        <div v-if="sessions?.length > 0" class="mt-4 space-y-6">
                            <div v-for="(session, i) in sessions" :key="i" class="flex items-center p-3 bg-gray-50 rounded-md">
                                <div>
                                    <svg v-if="session.agent?.is_desktop" class="size-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                    </svg>

                                    <svg v-else class="size-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                    </svg>
                                </div>

                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ session.agent?.platform || 'Unknown Platform' }} - {{ session.agent?.browser || 'Unknown Browser' }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        IP: {{ session.ip_address }},
                                        <span v-if="session.is_current_device" class="text-green-600 font-semibold">This device</span>
                                        <span v-else class="italic">Last active {{ session.last_active }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 text-sm">
                            No other active browser sessions found.
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center mt-8">
                            <button
                                type="button"
                                @click="confirmLogout"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition ease-in-out duration-150"
                            >
                                Log Out Other Browser Sessions
                            </button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-if="form.recentlySuccessful" class="ml-3 text-sm text-gray-600">Done.</p>
                            </Transition>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Out Other Devices Confirmation Modal -->
    <!-- Simple Tailwind-based Modal -->
    <div v-if="confirmingLogout" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

        <!-- Modal Content -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <!-- Icon (Optional, for visual confirmation) -->
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Log Out Other Browser Sessions
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.
                                </p>

                                <div class="mt-4">
                                    <TextInput
                                        ref="passwordInput"
                                        v-model="form.password"
                                        type="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Password"
                                        autocomplete="current-password"
                                        @keyup.enter="logoutOtherBrowserSessions"
                                    />

                                    <InputError :message="form.errors.password" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        :disabled="form.processing"
                        :class="{
                            'opacity-50 cursor-not-allowed': form.processing,
                            'inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm': !form.processing
                        }"
                        @click="logoutOtherBrowserSessions"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Logging Out...
                        </span>
                        <span v-else>Log Out Other Browser Sessions</span>
                    </button>
                    <button
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="closeModal"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</template>

<style scoped>
/* Add any specific scoped styles if needed */
</style>