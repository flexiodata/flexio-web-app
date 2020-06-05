<template>
  <div>
    <div class="flex flex-row flex-wrap template-list">
      <div
        class="template-item"
        :key="template.title"
        @click="$emit('template-click', template)"
        v-for="template in templates"
      >
        <div class="item-label">{{template.title}}</div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      templates: {
        type: Array,
        required: true
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .template-container
    overflow: hidden

  .template-list
    margin-left: -12px
    margin-right: -12px

  .template-item
    border: 2px solid transparent
    border-radius: 4px
    cursor: pointer
    margin: 12px
    padding: 24px 16px
    position: relative
    width: calc(50% - 24px)
    box-shadow: 0 4px 16px -4px rgba(0,0,0,0.2)
    &.selected
    &:hover
      border-color: #357edd

  .item-label
    font-size: 16px
    font-weight: 600
    color: #333
</style>
