<template>
  <a href="#" @click.prevent class="link cursor-default dib black-60 bg-black-10" :class="cls">{{item.name}}</a>
</template>

<script>
  export default {
    props: ['item'],
    data() {
      return {
        cls: 'b--black-10'
      }
    }
  }
</script>
