<template>
  <div>
    <div :style="spacer_style"></div>
    <div
      class="flex flex-column"
      :class="showWrapper ? 'br2 ba b--black-10 pa4' : ''"
    >
      <div
        class="flex flex-column justify-center form-logo"
        v-if="showIcon"
      >
        <ServiceIcon
          class="form-logo-icon"
          v-bind="$attrs"
        />
      </div>
      <slot></slot>
    </div>
  </div>
</template>

<script>
  import ServiceIcon from '@/components/ServiceIcon'

  export default {
    inheritAttrs: false,
    props: {
      topSpacer: {
        type: Number,
        default: 16
      },
      showWrapper: {
        type: Boolean,
        default: true
      },
      showIcon: {
        type: Boolean,
        default: true
      }
    },
    components: {
      ServiceIcon
    },
    computed: {
      spacer_style() {
        return `height: ${this.topSpacer}px`
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .form-logo
    background: #fff
    height: 48px
    margin: -56px auto 24px
    padding: 0 8px

  .form-logo-icon
    border-radius: 4px
    max-height: 48px
</style>
