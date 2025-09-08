<!-- resources/js/Pages/Auth/Register.vue -->
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    industries: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    organization: '',
    password_confirmation: '',
    terms: false,
    industry: '',
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

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
                    Create exam/quiz in minutes!
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account?
                    <Link :href="route('login')" class="font-medium text-emerald-600 hover:text-emerald-500">
                        Sign in
                    </Link>
                </p>
            </div>

            <!-- Registration Form -->
            <form class="mt-8 space-y-6 bg-white p-8 rounded-2xl shadow-lg border border-gray-100" @submit.prevent="submit">
                <div class="space-y-5">
                    <!-- Organization Name Input -->
                    <div>
                        <InputLabel for="organization" value="Organization Name" class="block text-sm font-medium text-gray-700 mb-1" />
                        <TextInput
                            id="organization"
                            v-model="form.organization"
                            type="text"
                            class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                            required
                            autofocus
                            autocomplete="organization"
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.organization" />
                    </div>

                    <!-- Contact Person Name Input -->
                    <div>
                        <InputLabel for="name" value="Contact Person Name" class="block text-sm font-medium text-gray-700 mb-1" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm"
                            required
                            autocomplete="name"
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.name" />
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
                            autocomplete="username"
                        />
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel for="industry" value="Industry" class="block text-sm font-medium text-gray-700 mb-1" />
                        <select
                            id="industry"
                            v-model="form.industry"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm appearance-none bg-white"
                            required
                        >
                            <option value="" disabled>Select your industry</option>
                            <option
                                v-for="industry in props.industries"
                                :key="industry.id"
                                :value="industry.name"
                            >
                                {{ industry.name }}
                            </option>
                        </select>
                        <InputError class="mt-2 text-sm text-red-600" :message="form.errors.industry" />
                    </div>
                    
                     <!-- Terms and Privacy Policy Checkbox -->
                    <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <Checkbox
                                    id="terms"
                                    v-model:checked="form.terms"
                                    name="terms"
                                    required
                                    class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <InputLabel for="terms" class="font-medium text-gray-700">
                                    I agree to the <a target="_blank" :href="route('terms.show')" class="font-medium text-emerald-600 hover:text-emerald-500">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="font-medium text-emerald-600 hover:text-emerald-500">Privacy Policy</a>
                                </InputLabel>
                                <InputError class="mt-2 text-sm text-red-600" :message="form.errors.terms" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <PrimaryButton
                        class="flex w-full justify-center rounded-lg border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-300"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Registering...</span>
                        <span v-else>Register</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Add any specific styles for the register page content if needed */
</style>