<template>
  <div>
    <div :style="top_spacer_style"></div>
    <div
      class="flex flex-column"
      :class="showWrapper ? 'br2 ba b--black-10 pa4' : ''"
    >
      <div
        class="flex flex-column justify-center form-logo"
        :style="form_logo_style"
        v-if="showIcon"
      >
        <slot name="icon">
          <ServiceIcon
            class="form-logo-icon"
            v-bind="$attrs"
          />
        </slot>
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
      topSpacing: {
        type: Number,
        default: 16
      },
      innerSpacing: {
        type: Number,
        default: 24
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
      top_spacer_style() {
        return { height: this.topSpacing + 'px' }
      },
      form_logo_style() {
        return { marginBottom: this.innerSpacing + 'px' }
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
