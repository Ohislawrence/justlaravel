<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    organization: Object,
    parentGroups: Array,
});

const { flash } = usePage().props;

const form = useForm({
    name: '',
    description: '',
    price: null,
    is_active: true,
    parent_id: null,
});

const priceInput = ref(null);
const showPriceField = ref(false);

const submit = () => {
    form.post(route('examiner.quiz-groups.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const formatPrice = () => {
    if (form.price) {
        // Remove any existing formatting
        let value = form.price.toString().replace(/[^0-9.]/g, '');
        // Format as currency
        form.price = parseFloat(value).toFixed(2);
    }
};

const togglePriceField = () => {
    showPriceField.value = !showPriceField.value;
    if (showPriceField.value && priceInput.value) {
        priceInput.value.focus();
    } else {
        form.price = null;
    }
};
</script>

<template>
    <AppLayout title="Create Quiz Group">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create New Quiz Group
                </h2>
                <div class="flex items-center space-x-2">
                    <Link 
                        :href="route('examiner.quiz-groups.index', props.organization.id)"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Back to Groups
                    </Link>
                </div>
            </div>
        </template>

        <!-- Flash Messages -->
        <div v-if="flash.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ flash.success }}
            </div>
        </div>

        <div v-if="flash.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ flash.error }}
            </div>
        </div>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <!-- Basic Information Section -->
                                <div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                        Basic Information
                                    </h3>
                                    <div class="grid grid-cols-1 gap-4">
                                        <!-- Name -->
                                        <div>
                                            <InputLabel for="name" value="Group Name *" />
                                            <TextInput
                                                id="name"
                                                v-model="form.name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                                autofocus
                                            />
                                            <InputError class="mt-2" :message="form.errors.name" />
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <InputLabel for="description" value="Description" />
                                            <Textarea
                                                id="description"
                                                v-model="form.description"
                                                class="mt-1 block w-full"
                                                rows="3"
                                            />
                                            <InputError class="mt-2" :message="form.errors.description" />
                                        </div>

                                        <!-- Parent Group -->
                                        <div v-if="parentGroups.length > 0">
                                            <InputLabel for="parent_id" value="Parent Group (optional)" />
                                            <SelectInput
                                                id="parent_id"
                                                v-model="form.parent_id"
                                                class="mt-1 block w-full"
                                            >
                                                <option :value="null">No parent group</option>
                                                <option 
                                                    v-for="group in parentGroups" 
                                                    :key="group.id" 
                                                    :value="group.id"
                                                >
                                                    {{ group.name }}
                                                </option>
                                            </SelectInput>
                                            <InputError class="mt-2" :message="form.errors.parent_id" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing Section -->
                                <div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                        Pricing Options
                                    </h3>
                                    <div class="space-y-4">
                                        <!-- Free/Paid Toggle -->
                                        <div class="flex items-center">
                                            <Checkbox 
                                                id="has_price" 
                                                v-model:checked="showPriceField" 
                                                @change="togglePriceField"
                                            />
                                            <InputLabel for="has_price" value="This is a paid group" class="ml-2" />
                                        </div>

                                        <!-- Price Input -->
                                        <div v-if="showPriceField" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel for="price" value="Price (USD)" />
                                                <div class="mt-1 relative rounded-md shadow-sm">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">$</span>
                                                    </div>
                                                    <TextInput
                                                        ref="priceInput"
                                                        id="price"
                                                        v-model="form.price"
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        class="block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                                        @blur="formatPrice"
                                                    />
                                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">USD</span>
                                                    </div>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    Set a price if this group contains premium content
                                                </p>
                                                <InputError class="mt-2" :message="form.errors.price" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Section -->
                                <div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                        Status
                                    </h3>
                                    <div class="flex items-center">
                                        <Checkbox id="is_active" v-model:checked="form.is_active" />
                                        <InputLabel for="is_active" value="Active (visible to users)" class="ml-2" />
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Inactive groups won't be visible to students
                                    </p>
                                </div>

                                <!-- Form Actions -->
                                <div class="flex justify-end pt-6 border-t border-gray-200">
                                    <Link
                                        :href="route('examiner.quiz-groups.index', props.organization.id)"
                                        class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Cancel
                                    </Link>
                                    <PrimaryButton
                                        type="submit"
                                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                    >
                                        Create Group
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom styling for the price input */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}
</style>