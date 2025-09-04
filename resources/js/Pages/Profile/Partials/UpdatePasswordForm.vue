<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
// Removed unused imports: ActionMessage, FormSection, PrimaryButton
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus(); // Add optional chaining for safety
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus(); // Add optional chaining for safety
            }
        },
    });
};
</script>

<template>
       
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="max-w-3xl">
                        <!-- Header -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Update Password</h2>
                        <p class="text-gray-600 mb-6">Ensure your account is using a long, random password to stay secure.</p>

                        <form @submit.prevent="updatePassword">
                            <!-- Current Password -->
                            <div class="mb-6">
                                <InputLabel for="current_password" value="Current Password" class="mb-2" />
                                <TextInput
                                    id="current_password"
                                    ref="currentPasswordInput"
                                    v-model="form.current_password"
                                    type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    autocomplete="current-password"
                                />
                                <InputError :message="form.errors.current_password" class="mt-2" />
                            </div>

                            <!-- New Password -->
                            <div class="mb-6">
                                <InputLabel for="password" value="New Password" class="mb-2" />
                                <TextInput
                                    id="password"
                                    ref="passwordInput"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    autocomplete="new-password"
                                />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-6">
                                <InputLabel for="password_confirmation" value="Confirm Password" class="mb-2" />
                                <TextInput
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    autocomplete="new-password"
                                />
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition ease-in-out duration-150"
                                >
                                    <span v-if="form.processing">Saving...</span>
                                    <span v-else>Save</span>
                                </button>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
    
</template>

<style scoped>
/* Add any specific scoped styles if needed */
</style>