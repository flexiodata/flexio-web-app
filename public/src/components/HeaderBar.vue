<template>
  <div class="w-100 flex flex-row items-center">
    <span class="flex-fill headerbar__title">{{title}}</span>
    <button
      type="button"
      class="headerbar__btn"
      aria-label="Close"
      @click="$emit('close-click')"
    >
      <i class="headerbar__close el-icon-close"></i>
    </button>
  </div>
</template>

<script>
  export default {
    props: {
      title: {
        type: String,
        default: 'Header Title'
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .headerbar__title
    color: $primary-text
    line-height: 24px
    font-size: 18px

  .headerbar__btn
    padding: 0
    font-size: 16px
    .headerbar__close
      color: $secondary-text
    &:hover
      .headerbar__close
        color: $blue
</style>
