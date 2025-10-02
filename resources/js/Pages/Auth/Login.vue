<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    // Honeypot field - should remain empty
    honeypot: '',
});

// Generate a random field name for the honeypot to make it harder for bots to detect
const honeypotFieldName = ref(`contact_${Math.random().toString(36).substring(2, 9)}`);

const submit = () => {
    // Check honeypot field - if it has any value, it's likely a bot
    if (form.honeypot && form.honeypot.trim() !== '') {
        // You can log this attempt or handle it silently
        console.log('Bot detected via honeypot');
        
        // Reset the form and prevent submission
        form.reset();
        return;
    }

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

    <!-- White Backdrop -->
    <div class="min-h-screen bg-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Modern Card Container -->
        <div class="max-w-md w-full space-y-8">
            <!-- Branding Section -->
            <div class="text-center">
                <div class="items-center justify-center mb-4">
                    <Link :href="route('home')" class="flex items-center">
                        <img 
                        src="/images/logo.png" 
                        alt="ExamPortal Logo" 
                        class=""
                        />
                    </Link>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                    Welcome back
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Please sign in to your account
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

            <!-- Login Form -->
            <form class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-lg border border-gray-100" @submit.prevent="submit">
                <div class="space-y-5">
                    <!-- Honeypot Field (Hidden from real users) -->
                    <div class="hidden">
                        <InputLabel :for="honeypotFieldName" value="Do not fill this field" />
                        <TextInput
                            :id="honeypotFieldName"
                            v-model="form.honeypot"
                            type="text"
                            :name="honeypotFieldName"
                            class="hidden"
                            tabindex="-1"
                            autocomplete="off"
                        />
                    </div>

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
                        <div class="flex items-center justify-between">
                            <InputLabel for="password" value="Password" class="block text-sm font-medium text-gray-700 mb-1" />
                            <div class="text-sm">
                                <Link v-if="canResetPassword" :href="route('password.request')" class="font-medium text-emerald-600 hover:text-emerald-500">
                                    Forgot password?
                                </Link>
                            </div>
                        </div>
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.password" />
                    </div>
                </div>

                <!-- Remember Me & Submit -->
                <div class="flex items-center justify-between">
                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center">
                        <Checkbox
                            id="remember-me"
                            v-model:checked="form.remember"
                            name="remember-me"
                            class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                        />
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <PrimaryButton
                            class="ml-4 flex justify-center rounded-lg border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-300"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Signing in...</span>
                            <span v-else>Sign in</span>
                        </PrimaryButton>
                    </div>
                </div>

                 <!-- Sign Up Link -->
                 <div class="text-sm text-center pt-4">
                    <span class="text-gray-600">Don't have an account? </span>
                    <Link :href="route('register')" class="font-medium text-emerald-600 hover:text-emerald-500">
                        Start for free!
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Ensure the honeypot field is properly hidden */
.hidden {
    display: none !important;
}
</style>