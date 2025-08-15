<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'; // Assuming AppLayout is used
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
// Removed unused imports: ActionMessage, FormSection, PrimaryButton, SecondaryButton

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    // Note: The actual sending logic depends on your backend route setup.
    // This just toggles the message. You might need to make an Inertia post request here.
    router.post(route('verification.send'), {}, {
        onSuccess: () => {
             verificationLinkSent.value = true;
        }
    });
    // verificationLinkSent.value = true; // Original line, kept for reference
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <!-- Assuming this component is used within AppLayout or similar -->
    <!-- <AppLayout title="Profile Information"> -->
        <!-- If not within AppLayout, uncomment the above and the closing tag -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="max-w-3xl">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Profile Information</h2>
                            <p class="text-gray-600 mb-6">Update your account's profile information and email address.</p>

                            <form @submit.prevent="updateProfileInformation">
                                <!-- Profile Photo -->
                                <div v-if="$page.props.jetstream?.managesProfilePhotos" class="mb-6">
                                    <InputLabel for="photo" value="Photo" class="mb-2" />

                                    <!-- Current Profile Photo / New Profile Photo Preview -->
                                    <div class="mt-2 flex items-center">
                                        <div v-show="!photoPreview" class="mr-4">
                                            <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                                        </div>

                                        <div v-show="photoPreview" class="mr-4">
                                            <span
                                                class="block rounded-full h-20 w-20 bg-cover bg-no-repeat bg-center"
                                                :style="'background-image: url(\'' + photoPreview + '\');'"
                                            />
                                        </div>

                                        <div>
                                            <input
                                                id="photo"
                                                ref="photoInput"
                                                type="file"
                                                class="hidden"
                                                @change="updatePhotoPreview"
                                            >

                                            <button
                                                type="button"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-25 transition ease-in-out duration-150"
                                                @click.prevent="selectNewPhoto"
                                            >
                                                Select A New Photo
                                            </button>

                                            <button
                                                v-if="user.profile_photo_path"
                                                type="button"
                                                class="ml-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-25 transition ease-in-out duration-150"
                                                @click.prevent="deletePhoto"
                                            >
                                                Remove Photo
                                            </button>
                                        </div>
                                    </div>

                                    <InputError :message="form.errors.photo" class="mt-2" />
                                </div>

                                <!-- Name -->
                                <div class="mb-6">
                                    <InputLabel for="name" value="Name" class="mb-2" />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        autocomplete="name"
                                    />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <!-- Email -->
                                <div class="mb-6">
                                    <InputLabel for="email" value="Email" class="mb-2" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        autocomplete="username"
                                    />
                                    <InputError :message="form.errors.email" class="mt-2" />

                                    <div v-if="$page.props.jetstream?.hasEmailVerification && user.email_verified_at === null">
                                        <p class="text-sm mt-2 text-gray-600">
                                            Your email address is unverified.

                                            <button
                                                type="button"
                                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                @click.prevent="sendEmailVerification"
                                            >
                                                Click here to re-send the verification email.
                                            </button>
                                        </p>

                                        <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                                            A new verification link has been sent to your email address.
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition ease-in-out duration-150"
                                    >
                                        <span v-if="form.processing">Saving...</span>
                                        <span v-else>Save</span>
                                    </button>

                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out"
                                        leave-to-class="opacity-0"
                                    >
                                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                                    </Transition>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- </AppLayout> -->
</template>

<style scoped>
/* Add any specific scoped styles if needed */
</style>