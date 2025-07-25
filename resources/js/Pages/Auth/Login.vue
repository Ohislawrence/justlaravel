<!-- resources/js/Pages/Auth/Login.vue -->
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
// Import the new layout
import AppLayout2 from '@/Layouts/AppLayout2.vue';
// You might still want some components for the form itself, or recreate the styles
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'; // Or use your own button style
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue'; // Or use your own checkbox style

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <!-- Use your custom layout instead of AuthenticationCard -->
    <AppLayout2>
        <!-- Main content area within your layout -->
        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                <!-- Logo/Brand Section (Optional, you could link to your homepage logo) -->
                <div>
                    <div class="mx-auto h-16 w-16 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 flex items-center justify-center shadow-sm">
                        <span class="text-white font-bold text-2xl">Q</span>
                    </div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Sign in to your account
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Or
                        <Link :href="route('register')" class="font-medium text-emerald-600 hover:text-emerald-500">
                            start your free trial
                        </Link>
                    </p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                    {{ status }}
                </div>

                <!-- Login Form -->
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
                        <div class="flex items-center justify-between">
                            <InputLabel for="password" value="Password" class="block text-sm font-medium text-gray-700" />
                            <div class="text-sm">
                                <Link v-if="canResetPassword" :href="route('password.request')" class="font-medium text-emerald-600 hover:text-emerald-500">
                                    Forgot your password?
                                </Link>
                            </div>
                        </div>
                        <div class="mt-1">
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                required
                                autocomplete="current-password"
                            />
                            <InputError class="mt-2 text-sm text-red-600" :message="form.errors.password" />
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center">
                        <Checkbox
                            id="remember-me"
                            v-model:checked="form.remember"
                            name="remember-me"
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                        />
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <PrimaryButton
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-300"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Sign in
                        </PrimaryButton>
                        <!-- Or use a standard button styled like your template:
                        <button
                            type="submit"
                            class="w-full py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm hover:shadow text-sm font-medium"
                            :disabled="form.processing"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        >
                           <span v-if="form.processing">Signing in...</span>
                           <span v-else>Sign in</span>
                        </button>
                        -->
                    </div>
                </form>
            </div>
        </div>
    </AppLayout2>
</template>

<style scoped>
/* Add any specific styles for the login page content if needed */
</style>