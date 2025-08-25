<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    organization: Object,
    group: Object,
    show: Boolean
});

const emit = defineEmits(['close', 'imported']);

const form = useForm({
    import_file: null,
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
});

const importResults = ref(null);
const isLoading = ref(false);

const downloadTemplate = () => {
    window.location.href = route('examiner.groups.importTemplate');
};

const submitImport = async () => {
    if (!form.import_file) {
        alert('Please select a file to import');
        return;
    }

    isLoading.value = true;
    
    try {
        const response = await axios.post(route('examiner.groups.importUsers', {
            group: props.group.id
        }), form, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        importResults.value = response.data;
        form.reset();
        
        if (response.data.results.success_count > 0) {
            emit('imported');
        }
        
    } catch (error) {
        if (error.response?.data?.errors) {
            importResults.value = {
                errors: error.response.data.errors
            };
        } else {
            alert('Import failed: ' + error.message);
        }
    } finally {
        isLoading.value = false;
    }
};

const closeModal = () => {
    form.reset();
    importResults.value = null;
    emit('close');
};

const handleFileChange = (event) => {
    form.import_file = event.target.files[0];
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-200 scale-100">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import Users to {{ group.name }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500 transition-colors duration-150">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-6 py-5">
                <!-- Instructions -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-green-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Import Instructions
                    </h4>
                    <p class="text-sm text-green-700 mb-3">
                        Upload a CSV file with columns: <strong>name</strong> and <strong>email</strong>.
                        Passwords will be automatically generated from names.
                    </p>
                    <button @click="downloadTemplate" 
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-md transition-colors duration-150">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template
                    </button>
                </div>

                <!-- Import Form -->
                <form @submit.prevent="submitImport" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                        <div class="relative">
                            <input 
                                type="file" 
                                @change="handleFileChange"
                                accept=".csv,.xlsx,.xls"
                                class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 cursor-pointer"
                                required
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v12" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Supported formats: CSV, Excel (.xlsx, .xls)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                        <div class="relative">
                            <select v-model="form.timezone" class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option v-for="timezone in $page.props.timezones" :value="timezone" :key="timezone">
                                    {{ timezone }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="closeModal" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                        <button type="submit" :disabled="isLoading || !form.import_file"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="isLoading">Importing...</span>
                            <span v-else>
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import Users
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Results -->
                <div v-if="importResults" class="mt-6 border-t border-gray-100 pt-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Import Results
                    </h4>
                    
                    <div v-if="importResults.results?.success_count > 0" 
                         class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-green-800 font-medium">
                                Successfully imported {{ importResults.results.success_count }} users
                            </span>
                        </div>
                    </div>

                    <div v-if="importResults.results?.error_count > 0" 
                         class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-yellow-800 font-medium">
                                {{ importResults.results.error_count }} errors occurred
                            </span>
                        </div>
                    </div>

                    <div v-if="importResults.errors && importResults.errors.length > 0" 
                         class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h5 class="font-medium text-red-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.362 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Errors:
                        </h5>
                        <ul class="text-sm text-red-700 space-y-1 max-h-32 overflow-y-auto">
                            <li v-for="(error, index) in importResults.errors" :key="index" class="flex items-start">
                                <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
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

/* Green-themed text */
.text-green-600 {
    color: #10B981;
}

.text-green-700 {
    color: #065f46;
}

.text-green-800 {
    color: #065f46;
}

/* Green-themed backgrounds */
.bg-green-50 {
    background-color: #ecfdf5;
}

/* Green-themed borders */
.border-green-200 {
    border-color: #a7f3d0;
}

/* Transition effects */
.transition-colors {
    transition: all 0.15s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .max-w-2xl {
        width: 95%;
    }
}
</style>