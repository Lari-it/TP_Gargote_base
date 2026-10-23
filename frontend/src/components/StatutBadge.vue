<script setup>
import { computed } from 'vue'

const props = defineProps({
  statut: { type: String, required: true },
})

const classes = computed(() => {
  return `badge badge-${props.statut}`
})
</script>

<template>
  <span :class="classes">{{ statut }}</span>
</template>
