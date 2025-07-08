<script setup>
import { computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    modelValue: [String, Number, Array],
    id: String,
    label: String,
    multiple: Boolean,
    placeholder: {
        type: String,
        default: 'Select an option'
    },
    required: Boolean,
    disabled: Boolean,
    error: String,
    showError: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (event) => {
    const value = props.multiple 
        ? Array.from(event.target.selectedOptions).map(option => option.value)
        : event.target.value;
    
    emit('update:modelValue', value);
};
</script>

<template>
    <div class="space-y-1">
        <InputLabel v-if="label" :for="id" :value="label" :required="required" />

        <select
            :id="id"
            :value="modelValue"
            @change="updateValue"
            :multiple="multiple"
            :disabled="disabled"
            :class="[
                'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white',
                error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : ''
            ]"
        >
            <option v-if="!multiple" value="" disabled selected hidden>
                {{ placeholder }}
            </option>

            <slot />
        </select>

        <InputError v-if="showError && error" :message="error" class="mt-1" />
    </div>
</template>