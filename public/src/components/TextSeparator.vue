<template>
  <div class="flex flex-row items-center">
    <div class="separator-line flex-fill" :style="line_style"></div>
    <div class="separator-text" :style="text_style"><slot>or</slot></div>
    <div class="separator-line flex-fill" :style="line_style"></div>
  </div>
</template>

<script>
  export default {
    props: {
      lineColor: {
        type: String,
        default: 'rgba(0,0,0,0.1)'
      },
      textColor: {
        type: String,
        default: '#ccc'
      }
    },
    computed: {
      line_style() {
        return 'border-color: ' + this.lineColor
      },
      text_style() {
        return 'color: ' + this.textColor
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .separator-line
    border-bottom: 1px solid rgba(0,0,0,0.1)
  .separator-text
    color: #ccc
    padding: 0 8px
</style>
