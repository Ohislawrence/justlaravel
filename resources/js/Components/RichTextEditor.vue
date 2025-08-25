<template>
    <div class="rich-text-editor">
      <!-- Toolbar -->
      <div class="toolbar border border-gray-300 rounded-t bg-gray-50 p-1 flex flex-wrap gap-1">
        <!-- Text Formatting -->
        <button type="button" @click="execCommand('bold')" title="Bold" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M7 12h14M5 18h14" />
          </svg>
        </button>
        <button type="button" @click="execCommand('italic')" title="Italic" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 5h10M7 19h10m-7-8h4" />
          </svg>
        </button>
        <button type="button" @click="execCommand('underline')" title="Underline" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19V5h4v7a4 4 0 008 0V5h4v14h-4v-7a4 4 0 00-8 0v7H5z" />
          </svg>
        </button>
  
        <!-- Text Alignment -->
        <div class="border-l border-gray-300 h-6 mx-1"></div>
        <button type="button" @click="execCommand('justifyLeft')" title="Align Left" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M3 18h18M3 6h18" />
          </svg>
        </button>
        <button type="button" @click="execCommand('justifyCenter')" title="Center" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <button type="button" @click="execCommand('justifyRight')" title="Align Right" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M7 12h10M3 18h18" />
          </svg>
        </button>
  
        <!-- Lists -->
        <div class="border-l border-gray-300 h-6 mx-1"></div>
        <button type="button" @click="execCommand('insertUnorderedList')" title="Bullet List" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <button type="button" @click="execCommand('insertOrderedList')" title="Numbered List" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4l4 4" />
          </svg>
        </button>
        <button type="button" @click="insertImage" title="Insert Image" class="p-1 rounded hover:bg-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </button>
  
        <!-- Text Color -->
        <div class="border-l border-gray-300 h-6 mx-1"></div>
        <div class="relative">
          <button type="button" @click="toggleColorPicker" title="Text Color" class="p-1 rounded hover:bg-gray-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <div class="h-4 w-4 ml-1 border border-gray-300" :style="{ backgroundColor: textColor }"></div>
          </button>
          <div v-if="showColorPicker" class="absolute z-10 mt-1 bg-white p-2 border rounded shadow-lg">
            <div class="grid grid-cols-6 gap-1">
              <div v-for="color in colors" :key="color" 
                   class="w-6 h-6 cursor-pointer border border-gray-200"
                   :style="{ backgroundColor: color }"
                   @click="setTextColor(color)"></div>
            </div>
            <input type="color" v-model="textColor" @input="setTextColor(textColor)" class="mt-2 w-full">
          </div>
        </div>
  
        <!-- Font Size -->
        <div class="border-l border-gray-300 h-6 mx-1"></div>
        <select v-model="fontSize" @change="setFontSize" class="text-xs p-1 border rounded bg-white">
          <option value="">Size</option>
          <option v-for="size in fontSizes" :key="size" :value="size">{{ size }}</option>
        </select>
  
        <!-- Font Family -->
        <select v-model="fontFamily" @change="setFontFamily" class="text-xs p-1 border rounded bg-white">
          <option value="">Font</option>
          <option v-for="font in fonts" :key="font" :value="font">{{ font }}</option>
        </select>
  
        <!-- Link -->
        <div class="border-l border-gray-300 h-6 mx-1"></div>
        <button type="button" @click="insertLink" title="Insert Link" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
          </svg>
        </button>
  
        <!-- Clear Formatting -->
        <div class="border-l border-gray-300 h-6 mx-1"></div>
        <button type="button" @click="execCommand('removeFormat')" title="Clear Formatting" class="p-1 rounded hover:bg-gray-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
  
      <!-- Editor Area -->
      <div
        ref="editor"
        class="editor border border-gray-300 border-t-0 rounded-b p-3 min-h-[200px] max-h-[500px] overflow-y-auto focus:outline-none focus:ring-1 focus:ring-blue-500"
        :class="{ 'border-red-500': error }"
        contenteditable="true"
        @input="handleInput"
        @focus="emit('focus')"
        @blur="emit('blur')"
        v-html="modelValue"
      ></div>
  
      <!-- Error Message -->
      <InputError v-if="error" class="mt-1" :message="error" />
    </div>
  </template>
  
  <script setup>
  import { ref, watch, nextTick, onMounted } from 'vue';
  import InputError from '@/Components/InputError.vue';
  
  const props = defineProps({
    modelValue: {
      type: String,
      default: ''
    },
    error: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    }
  });

  const insertImage = () => {
    const url = prompt('Enter the image URL:', 'https://');
    if (url) {
        execCommand('insertImage', url);
    }
};
  
  const emit = defineEmits(['update:modelValue', 'focus', 'blur']);

  

  
  const editor = ref(null);
  let lastSelection = null;
  const showColorPicker = ref(false);
  const textColor = ref('#000000');
  const fontSize = ref('');
  const fontFamily = ref('');
  const linkUrl = ref('');

  // Save cursor position
const saveSelection = () => {
  const selection = window.getSelection();
  if (selection.rangeCount > 0) {
    lastSelection = selection.getRangeAt(0);
  }
};

// Restore cursor position
const restoreSelection = () => {
  if (lastSelection) {
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(lastSelection);
  }
};


  
  const colors = [
    '#000000', '#ffffff', '#ff0000', '#00ff00', '#0000ff', 
    '#ffff00', '#00ffff', '#ff00ff', '#c0c0c0', '#808080',
    '#800000', '#808000', '#008000', '#800080', '#008080', '#000080'
  ];
  
  const fontSizes = ['1', '2', '3', '4', '5', '6', '7'];
  const fonts = ['Arial', 'Courier New', 'Georgia', 'Times New Roman', 'Verdana'];
  
  onMounted(() => {
    if (props.placeholder && !props.modelValue) {
      editor.value.innerHTML = props.placeholder;
    }
  });
  

  
  const handleInput = () => {
  saveSelection();
  emit('update:modelValue', editor.value.innerHTML);
  nextTick(restoreSelection);
};

watch(() => props.modelValue, (newValue) => {
  if (editor.value && editor.value.innerHTML !== newValue) {
    saveSelection();
    editor.value.innerHTML = newValue;
    nextTick(restoreSelection);
  }
});
  
  const execCommand = (command, value = null) => {
    document.execCommand(command, false, value);
    editor.value.focus();
  };
  
  const toggleColorPicker = () => {
    showColorPicker.value = !showColorPicker.value;
  };
  
  const setTextColor = (color) => {
    execCommand('foreColor', color);
    textColor.value = color;
    showColorPicker.value = false;
  };
  
  const setFontSize = () => {
    if (fontSize.value) {
      execCommand('fontSize', fontSize.value);
    }
  };
  
  const setFontFamily = () => {
    if (fontFamily.value) {
      execCommand('fontName', fontFamily.value);
    }
  };
  
  const insertLink = () => {
    const url = prompt('Enter the URL:', 'http://');
    if (url) {
      execCommand('createLink', url);
    }
  };
  
  const focus = () => {
    editor.value.focus();
  };
  
  defineExpose({
    focus
  });
  </script>
  
  <style scoped>
  .rich-text-editor {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  .toolbar {
    user-select: none;
  }
  
  .editor {
    line-height: 1.5;
  }
  
  .editor:empty:before {
    content: attr(placeholder);
    color: #9ca3af;
    pointer-events: none;
    display: block;
  }
  
  .editor:focus {
    outline: none;
  }
  
  /* Style the content inside the editor */
  .editor h1, .editor h2, .editor h3, .editor h4, .editor h5, .editor h6 {
    margin: 0.5em 0;
    font-weight: bold;
  }
  
  .editor ul, .editor ol {
    padding-left: 2em;
    margin: 0.5em 0;
  }
  
  .editor a {
    color: #3b82f6;
    text-decoration: underline;
  }
  </style>