<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
// Removed unused imports: ActionSection, ConfirmsPassword, PrimaryButton, SecondaryButton, DangerButton
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => !enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    // This part requires password confirmation logic.
    // A simple approach is to prompt the user before proceeding.
    // A more robust solution would integrate a password confirmation modal/popup.
    if (!confirm('Please confirm your password to enable 2FA.')) {
        return;
    }

    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
     if (!confirm('Please confirm your password to regenerate recovery codes.')) {
        return;
    }
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    // This part requires password confirmation logic.
     if (!confirm('Please confirm your password to disable 2FA.')) {
        return;
    }

    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
    
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="max-w-3xl">
                        <!-- Header -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Two Factor Authentication</h2>
                        <p class="text-gray-600 mb-6">Add additional security to your account using two factor authentication.</p>

                        <!-- Status Message -->
                        <h3 v-if="twoFactorEnabled && !confirming" class="text-lg font-medium text-gray-900">
                            You have enabled two factor authentication.
                        </h3>

                        <h3 v-else-if="twoFactorEnabled && confirming" class="text-lg font-medium text-gray-900">
                            Finish enabling two factor authentication.
                        </h3>

                        <h3 v-else class="text-lg font-medium text-gray-900">
                            You have not enabled two factor authentication.
                        </h3>

                        <!-- Description -->
                        <div class="mt-3 max-w-xl text-sm text-gray-600">
                            <p>
                                When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                            </p>
                        </div>

                        <!-- 2FA Enabled Content -->
                        <div v-if="twoFactorEnabled">
                            <!-- QR Code & Setup Key -->
                            <div v-if="qrCode">
                                <div class="mt-4 max-w-xl text-sm text-gray-600">
                                    <p v-if="confirming" class="font-semibold">
                                        To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.
                                    </p>

                                    <p v-else>
                                        Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application or enter the setup key.
                                    </p>
                                </div>

                                <div class="mt-4 p-2 inline-block bg-white border border-gray-200 rounded-md" v-html="qrCode" />

                                <div v-if="setupKey" class="mt-4 max-w-xl text-sm text-gray-600">
                                    <p class="font-semibold">
                                        Setup Key: <span v-html="setupKey" class="font-mono bg-gray-100 px-1 py-0.5 rounded"></span>
                                    </p>
                                </div>

                                <!-- Confirmation Input -->
                                <div v-if="confirming" class="mt-4 max-w-md">
                                    <InputLabel for="code" value="Code" class="mb-2" />

                                    <TextInput
                                        id="code"
                                        v-model="confirmationForm.code"
                                        type="text"
                                        name="code"
                                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        inputmode="numeric"
                                        autofocus
                                        autocomplete="one-time-code"
                                        @keyup.enter="confirmTwoFactorAuthentication"
                                    />

                                    <InputError :message="confirmationForm.errors.code" class="mt-2" />
                                </div>
                            </div>

                            <!-- Recovery Codes -->
                            <div v-if="recoveryCodes.length > 0 && !confirming">
                                <div class="mt-6 max-w-xl text-sm text-gray-600">
                                    <p class="font-semibold">
                                        Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                                    </p>
                                </div>

                                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                                    <div v-for="code in recoveryCodes" :key="code">
                                        {{ code }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <!-- Enable Button -->
                            <div v-if="!twoFactorEnabled">
                                <button
                                    type="button"
                                    @click="enableTwoFactorAuthentication"
                                    :disabled="enabling"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': enabling,
                                        'inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500': !enabling
                                    }"
                                >
                                    <span v-if="enabling">Enabling...</span>
                                    <span v-else>Enable</span>
                                </button>
                            </div>

                            <!-- Buttons when 2FA is Enabled -->
                            <div v-else class="flex flex-wrap items-center gap-3">
                                <!-- Confirm Button -->
                                <button
                                    v-if="confirming"
                                    type="button"
                                    @click="confirmTwoFactorAuthentication"
                                    :disabled="enabling"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': enabling,
                                        'inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500': !enabling
                                    }"
                                >
                                    Confirm
                                </button>

                                <!-- Regenerate Recovery Codes Button -->
                                <button
                                    v-if="recoveryCodes.length > 0 && !confirming"
                                    type="button"
                                    @click="regenerateRecoveryCodes"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Regenerate Recovery Codes
                                </button>

                                <!-- Show Recovery Codes Button -->
                                <button
                                    v-if="recoveryCodes.length === 0 && !confirming"
                                    type="button"
                                    @click="showRecoveryCodes"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Show Recovery Codes
                                </button>

                                <!-- Cancel Button (during confirmation) -->
                                <button
                                    v-if="confirming"
                                    type="button"
                                    @click="disableTwoFactorAuthentication" <!-- Or a simple reset if canceling setup -->
                                    :disabled="disabling"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': disabling,
                                        'inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500': !disabling
                                    }"
                                >
                                    Cancel
                                </button>

                                <!-- Disable Button -->
                                <button
                                    v-if="!confirming"
                                    type="button"
                                    @click="disableTwoFactorAuthentication"
                                    :disabled="disabling"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': disabling,
                                        'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500': !disabling
                                    }"
                                >
                                    <span v-if="disabling">Disabling...</span>
                                    <span v-else>Disable</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
          
</template>

<style scoped>
/* Add any specific scoped styles if needed */
</style>