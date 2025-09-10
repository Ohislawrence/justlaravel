<template>
    <Transition
      enter-active-class="duration-150 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-100 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-show="show" class="md:hidden fixed inset-0 z-40">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="close" />
        <div class="fixed inset-y-0 right-0 max-w-full flex">
          <div class="relative w-screen max-w-xs">
            <div class="absolute top-0 left-0 -ml-8 pt-4 pr-2 flex sm:-ml-10 sm:pr-4">
              <button
                type="button"
                class="rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                @click="close"
              >
                <span class="sr-only">Close menu</span>
                <XMarkIcon class="h-6 w-6" />
              </button>
            </div>
            <div class="h-full bg-white shadow-xl overflow-y-auto">
              <div class="px-4 pt-5 pb-6 flex items-center">
                <img class="h-8 w-auto" src="/images/logo.png" alt="ExamPortal" />
              </div>
              <div class="mt-6 px-2 space-y-1">
                <Link
                  v-for="item in navigation"
                  :key="item.name"
                  :href="item.href"
                  class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                  @click="close"
                >
                  {{ item.name }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </template>
  
  <script setup>
  import { XMarkIcon } from '@heroicons/vue/24/outline';
  import { Link } from '@inertiajs/vue3';
  
  defineProps({
    show: Boolean
  });
  
  const emit = defineEmits(['close']);
  
  const close = () => {
    emit('close');
  };
  
  const navigation = [
    { name: 'My Dashboard', href: route('examinee.dashboard') },
    { name: 'My Quizzes', href: route('examinee.quizzes.index') },
    { name: 'My History', href: route('examinee.history') },
  ];
  </script>