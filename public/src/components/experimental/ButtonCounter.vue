<template>
  <a btn href="#" class="link darken-10 dib f5 br1 white bg-blue ph3 pv2" v-on:click="increment">{{counter}}</a>
</template>

<script>
  export default {
    data() {
      return {
        counter: 0
      }
    },
    methods: {
      increment() {
        this.counter += 1
        this.$emit('increment')
      }
    }
  }
</script>
