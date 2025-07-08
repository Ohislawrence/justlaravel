<script setup>
import { ref } from 'vue';

const props = defineProps({
    quiz: {
        type: Object,
        default: () => ({})
    },
    initialPools: {
        type: Array,
        default: () => []
    },
    availableQuestions: {
        type: Array,
        default: () => []
    }
});

// Initialize pools with proper fallbacks
const pools = ref(
    props.initialPools.map(p => ({
        id: p.id || null,
        name: p.name || `Pool ${props.initialPools.length + 1}`,
        description: p.description || '',
        questions_to_show: p.questions_to_show || 1,
        questions: Array.isArray(p.questions) 
            ? p.questions.map(q => q.id || q) 
            : []
    }))
);

// Initialize with one empty pool if no pools provided
if (pools.value.length === 0) {
    pools.value.push({
        name: 'Pool 1',
        questions_to_show: 1,
        questions: []
    });
}

const addPool = () => {
    pools.value.push({
        name: `Pool ${pools.value.length + 1}`,
        questions_to_show: 1,
        questions: []
    });
};

const removePool = (index) => {
    if (pools.value.length > 1) {
        pools.value.splice(index, 1);
    }
};

const isQuestionInOtherPool = (questionId, currentPoolIndex) => {
    return pools.value.some((pool, index) => 
        index !== currentPoolIndex && 
        pool.questions.includes(questionId)
    );
};

const getPoolData = () => {
    return pools.value.map(pool => ({
        name: pool.name,
        description: pool.description,
        questions_to_show: parseInt(pool.questions_to_show) || 1,
        questions: pool.questions
    }));
};

defineExpose({
    getPoolData
});
</script>

<template>
    <div class="mt-6">
        <h3 class="text-lg font-medium mb-4">Question Pools</h3>
        
        <div v-for="(pool, index) in pools" :key="index" class="mb-6 p-4 border rounded">
            <div class="flex justify-between items-center mb-2">
                <input 
                    v-model="pool.name" 
                    placeholder="Pool name" 
                    class="w-full p-2 border rounded"
                    required
                >
                <button 
                    @click="removePool(index)" 
                    class="ml-2 text-red-500"
                    :disabled="pools.length <= 1"
                >
                    Remove
                </button>
            </div>
            
            <div class="mb-4">
                <label class="block mb-1">Description (optional)</label>
                <textarea
                    v-model="pool.description"
                    placeholder="Pool description"
                    class="w-full p-2 border rounded"
                    rows="2"
                ></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block mb-1">Questions to show:</label>
                <input
                    v-model.number="pool.questions_to_show"
                    type="number"
                    min="1"
                    class="w-20 p-2 border rounded"
                    required
                >
            </div>
            
            <div>
                <label class="block mb-1">Select questions:</label>
                <select 
                    v-model="pool.questions" 
                    multiple 
                    class="w-full p-2 border rounded min-h-[100px]"
                    required
                >
                    <option 
                        v-for="question in availableQuestions" 
                        :key="question.id" 
                        :value="question.id"
                        :disabled="isQuestionInOtherPool(question.id, index)"
                        class="p-2"
                    >
                        {{ question.question }}
                        <span 
                            v-if="isQuestionInOtherPool(question.id, index)" 
                            class="text-gray-500"
                        >
                            (already in another pool)
                        </span>
                    </option>
                </select>
                <p class="text-sm text-gray-500 mt-1">
                    Hold Ctrl/Cmd to select multiple questions
                </p>
                <p v-if="availableQuestions.length === 0" class="text-sm text-red-500 mt-1">
                    No available questions found
                </p>
            </div>
        </div>
        
        <button 
            @click="addPool" 
            class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
            Add Question Pool
        </button>
    </div>
</template>