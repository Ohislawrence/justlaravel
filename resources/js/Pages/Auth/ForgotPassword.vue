<!-- resources/js/Pages/Auth/ForgotPassword.vue -->
<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

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
                    Forgot your password?
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                </p>
            </div>

            <!-- Status Message -->
            <div v-if="status" class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ status }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Forgot Password Form -->
            <form class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-lg border border-gray-100" @submit.prevent="submit">
                <div class="space-y-5">
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
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6">
                    <PrimaryButton
                        class="flex justify-center rounded-lg border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-300"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Sending...</span>
                        <span v-else>Email Password Reset Link</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Add any specific styles for the forgot password page content if needed */
</style>