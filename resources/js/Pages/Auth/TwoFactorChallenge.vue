<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
// Import the new layout
import AppLayout2 from '@/Layouts/AppLayout2.vue';
// You might still want some components for the form itself, or recreate the styles
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'; // Or use your own button style
import TextInput from '@/Components/TextInput.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head title="Two-factor Confirmation" />

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
                        Two-Factor Authentication
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        <template v-if="! recovery">
                            Please confirm access to your account by entering the authentication code provided by your authenticator application.
                        </template>
                        <template v-else>
                            Please confirm access to your account by entering one of your emergency recovery codes.
                        </template>
                    </p>
                </div>

                <!-- Two-Factor Authentication Form -->
                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <!-- Code Input -->
                    <div v-if="! recovery">
                        <InputLabel for="code" value="Authentication Code" class="block text-sm font-medium text-gray-700" />
                        <div class="mt-1">
                            <TextInput
                                id="code"
                                ref="codeInput"
                                v-model="form.code"
                                type="text"
                                inputmode="numeric"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                autofocus
                                autocomplete="one-time-code"
                            />
                            <InputError class="mt-2 text-sm text-red-600" :message="form.errors.code" />
                        </div>
                    </div>

                    <!-- Recovery Code Input -->
                    <div v-else>
                        <InputLabel for="recovery_code" value="Recovery Code" class="block text-sm font-medium text-gray-700" />
                        <div class="mt-1">
                            <TextInput
                                id="recovery_code"
                                ref="recoveryCodeInput"
                                v-model="form.recovery_code"
                                type="text"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                autocomplete="one-time-code"
                            />
                            <InputError class="mt-2 text-sm text-red-600" :message="form.errors.recovery_code" />
                        </div>
                    </div>

                    <!-- Toggle Button & Submit Button -->
                    <div class="flex items-center justify-between">
                        <button type="button" class="text-sm font-medium text-emerald-600 hover:text-emerald-500 focus:outline-none focus:underline" @click.prevent="toggleRecovery">
                            <template v-if="! recovery">
                                Use a recovery code
                            </template>
                            <template v-else>
                                Use an authentication code
                            </template>
                        </button>

                        <PrimaryButton
                            class="flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-300"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Log in
                        </PrimaryButton>
                        <!-- Or use a standard button styled like your template:
                        <button
                            type="submit"
                            class="py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm hover:shadow text-sm font-medium"
                            :disabled="form.processing"
                            :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        >
                           <span v-if="form.processing">Logging in...</span>
                           <span v-else>Log in</span>
                        </button>
                        -->
                    </div>
                </form>
            </div>
        </div>
    </AppLayout2>
</template>

<style scoped>
/* Add any specific styles for the two-factor challenge page content if needed */
</style>