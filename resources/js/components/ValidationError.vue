<script setup>
import { defineProps } from 'vue'
 
const props = defineProps({
  field: '',
  klass: '',
  errors: {
    type: Object,
    default: () => ({})
  }
})
</script>

<template v-if="props?.errors.value?.[props.field]?.length"> 
  <div :class="'invalid-feedback '+klass" v-for="message in props?.errors.value?.[props.field]" :key="message">{{ message }}</div>
</template>