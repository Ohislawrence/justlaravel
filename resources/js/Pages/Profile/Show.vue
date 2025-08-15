<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
// Removed unused import: SectionBorder
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="space-y-4">
                    <div v-if="$page.props.jetstream?.canUpdateProfileInformation">
                        <UpdateProfileInformationForm :user="$page.props.auth.user" />
                    </div>

                    <div v-if="$page.props.jetstream?.canUpdatePassword">
                        <UpdatePasswordForm />
                    </div>

                    <div v-if="$page.props.jetstream?.canManageTwoFactorAuthentication">
                        <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
                    </div>

                    <div>
                        <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                    </div>

                    <div v-if="$page.props.jetstream?.hasAccountDeletionFeatures">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Add any specific scoped styles if needed */
</style>