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
        default: '#ddd'
      },
      textColor: {
        type: String,
        default: '#bbb'
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
    border-bottom: 1px solid #ddd
  .separator-text
    color: #bbb
    padding: 0 8px
</style>
