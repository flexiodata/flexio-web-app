<template>
  <div class="vue-popper">
    <slot></slot>
    <div class="vue-popper__arrow"></div>
  </div>
</template>

<script>
  import Popper from 'popper.js'

  const isNil = (v) => { return v === undefined || v === null }
  const getType = (v) => { return v === undefined ? 'undefined' : v === null ? 'null' : v.constructor.name.toLowerCase() }

  export default {
    name: 'vue-popper',
    props: {
      target: {
        default: null
      },
      options: {
        type: Object,
        default: () => { return {} }
      }
    },
    data() {
      return {
        popper: null
      }
    },
    computed: {
      popper_options() {
        var default_opts = {
          placement: 'top',
          modifiers: {
            arrow: {
              element: '.vue-popper__arrow'
            }
          }
        }

        return Object.assign({}, default_opts, this.options)
      }
    },
    mounted() {
      this.initSelf()
    },
    beforeDestroy() {
      this.uninitSelf()
    },
    methods: {
      initSelf() {
        var target_el
        var type = getType(this.target)

        if (isNil(this.target)) {
          return
        } else if (type == 'string') {
          target_el = document.querySelector(this.target)
        } else if (type == 'htmldivelement') {
          target_el = this.target
        }

        if (target_el) {
          this.popper = new Popper(
            target_el,
            this.$el,
            this.popper_options
          )
        } else {
          // TODO: error handling
        }
      },
      uninitSelf() {
        if (this.popper) {
          this.popper.destroy()
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  $body_bg_color = #50596c
  $body_fg_color = #fff
  $body_border_color = #50596c
  $body_border_radius = 0.25rem
  $body_padding = 1rem
  $body_min_width = 10rem
  $body_max_width = 22rem
  $body_font_size = 0.875rem

  $triangle_bg_color = #50596c
  $triangle_border_color = #50596c
  $triangle_size = .625rem
  $triangle_offset = .75rem

  $distance = .875rem

  .vue-popper
    background: $body_bg_color
    min-width: $body_min_width
    max-width: $body_max_width
    border-radius: $body_border_radius
    border: 1px solid $body_border_color
    padding: $body_padding
    color: $body_fg_color
    line-height: 1.4
    font-size: $body_font_size
    box-shadow: 0 8px 16px -6px rgba(0,0,0,0.6)

  .vue-popper .vue-popper__arrow,
  .vue-popper .vue-popper__arrow::after
    position: absolute
    display: block
    width: 0
    height: 0
    border-color: transparent
    border-style: solid

  .vue-popper .vue-popper__arrow
    border-width: $triangle_size
    -webkit-filter: drop-shadow(0 2px 12px rgba(0, 0, 0, .03))
    filter: drop-shadow(0 2px 12px rgba(0,0,0,.03))

  .vue-popper .vue-popper__arrow::after
    content:" "
    border-width: $triangle_size

  .vue-popper[x-placement^="top"]
    margin-bottom: $distance
    .vue-popper__arrow
      bottom: $triangle_size * -1
      left: 50%
      margin: 0 $triangle_offset
      border-top-color: $triangle_border_color
      border-bottom-width: 0
      &::after
        bottom: 1px
        margin-left: $triangle_size * -1
        border-top-color: $triangle_bg_color
        border-bottom-width: 0

  .vue-popper[x-placement^="bottom"]
    margin-top: $distance
    .vue-popper__arrow
      top: $triangle_size * -1
      left: 50%
      margin: 0 $triangle_offset
      border-bottom-color: $triangle_border_color
      border-top-width: 0
      &::after
        top: 1px
        margin-left: $triangle_size * -1
        border-bottom-color: $triangle_bg_color
        border-top-width: 0

  .vue-popper[x-placement^="right"]
    margin-left: $distance
    .vue-popper__arrow
      left: $triangle_size * -1
      top: 50%
      margin: $triangle_offset 0
      border-right-color: $triangle_border_color
      border-left-width: 0
      &::after
        bottom: $triangle_size * -1
        left: 1px
        border-right-color: $triangle_bg_color
        border-left-width: 0

  .vue-popper[x-placement^="left"]
    margin-right: $distance
    .vue-popper__arrow
      right: $triangle_size * -1
      top: 50%
      margin: $triangle_offset 0
      border-left-color: $triangle_border_color
      border-right-width: 0
      &::after
        bottom: $triangle_size * -1
        right: 1px
        border-left-color: $triangle_bg_color
        border-right-width: 0

  /*
  .vue-popper
    background: #fff
    min-width: 150px
    border-radius: 4px
    border: 1px solid #ebeef5
    padding: 12px
    color: #606266
    line-height: 1.4
    font-size: 14px
    box-shadow: 0 2px 12px 0 rgba(0,0,0,.1)

  .vue-popper .vue-popper__arrow,
  .vue-popper .vue-popper__arrow::after
    position: absolute
    display: block
    width: 0
    height: 0
    border-color: transparent
    border-style: solid

  .vue-popper .vue-popper__arrow
    border-width: 6px
    -webkit-filter: drop-shadow(0 2px 12px rgba(0, 0, 0, .03))
    filter: drop-shadow(0 2px 12px rgba(0,0,0,.03))

  .vue-popper .vue-popper__arrow::after
    content:" "
    border-width: 6px

  .vue-popper[x-placement^="top"]
    margin-bottom: 12px
    .vue-popper__arrow
      bottom: -6px
      left: 50%
      margin-right: 3px
      border-top-color: #ebeef5
      border-bottom-width: 0
      &::after
        bottom: 1px
        margin-left: -6px
        border-top-color: #fff
        border-bottom-width: 0

  .vue-popper[x-placement^="bottom"]
    margin-top: 12px
    .vue-popper__arrow
      top: -6px
      left: 50%
      margin-right: 3px
      border-bottom-color: #ebeef5
      border-top-width: 0
      &::after
        top: 1px
        margin-left: -6px
        border-bottom-color: #fff
        border-top-width: 0

  .vue-popper[x-placement^="right"]
    margin-left: 12px
    .vue-popper__arrow
      left: -6px
      top: 50%
      margin-bottom: 3px
      border-right-color: #ebeef5
      border-left-width: 0
      &::after
        bottom: -6px
        left: 1px
        border-right-color: #fff
        border-left-width: 0

  .vue-popper[x-placement^="left"]
    margin-right: 12px
    .vue-popper__arrow
      right: -6px
      top: 50%
      margin-bottom: 3px
      border-left-color: #ebeef5
      border-right-width: 0
      &::after
        bottom: -6px
        right: 1px
        border-left-color: #fff
        border-right-width: 0
  */
</style>
