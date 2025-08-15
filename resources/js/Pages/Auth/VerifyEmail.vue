<!-- resources/js/Pages/Auth/VerifyEmail.vue -->
<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />

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
                    Verify your email address
                </h2>
            </div>

            <!-- Verification Message -->
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                <div class="text-sm text-gray-600 mb-6">
                    Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </div>

                <!-- Status Message -->
                <div v-if="verificationLinkSent" class="rounded-md bg-green-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                A new verification link has been sent to the email address you provided in your profile settings.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Verification Form -->
                <form @submit.prevent="submit">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <PrimaryButton
                            class="w-full sm:w-auto flex justify-center rounded-lg border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-300"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Sending...</span>
                            <span v-else>Resend Verification Email</span>
                        </PrimaryButton>

                        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-4 text-sm">
                            <Link
                                :href="route('profile.show')"
                                class="font-medium text-emerald-600 hover:text-emerald-500 focus:outline-none focus:underline"
                            >
                                Edit Profile
                            </Link>
                            <span class="hidden sm:inline text-gray-300">|</span>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="font-medium text-emerald-600 hover:text-emerald-500 focus:outline-none focus:underline"
                            >
                                Log Out
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add any specific styles for the email verification page content if needed */
</style>