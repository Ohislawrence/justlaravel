<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    gradingSystem: Object,
    defaultSystems: Array
});

const form = useForm({
    name: props.gradingSystem.name,
    type: props.gradingSystem.type,
    description: props.gradingSystem.description || '',
    grade_ranges: JSON.parse(JSON.stringify(props.gradingSystem.grade_ranges))
});

const addGradeRange = () => {
    form.grade_ranges.push({ min: 0, max: 0, grade: '', points: null, interpretation: '' });
};

const removeGradeRange = (index) => {
    if (form.grade_ranges.length > 1) {
        form.grade_ranges.splice(index, 1);
    }
};

const loadDefaultSystem = (systemId) => {
    const defaultSystem = props.defaultSystems.find(s => s.id == systemId);
    if (defaultSystem) {
        form.name = defaultSystem.name;
        form.type = defaultSystem.type;
        form.grade_ranges = JSON.parse(JSON.stringify(defaultSystem.grade_ranges));
    }
};

const submit = () => {
    form.put(route('examiner.grading-systems.update', props.gradingSystem.id));
};

const cancel = () => {
    router.visit(route('examiner.grading-systems.index'));
};
</script>

<template>
    <AppLayout :title="`Edit Grading System - ${gradingSystem.name}`">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Grading System: {{ gradingSystem.name }}
                </h2>
                <a 
                    :href="route('examiner.grading-systems.index')" 
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors duration-150"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Grading Systems
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Load Default Template -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="font-medium text-gray-900 mb-2">Load Default Template</div>
                                    <div>
                                        <select 
                                            @change="loadDefaultSystem($event.target.value)" 
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                        >
                                            <option value="">Select a template to load</option>
                                            <option v-for="system in defaultSystems" :key="system.id" :value="system.id">
                                                {{ system.name }} ({{ system.type }})
                                            </option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Warning: Loading a template will replace your current grade ranges.
                                        </p>
                                    </div>
                                </div>

                                <!-- Basic Information -->
                                <div>
                                    <InputLabel for="name" value="System Name *" />
                                    <TextInput 
                                        id="name" 
                                        v-model="form.name" 
                                        class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                        required 
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="type" value="System Type *" />
                                    <select 
                                        id="type" 
                                        v-model="form.type" 
                                        class="mt-1 block w-full px-4 py-2.5 border border-gray-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                        required
                                    >
                                        <option value="4-point">4-Point Scale</option>
                                        <option value="5-point">5-Point Scale</option>
                                        <option value="waec">WAEC System</option>
                                        <option value="pass-fail">Pass/Fail</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.type" />
                                </div>

                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <Textarea 
                                        id="description" 
                                        v-model="form.description" 
                                        class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                        rows="3"
                                    />
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <!-- Grade Ranges -->
                                <div>
                                    <div class="flex justify-between items-center mb-4">
                                        <InputLabel value="Grade Ranges *" />
                                        <button 
                                            type="button" 
                                            @click="addGradeRange" 
                                            class="inline-flex items-center text-sm text-green-600 hover:text-green-800 transition-colors duration-150"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Add Range
                                        </button>
                                    </div>
                                    
                                    <div v-for="(range, index) in form.grade_ranges" :key="index" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-3 p-4 border border-gray-200 rounded-lg bg-white shadow-sm hover:shadow-md transition-all duration-200">
                                        <div>
                                            <InputLabel :for="`min-${index}`" value="Min %" />
                                            <TextInput 
                                                :id="`min-${index}`" 
                                                v-model.number="range.min" 
                                                type="number" 
                                                min="0" 
                                                max="100" 
                                                class="w-full focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                                required 
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="`max-${index}`" value="Max %" />
                                            <TextInput 
                                                :id="`max-${index}`" 
                                                v-model.number="range.max" 
                                                type="number" 
                                                min="0" 
                                                max="100" 
                                                class="w-full focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                                required 
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="`grade-${index}`" value="Grade" />
                                            <TextInput 
                                                :id="`grade-${index}`" 
                                                v-model="range.grade" 
                                                class="w-full focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                                required 
                                                maxlength="10"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="`points-${index}`" value="Points" />
                                            <TextInput 
                                                :id="`points-${index}`" 
                                                v-model.number="range.points" 
                                                type="number" 
                                                step="0.1" 
                                                min="0" 
                                                class="w-full focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                                placeholder="Optional"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="`interpretation-${index}`" value="Interpretation" />
                                            <TextInput 
                                                :id="`interpretation-${index}`" 
                                                v-model="range.interpretation" 
                                                class="w-full focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                                placeholder="Optional"
                                                maxlength="255"
                                            />
                                        </div>
                                        <div class="md:col-span-5 flex justify-end">
                                            <button 
                                                type="button" 
                                                @click="removeGradeRange(index)" 
                                                :disabled="form.grade_ranges.length <= 1"
                                                :class="{ 'opacity-50 cursor-not-allowed': form.grade_ranges.length <= 1 }"
                                                class="inline-flex items-center text-sm text-red-600 hover:text-red-800 transition-colors duration-150"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.grade_ranges" />
                                    <p class="text-xs text-gray-500 mt-2">
                                        Ensure grade ranges cover 0-100% without gaps or overlaps.
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                                    <div>
                                        <span class="text-sm text-gray-500">
                                            Created: {{ new Date(gradingSystem.created_at).toLocaleDateString() }}
                                        </span>
                                        <span v-if="gradingSystem.updated_at !== gradingSystem.created_at" class="text-sm text-gray-500 ml-4">
                                            Updated: {{ new Date(gradingSystem.updated_at).toLocaleDateString() }}
                                        </span>
                                    </div>
                                    <div class="flex space-x-3">
                                        <button 
                                            type="button" 
                                            @click="cancel"
                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                        >
                                            Cancel
                                        </button>
                                        <button 
                                            type="submit" 
                                            :disabled="form.processing"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200"
                                        >
                                            <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span v-if="form.processing" class="flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Updating...
                                            </span>
                                            <span v-else>Update Grading System</span>
                                        </button>
                                    </div>
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

.text-green-800 {
    color: #065f46;
}

/* Border styling */
.border-gray-100 {
    border-color: #f3f4f6;
}

/* Transition effects */
.transition-colors {
    transition: all 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .gap-6 > *:not(:last-child) {
        margin-bottom: 1.5rem;
    }
}
</style>