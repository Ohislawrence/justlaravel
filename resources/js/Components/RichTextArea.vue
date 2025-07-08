<template>
  <div>
    <InputLabel :for="id" :value="label" />

    <div v-if="editor" class="menu-bar border-b border-gray-200 mb-2 flex flex-wrap gap-1 p-1">
      <template v-for="(btn, i) in toolbarButtons" :key="i">
        <button
          v-if="btn.icon"
          :title="btn.title"
          :disabled="btn.disable && !btn.disable()"
          @click="btn.action"
          type="button"
          :class="[
            baseBtnClass,
            btn.isActive?.() ? activeBtnClass : ''
          ]"
        >
          <component :is="btn.icon" class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
        </button>
        <button
          v-else
          :title="btn.title"
          :disabled="btn.disable && !btn.disable()"
          @click="btn.action"
          type="button"
          v-text="btn.title"
          :class="[
            baseBtnClass,
            btn.isActive?.() ? activeBtnClass : ''
          ]"
        />
      </template>
    </div>

    <editor-content
      :editor="editor"
      class="prose max-w-none border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[150px] p-2"
      :class="{ 'border-red-500': error }"
    />

    <InputError v-if="error" class="mt-2" :message="error" />
  </div>
</template>


<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import BulletList from '@tiptap/extension-bullet-list'
import OrderedList from '@tiptap/extension-ordered-list'
import ListItem from '@tiptap/extension-list-item'
import { watch, computed } from 'vue'

import { ArrowUturnLeftIcon, ArrowUturnRightIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  modelValue: String,
  id: String,
  label: String,
  error: String,
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [StarterKit, BulletList, OrderedList, ListItem],
  onUpdate: () => {
    emit('update:modelValue', editor.value.getHTML())
  },
})

watch(() => props.modelValue, (value) => {
  if (editor.value && editor.value.getHTML() !== value) {
    editor.value.commands.setContent(value, false)
  }
})

const baseBtnClass =
  'm-1 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10'
const activeBtnClass = 'ring-indigo-900 ring-2'

const toolbarButtons = computed(() => {
  if (!editor.value) return []

  const e = editor.value
  return [
    {
      title: 'bold',
      action: () => e.chain().focus().toggleBold().run(),
      isActive: () => e.isActive('bold'),
    },
    {
      title: 'italic',
      action: () => e.chain().focus().toggleItalic().run(),
      isActive: () => e.isActive('italic'),
    },
    {
      title: 'strike',
      action: () => e.chain().focus().toggleStrike().run(),
      isActive: () => e.isActive('strike'),
      disable: () => e.can().chain().focus().toggleStrike().run(),
    },
    {
      title: 'paragraph',
      action: () => e.chain().focus().setParagraph().run(),
      isActive: () => e.isActive('paragraph'),
    },
    {
      title: 'h1',
      action: () => e.chain().focus().toggleHeading({ level: 1 }).run(),
      isActive: () => e.isActive('heading', { level: 1 }),
    },
    {
      title: 'h2',
      action: () => e.chain().focus().toggleHeading({ level: 2 }).run(),
      isActive: () => e.isActive('heading', { level: 2 }),
    },
    {
      title: 'h3',
      action: () => e.chain().focus().toggleHeading({ level: 3 }).run(),
      isActive: () => e.isActive('heading', { level: 3 }),
    },
    {
      title: 'bullet list',
      action: () => e.chain().focus().toggleBulletList().run(),
      isActive: () => e.isActive('bulletList'),
    },
    {
      title: 'ordered list',
      action: () => e.chain().focus().toggleOrderedList().run(),
      isActive: () => e.isActive('orderedList'),
    },
    {
      title: 'blockquote',
      action: () => e.chain().focus().toggleBlockquote().run(),
      isActive: () => e.isActive('blockquote'),
    },
    {
      title: 'horizontal rule',
      action: () => e.chain().focus().setHorizontalRule().run(),
    },
    {
      title: 'hard break',
      action: () => e.chain().focus().setHardBreak().run(),
    },
    {
      title: 'undo',
      icon: ArrowUturnLeftIcon,
      action: () => e.chain().focus().undo().run(),
      disable: () => e.can().chain().focus().undo().run(),
    },
    {
      title: 'redo',
      icon: ArrowUturnRightIcon,
      action: () => e.chain().focus().redo().run(),
      disable: () => e.can().chain().focus().redo().run(),
    },
  ]
})
</script>


<style>
</style>