<script setup>
import { Head, useForm } from '@inertiajs/vue3';
// Import the new layout
import AppLayout2 from '@/Layouts/AppLayout2.vue';
// You might still want some components for the form itself, or recreate the styles
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'; // Or use your own button style
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

    <!-- Use your custom layout instead of AuthenticationCard -->
    <AppLayout2>
        <!-- Main content area within your layout -->
        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                <!-- Logo/Brand Section -->
                <div>
                    <div class="mx-auto h-16 w-16 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 flex items-center justify-center shadow-sm">
                        <span class="text-white font-bold text-2xl">Q</span>
                    </div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Reset your password
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Enter your email address and new password below.
                    </p>
                </div>

                <!-- Reset Password Form -->
                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <!-- Email Input -->
                    <div>
                        <InputLabel for="email" value="Email address" class="block text-sm font-medium text-gray-700" />
                        <div class="mt-1">
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <InputError class="mt-2 text-sm text-red-600" :message="form.errors.email" />
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <InputLabel for="password" value="New Password" class="block text-sm font-medium text-gray-700" />
                        <div class="mt-1">
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2 text-sm text-red-600" :message="form.errors.password" />
                        </div>
                    </div>

                    <!-- Confirm Password Input -->
                    <div>
                        <InputLabel for="password_confirmation" value="Confirm New Password" class="block text-sm font-medium text-gray-700" />
                        <div class="mt-1">
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2 text-sm text-red-600" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <PrimaryButton
                            class="flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-300"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Reset Password
                        </PrimaryButton>
                        <!-- Or use a standard button styled like your template:
                        <button
                            type="submit"
                            class="py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm hover:shadow text-sm font-medium"
                            :disabled="form.processing"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        >
                           <span v-if="form.processing">Resetting...</span>
                           <span v-else>Reset Password</span>
                        </button>
                        -->
                    </div>
                </form>
            </div>
        </div>
    </AppLayout2>
</template>

<style scoped>
/* Add any specific styles for the reset password page content if needed */
</style>