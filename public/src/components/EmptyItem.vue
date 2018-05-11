<template>
  <div class="flex flex-column justify-center tc">
    <div class="tc mb3" v-if="$slots.icon">
      <div class="dib pa3 pa4-ns bg-black-30 white br-100 overlay-icon cursor-default">
        <slot name="icon"></slot>
      </div>
    </div>
    <div class="f5 f4-ns fw6 black-30 cursor-default"><slot name="text"></slot></div>
  </div>
</template>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .overlay-icon .material-icons
    font-size: 64px

  @media $breakpoint-large
    .overlay-icon .material-icons
      font-size: 96px
</style>
