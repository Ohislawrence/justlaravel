<!-- resources/js/Pages/Auth/ResetPassword.vue -->
<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <!-- White Backdrop -->
    <div class="min-h-screen bg-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Modern Card Container -->
        <div class="max-w-md w-full space-y-8">
            <!-- Branding Section -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 flex items-center justify-center shadow-md mb-4">
                    <span class="text-white font-bold text-2xl">Q</span>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                    Reset your password
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter your email address and new password below.
                </p>
            </div>

            <!-- Reset Password Form -->
            <form class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-lg border border-gray-100" @submit.prevent="submit">
                <div class="space-y-5">
                     <!-- Hidden Token Input (if needed, though usually handled by the form object) -->
                     <input type="hidden" name="token" :value="token">

                    <!-- Email Input -->
                    <div>
                        <InputLabel for="email" value="Email address" class="block text-sm font-medium text-gray-700 mb-1" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.email" />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <InputLabel for="password" value="New Password" class="block text-sm font-medium text-gray-700 mb-1" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password Input -->
                    <div>
                        <InputLabel for="password_confirmation" value="Confirm New Password" class="block text-sm font-medium text-gray-700 mb-1" />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6">
                    <PrimaryButton
                        class="flex justify-center rounded-lg border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-300"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Resetting...</span>
                        <span v-else>Reset Password</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Add any specific styles for the reset password page content if needed */
</style>