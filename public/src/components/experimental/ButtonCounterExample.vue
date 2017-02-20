<template>
  <div>
    <p class="mt0">{{total}}</p>
    <button-counter v-on:increment="incrementTotal"></button-counter>
    <button-counter v-on:increment="incrementTotal"></button-counter>
  </div>
</template>

<script>
  import ButtonCounter from './ButtonCounter.vue'

  export default {
    components: {
      'button-counter': ButtonCounter
    },
    data() {
      return {
        total: 0
      }
    },
    methods: {
      incrementTotal() {
        this.total += 1
      }
    }
  }
</script>
