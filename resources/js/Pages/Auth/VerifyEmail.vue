<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
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
                        Verify your email address
                    </h2>
                </div>

                <!-- Verification Message -->
                <div class="mb-4 text-sm text-gray-600">
                    Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </div>

                <!-- Status Message -->
                <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                    A new verification link has been sent to the email address you provided in your profile settings.
                </div>

                <!-- Verification Form -->
                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <PrimaryButton
                            class="w-full sm:w-auto flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-300"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Resend Verification Email
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
    </AppLayout2>
</template>

<style scoped>
/* Add any specific styles for the email verification page content if needed */
</style>