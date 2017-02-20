<template>
  <div v-if="inline" class="ui active inline loader" :class="[inlineCentered?'centered':'', size]" ></div>
  <div v-else class="ui active inverted dimmer">
    <div class="ui loader" :class="[showText?'text':'', size]"><span v-if="showText">{{loadingText}}</span></div>
  </div>
</template>

<script>
  export default {
    props: {
      'inline': {
        type: Boolean,
        default: false
      },
      'inline-centered': {
        type: Boolean,
        default: false
      },
      'show-text': {
        type: Boolean,
        default: false
      },
      'loading-text': {
        type: String,
        default: 'Loading'
      },
      'size': {
        type: String,
        default: '' // mini | tiny | small | medium | large | big | huge | massive
      }
    }
  }
</script>

<style>
  @import "../../node_modules/semantic-ui-css/components/loader.min.css";
</style>
