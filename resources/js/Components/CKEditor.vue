<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

const props = defineProps({
  modelValue: String,
  id: String,
  disabled: Boolean,
  config: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update:modelValue']);
const editorRef = ref(null);
const editor = ref(null);

onMounted(() => {
  ClassicEditor
    .create(editorRef.value, props.config)
    .then(instance => {
      editor.value = instance;
      editor.value.setData(props.modelValue || '');

      editor.value.model.document.on('change:data', () => {
        emit('update:modelValue', editor.value.getData());
      });
    })
    .catch(error => {
      console.error('CKEditor initialization error', error);
    });
});

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy();
  }
});

watch(() => props.modelValue, (newValue) => {
  if (editor.value && newValue !== editor.value.getData()) {
    editor.value.setData(newValue || '');
  }
});
</script>

<template>
  <div class="ckeditor-container">
    <div :id="id" ref="editorRef"></div>
  </div>
</template>

<style>
.ckeditor-container .ck-editor__editable {
  min-height: 200px;
}
</style>