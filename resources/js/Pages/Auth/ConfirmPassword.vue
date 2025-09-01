<!-- resources/js/Pages/Auth/ConfirmPassword.vue -->
<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    password: '',
});

const passwordInput = ref(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
            if (passwordInput.value) {
                 passwordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <Head title="Secure Area" />

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
                    Confirm Password
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    This is a secure area of the application. Please confirm your password before continuing.
                </p>
            </div>

            <!-- Confirm Password Form -->
            <form class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-lg border border-gray-100" @submit.prevent="submit">
                <div class="space-y-5">
                    <!-- Password Input -->
                    <div>
                        <InputLabel for="password" value="Password" class="block text-sm font-medium text-gray-700 mb-1" />
                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                            required
                            autocomplete="current-password"
                            autofocus
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.password" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6">
                    <PrimaryButton
                        class="flex justify-center rounded-lg border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-300"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Confirming...</span>
                        <span v-else>Confirm</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Add any specific styles for the confirm password page content if needed */
</style>