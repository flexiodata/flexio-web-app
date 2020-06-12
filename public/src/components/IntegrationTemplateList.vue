<template>
  <div>
    <div class="template-list">
      <div
        class="flex flex-row template-item"
        :class="integrationIcon.length > 0 ? 'items-center' : ''"
        :key="template.title"
        @click="$emit('template-click', template, integrationName)"
        v-for="template in templates"
      >
        <img
          :src="integrationIcon"
          class="item-icon"
          v-if="integrationIcon.length > 0"
        >
        <div class="item-body">
          <h4 class="mv0 item-title">{{template.title}}</h4>
          <div
            class="f6 lh-copy item-description"
            v-if="hasDescription(template)"
          >
            {{template.description}}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      integrationName: {
        type: String,
        default: ''
      },
      integrationIcon: {
        type: String,
        default: ''
      },
      templates: {
        type: Array,
        required: true
      }
    },
    methods: {
      hasDescription(template) {
        return _.get(template, 'description', '').length > 0
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .item-icon
    max-width: 48px

  .item-body
    color: #222
    font-weight: normal
    margin-left: 24px

  .item-title
    margin: 0

  .item-description
    margin-top: 8px

  .template-item
    padding: 28px 24px
    margin-top: -1px
    border-top: 1px solid #dcdfe6
    border-bottom: 1px solid #dcdfe6
    &:hover
    &:active
      position: relative
      cursor: pointer
      border-color: $blue
      .item-title
      .item-description
        color: $blue
</style>
