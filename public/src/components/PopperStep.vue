<template>
  <Popper
    class="vue-popper-step"
    :options="popperOptions"
    v-bind="$attrs"
  >
    <slot name="header">
      <div v-if="title.length > 0" class="vue-popper__header">
        <div v-html="title"></div>
      </div>
    </slot>

    <slot name="body">
      <div class="vue-popper__body">
        <slot>
          <div v-if="content.length > 0" v-html="content"></div>
          <div v-else>No content has been specified.</div>
        </slot>
        <div
          class="vue-popper-step__dots"
          v-if="showDots"
        >
          <div
            class="vue-popper-step__dot"
            :class="{
              'vue-popper-step__dot--active': step_idx == index
            }"
            @click="jumpClick(step_idx)"
            v-for="(item, step_idx) in steps"
          >
          </div>
        </div>
      </div>
    </slot>

    <slot name="footer">
      <div class="vue-popper__footer">
        <button
          class="vue-popper__button vue-popper__button-skip"
          @click.prevent="skipClick"
          v-if="!isLastStep"
        >
          {{skipButtonLabel}}
        </button>
        <div class="flex-fill"></div>
        <button
          class="vue-popper__button vue-popper__button-prev"
          @click.prevent="prevClick"
          v-if="!isFirstStep"
        >
          {{prevButtonLabel}}
        </button>
        <button
          class="vue-popper__button vue-popper__button-next"
          @click.prevent="nextClick"
          v-if="!isLastStep"
        >
          {{nextButtonLabel}}
        </button>
        <button
          class="vue-popper__button vue-popper__button-done"
          @click.prevent="doneClick"
          v-if="isLastStep"
        >
          {{doneButtonLabel}}
        </button>
      </div>
    </slot>
  </Popper>
</template>

<script>
  import Popper from './Popper.vue'

  export default {
    inheritAttrs: false,
    name: 'vue-popper-step',
    props: {
      title: {
        type: String,
        default: ''
      },
      content: {
        type: String,
        default: ''
      },
      index: {
        type: Number
      },
      steps: {
        type: Array,
        default: () => []
      },
      showDots: {
        type: Boolean,
        default: false
      },
      popperOptions: {
        type: Object,
        default: () => { return {} }
      },
      skipButtonLabel: {
        type: String,
        default: 'Skip tour'
      },
      prevButtonLabel: {
        type: String,
        default: 'Previous'
      },
      nextButtonLabel: {
        type: String,
        default: 'Next'
      },
      doneButtonLabel: {
        type: String,
        default: 'Finish'
      },
      prevClick: {
        type: Function
      },
      nextClick: {
        type: Function
      },
      skipClick: {
        type: Function
      },
      doneClick: {
        type: Function
      },
      jumpClick: {
        type: Function
      },
      isFirstStep: {
        type: Boolean
      },
      isLastStep: {
        type: Boolean
      }
    },
    components: {
      Popper
    }
  }
</script>

<style lang="stylus" scoped>
  .vue-popper__header
    margin-bottom: 1rem

  .vue-popper__body
    margin: 0

  .vue-popper__footer
    margin-top: 1.5rem

    // .flex
    display: flex

    // .flex-row
    -webkit-box-orient: horizontal
    -webkit-box-direction: normal
    -ms-flex-direction: row
    flex-direction: row

    // .justify-end
    -webkit-box-pack: end
    -ms-flex-pack: end
    justify-content: flex-end

  .vue-popper__button
    background: transparent
    border: .0625rem solid #fff
    border-radius: .25rem
    color: #fff
    cursor: pointer
    display: inline-block
    line-height: 1rem
    outline: none
    padding: .5rem .75rem
    text-align: center
    text-decoration: none
    transition: all .2s ease
    vertical-align: middle
    white-space: nowrap
    &:hover
      background-color: #fff
      color: #50596c

  .vue-popper__button + .vue-popper__button
    margin-left: 0.375rem

  .vue-popper__button-skip
    margin-right: 2rem

  .vue-popper-step__dots
    text-align: center
    margin: 0.5rem 0

  .vue-popper-step__dot
    display: inline-block
    cursor: pointer
    background-color: #fff
    opacity: .25
    border-radius 100%
    width: 6px
    height: 6px
    margin: 0 3px
    transition: opacity .2s ease
    &:hover
      opacity: .5

  .vue-popper-step__dot--active
    opacity: 1
</style>
