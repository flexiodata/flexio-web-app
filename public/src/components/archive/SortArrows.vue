<template>
  <span class="sort-caret-container" :class="isActive ? direction : ''">
    <i
      class="sort-caret asc"
      @click="$emit('sort', sortKey, 'asc')"
    ></i>
    <i
      class="sort-caret desc"
      @click="$emit('sort', sortKey, 'desc')"
    ></i>
  </span>
</template>

<script>
  export default {
    props: {
      isActive: {
        type: Boolean,
        default: false
      },
      sortKey: {
        type: String,
        required: true
      },
      direction: {
        type: String,
        default: 'none' // 'asc', 'desc', 'none'
      }
    }
  }
</script>
<style lang="stylus" scoped>
  .sort-caret-container
    position: relative
    display: inline-flex
    flex-direction: column
    align-items: center
    height: 34px
    width: 17px
    vertical-align: middle
    cursor: pointer

  .sort-caret
    width: 0
    height: 0
    border: 5px solid transparent
    position: absolute
    left: 7px

  .sort-caret.asc
    border-bottom-color: #c0c4cc
    top: 5px

  .sort-caret.desc
    border-top-color: #c0c4cc
    bottom: 7px

  .sort-caret-container.asc .sort-caret.asc
    border-bottom-color: #409EFF

  .sort-caret-container.desc .sort-caret.desc
    border-top-color: #409EFF
</style>
