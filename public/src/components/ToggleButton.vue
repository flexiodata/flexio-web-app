<template>
  <div class="css-ios-toggle" @click.stop @dblclick.stop>
    <input
      type="checkbox"
      :id="uid"
      :checked="checked"
      @click.stop="onClick"
      @dblclick.stop="onDblClick"
    >
    <label :for="uid"></label>
  </div>
</template>

<script>
  export default {
    props: {
      'checked': {
        type: Boolean,
        required: true
      },
      'prevent-default': {
        type: Boolean,
        default: false
      }
    },
    computed: {
      uid() {
        return _.uniqueId('toggle-')
      }
    },
    methods: {
      onClick(evt) {
        if (this.preventDefault)
          evt.preventDefault()

        this.$emit('click', evt)
      },
      onDblClick(evt) {
        this.$emit('dblclick', evt)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  // iOS Checkbox Toggle Switch
  // ref. https://proto.io/freebies/onoff/

  $size-md = 1.5rem
  $size-sm = 1.25rem
  $size-lg = 2rem

  $width-md = 2.75em
  $width-sm = 2.25rem
  $width-lg = 3.75rem

  $border-width = 2px

  $bg-color = #fff
  $off-color = #ddd
  $on-color = #009900

  .css-ios-toggle

    box-sizing: border-box
    position: relative
    width: $width-md
    user-select: none

    input[type="checkbox"]
      display: none

    label
      display: block
      overflow: hidden
      cursor: pointer
      padding: 0
      height: $size-md
      line-height: $size-md
      border-radius: $size-md
      border: $border-width solid $off-color
      background-color: $bg-color
      transition: background-color 0.15s linear

    label:before
      content: ""
      display: block
      margin: 0px
      background: $bg-color
      position: absolute
      top: 0
      bottom: 0
      width: $size-md
      right: $width-md - $size-md
      border-radius: $size-md
      border: $border-width solid $off-color
      transition: all 0.15s linear

    input[type="checkbox"]:checked + label
      background-color: $on-color

    input[type="checkbox"]:checked + label,
    input[type="checkbox"]:checked + label:before
      border-color: $on-color

    input[type="checkbox"]:checked + label:before
      right: 0
      margin: 0

  .css-ios-toggle.css-ios-toggle-sm
    width: $width-sm

    label
      height: $size-sm
      line-height: $size-sm
      border-radius: $size-sm

    label:before
      width: $size-sm
      right: $width-sm - $size-sm
      border-radius: $size-sm

  .css-ios-toggle.css-ios-toggle-lg
    width: $width-lg

    label
      height: $size-lg
      line-height: $size-lg
      border-radius: $size-lg

    label:before
      width: $size-lg
      right: $width-lg - $size-lg
      border-radius: $size-lg
</style>
