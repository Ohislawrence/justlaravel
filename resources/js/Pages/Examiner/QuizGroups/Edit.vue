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
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    organization: Object,
    group: Object,
    parentGroups: Array,
});

const { flash } = usePage().props;

const form = useForm({
    name: props.group.name,
    description: props.group.description,
    price: props.group.price,
    is_active: props.group.is_active,
    parent_id: props.group.parent_id,
});

const priceInput = ref(null);
const showPriceField = ref(!!props.group.price);
const showDeleteModal = ref(false);

const submit = () => {
    form.put(route('examiner.quiz-groups.update', {
        organization: props.organization.id,
        quiz_group: props.group.id
    }), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Add any success handling
        },
    });
};

const deleteGroup = () => {
    router.delete(route('examiner.quiz-groups.destroy', {
        organization: props.organization.id,
        quiz_group: props.group.id
    }), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            router.visit(route('examiner.quiz-groups.index', props.organization.id));
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

// Filter out current group and its descendants from parent group options
const filteredParentGroups = computed(() => {
    return props.parentGroups.filter(group => 
        group.id !== props.group.id && 
        (!props.group.parent_id || group.id !== props.group.parent_id)
    );
});
</script>

<template>
    <AppLayout :title="`Edit ${group.name}`">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Quiz Group: {{ group.name }}
                </h2>
                <div class="flex items-center space-x-2">
                    <Link 
                        :href="route('examiner.quiz-groups.show', { 
                            organization: organization.id, 
                            quiz_group: group.id 
                        })"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Back to Group
                    </Link>
                </div>
            </div>
        </template>

        <!-- Flash Messages -->
        <div v-if="flash.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ flash.success }}</span>
                <button
                    type="button" 
                    @click="flash.success = null"
                    class="absolute top-0 bottom-0 right-0 px-4 py-3"
                    aria-label="Close success message"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div v-if="flash.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ flash.error }}</span>
                <button
                    type="button" 
                    @click="flash.error = null"
                    class="absolute top-0 bottom-0 right-0 px-4 py-3"
                    aria-label="Close error message"
                >
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
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
                                        <div v-if="filteredParentGroups.length > 0">
                                            <InputLabel for="parent_id" value="Parent Group" />
                                            <SelectInput
                                                id="parent_id"
                                                v-model="form.parent_id"
                                                class="mt-1 block w-full"
                                            >
                                                <option :value="null">No parent group</option>
                                                <option 
                                                    v-for="group in filteredParentGroups" 
                                                    :key="group.id" 
                                                    :value="group.id"
                                                >
                                                    {{ group.name }}
                                                </option>
                                            </SelectInput>
                                            <InputError class="mt-2" :message="form.errors.parent_id" />
                                        </div>

                                        <!-- Current Parent (readonly if no other options) -->
                                        <div v-else-if="group.parent">
                                            <InputLabel value="Current Parent Group" />
                                            <div class="mt-1 p-2 bg-gray-50 rounded-md border border-gray-200">
                                                {{ group.parent.name }}
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">
                                                No other parent groups available
                                            </p>
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
                                                        class="block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
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
                                <div class="flex justify-between pt-6 border-t border-gray-200">
                                    <button
                                        type="button"
                                        @click="showDeleteModal = true"
                                        class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 flex items-center"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete Group
                                    </button>
                                    <div class="flex space-x-3">
                                        <Link
                                            :href="route('examiner.quiz-groups.show', { 
                                                organization: organization.id, 
                                                quiz_group: group.id 
                                            })"
                                            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                        >
                                            Cancel
                                        </Link>
                                        <button
                                            type="submit"
                                            class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 flex items-center"
                                            :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                                            :disabled="form.processing"
                                        >
                                            <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span v-if="form.processing" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Saving...
                                            </span>
                                            <span v-else>Save Changes</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>
                Delete Quiz Group
            </template>

            <template #content>
                <p class="text-gray-700">
                    Are you sure you want to delete this quiz group? 
                    <span class="font-semibold">This action cannot be undone.</span>
                </p>
                <p v-if="group.quizzes_count > 0" class="mt-2 text-red-600">
                    Warning: This group contains {{ group.quizzes_count }} quiz{{ group.quizzes_count !== 1 ? 'zes' : '' }}.
                    They will be removed from this group but not deleted.
                </p>
                <p v-if="group.children_count > 0" class="mt-2 text-red-600">
                    Warning: This group has {{ group.children_count }} subgroup{{ group.children_count !== 1 ? 's' : '' }}.
                    They will become root groups if you proceed.
                </p>
            </template>

            <template #footer>
                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="deleteGroup"
                        class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 flex items-center"
                        :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Deleting...
                        </span>
                        <span v-else>Delete Group</span>
                    </button>
                </div>
            </template>
        </ConfirmationModal>
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

/* Enhanced green-themed styling */
.bg-green-600 {
    background-color: #10B981;
}

.bg-green-700:hover {
    background-color: #059669;
}

.focus\:ring-green-500:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Green-themed checkbox */
input[type="checkbox"]:checked {
    background-color: #10B981;
    border-color: #10B981;
}

/* Green-themed select arrow */
select {
    background-image: url("image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2310B981' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}

/* Input focus states */
input:focus, select:focus, textarea:focus {
    border-color: #10B981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

/* Hover effects */
button:hover, .hover\:bg-gray-50:hover {
    transform: translateY(-1px);
    transition: all 0.3s ease;
}

/* Disabled state */
.opacity-25 {
    opacity: 0.5;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .px-6 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
}
</style>