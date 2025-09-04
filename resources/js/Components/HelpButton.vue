<template>
    <!-- Floating Help Button -->
    <div class="fixed bottom-4 right-4 z-40 sm:bottom-6 sm:right-6">
        <!-- Minimized State -->
        <button
            v-if="!isExpanded"
            @click.stop="toggleHelp"
            class="flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 text-white rounded-full shadow-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-300 font-medium text-sm"
            :class="{ 'animate-pulse': !helpUsed }"
            aria-label="Open Help"
        >
            <span class="text-lg">❓</span>
            <span class="hidden sm:inline">Help</span> <!-- Hide text on mobile -->
        </button>

        <!-- Expanded State -->
        <div
            v-else
            class="fixed inset-0 bg-white flex flex-col z-50 sm:static sm:w-96 sm:h-[500px] sm:rounded-2xl sm:shadow-2xl sm:border sm:border-gray-200"
            @click.stop="toggleHelp"
        >
            <!-- Header: Fixed at top with safe area padding -->
            <div class="flex-shrink-0 bg-emerald-50 border-b border-gray-200 px-4 py-3 relative">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-emerald-900 flex items-center">
                        <span class="text-emerald-600 mr-2">💡</span>
                        Help Center
                    </h3>
                    <button
                        @click.stop="toggleHelp"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                        aria-label="Close Help"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Optional: Add top padding for iPhone notch -->
                <div class="h-2 bg-emerald-50" :style="{ height: 'max(env(safe-area-inset-top, 0px), 8px)' }"></div>
            </div>

            <!-- Search Bar -->
            <div class="flex-shrink-0 p-3 border-b border-gray-100 bg-white">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search help topics..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400"
                    @click.stop
                />
            </div>

            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto p-1 bg-white" @click.stop>
                <div
                    v-for="topic in filteredTopics"
                    :key="topic.id"
                    class="border-b border-gray-100 last:border-b-0 rounded-md mx-1"
                >
                    <button
                        @click.stop="toggleTopic(topic.id)"
                        class="w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors flex items-center justify-between"
                    >
                        <div class="flex items-center">
                            <span class="text-lg mr-3">{{ topic.icon }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ topic.title }}</span>
                        </div>
                        <svg
                            class="w-4 h-4 text-gray-400 transition-transform duration-200"
                            :class="{ 'transform rotate-180': activeTopic === topic.id }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div v-if="activeTopic === topic.id" class="px-4 pb-3 pt-1">
                        <div class="text-sm text-gray-600 prose prose-sm max-w-none" v-html="topic.content"></div>
                    </div>
                </div>

                <div v-if="filteredTopics.length === 0" class="p-4 text-center text-gray-500 text-sm bg-gray-50 rounded-lg mx-2 mt-2">
                    No topics found matching "{{ searchQuery }}"
                </div>
            </div>

            <!-- Footer -->
            <div class="flex-shrink-0 p-3 border-t border-gray-200 bg-gray-50" @click.stop>
                <a
                    href="mailto:support@examportal.online"
                    class="text-xs text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5"
                    @click.stop
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Contact Support
                </a>
            </div>
            <div class="h-3 bg-gray-50" :style="{ height: 'max(env(safe-area-inset-bottom, 0px), 12px)' }"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { helpTopics } from './HelpData.js';

const isExpanded = ref(false);
const activeTopic = ref(null);
const searchQuery = ref('');
const helpUsed = ref(false);

const filteredTopics = computed(() => {
    if (!searchQuery.value) return helpTopics;
    
    const query = searchQuery.value.toLowerCase();
    return helpTopics.filter(topic => 
        topic.title.toLowerCase().includes(query) ||
        topic.content.toLowerCase().includes(query)
    );
});

const toggleHelp = () => {
    isExpanded.value = !isExpanded.value;
    if (isExpanded.value && !helpUsed.value) {
        helpUsed.value = true;
    }
    if (!isExpanded.value) {
        activeTopic.value = null;
        searchQuery.value = '';
    }
};

const toggleTopic = (topicId) => {
    activeTopic.value = activeTopic.value === topicId ? null : topicId;
};

const handleClickOutside = (event) => {
    const helpContainer = document.querySelector('.fixed.bottom-6.right-6');
    if (helpContainer && !helpContainer.contains(event.target) && isExpanded.value) {
        toggleHelp();
    }
};

onMounted(() => {
    setTimeout(() => {
        document.addEventListener('click', handleClickOutside);
    }, 100);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.prose ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.prose li {
    margin-bottom: 0.25rem;
    line-height: 1.4;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>