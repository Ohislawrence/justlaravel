<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="$page.props.flash.message"
      class="mb-4 rounded-md p-4"
      :class="{
        'bg-green-50 text-green-800': $page.props.flash.type === 'success',
        'bg-red-50 text-red-800': $page.props.flash.type === 'error',
        'bg-blue-50 text-blue-800': !$page.props.flash.type
      }"
    >
      <div class="flex">
        <div class="flex-shrink-0">
          <CheckCircleIcon
            v-if="$page.props.flash.type === 'success'"
            class="h-5 w-5 text-green-400"
          />
          <XCircleIcon
            v-else-if="$page.props.flash.type === 'error'"
            class="h-5 w-5 text-red-400"
          />
          <InformationCircleIcon
            v-else
            class="h-5 w-5 text-blue-400"
          />
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium">
            {{ $page.props.flash.message }}
          </p>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import {
  CheckCircleIcon,
  XCircleIcon,
  InformationCircleIcon
} from '@heroicons/vue/20/solid';
</script>