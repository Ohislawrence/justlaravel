<template>
    <div>
      <div class="grid grid-cols-1 gap-4">
        <div>
          <InputLabel value="Grading System Name" />
          <TextInput v-model="form.name" />
        </div>
        
        <div>
          <InputLabel value="Grading Type" />
          <select v-model="form.type" class="w-full border-gray-300 rounded">
            <option value="percentage">Percentage</option>
            <option value="letter_grade">Letter Grades</option>
            <option value="points">Points</option>
            <option value="custom">Custom Scale</option>
          </select>
        </div>
  
        <div v-if="form.type === 'custom'" class="bg-gray-50 p-4 rounded">
          <h4 class="font-semibold mb-3">Custom Grade Scale</h4>
          <div v-for="(grades, index) in form.grade_scale" :key="index" class="flex gap-2 mb-2">
            <TextInput v-model="grades.min" placeholder="Min %" type="number" />
            <TextInput v-model="grades.max" placeholder="Max %" type="number" />
            <TextInput v-model="grades.grade" placeholder="Grade" />
            <select v-model="grades.status" class="border-gray-300 rounded">
              <option value="passed">Pass</option>
              <option value="failed">Fail</option>
            </select>
            <button @click="removeGrade(index)" class="text-red-600">Remove</button>
          </div>
          <button @click="addGrade" class="mt-2 text-blue-600">+ Add Grade Level</button>
        </div>
  
        <div>
          <label class="flex items-center">
            <input type="checkbox" v-model="form.is_default" class="mr-2">
            Set as default grading system
          </label>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive } from 'vue';
  
  const form = reactive({
    name: '',
    type: 'percentage',
    grade_scale: [
      { min: 90, max: 100, grade: 'A', status: 'passed' },
      { min: 80, max: 89, grade: 'B', status: 'passed' },
      { min: 70, max: 79, grade: 'C', status: 'passed' },
      { min: 60, max: 69, grade: 'D', status: 'passed' },
      { min: 0, max: 59, grade: 'F', status: 'failed' }
    ],
    is_default: false
  });
  
  const addGrade = () => {
    form.grade_scale.push({ min: 0, max: 0, grade: '', status: 'passed' });
  };
  
  const removeGrade = (index) => {
    form.grade_scale.splice(index, 1);
  };
  </script>