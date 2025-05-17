<template>
  <div class="option" @click="select">
    <slot>{{ label }}</slot>
  </div>
</template>

<script>
export default {
  props: ['label', 'value'],
  emits: ['select'],
  methods: {
    select() {
      this.$emit('select', this.value)
    }
  }
}
</script>

<style scoped>
.option {
  padding: 1rem;
  cursor: pointer;
}
</style>
