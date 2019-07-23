<template>
  <Popper
    class="vue-popper-step"
    :class="is_scrolling ? 'o-0' : ''"
    :target="target"
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
              'vue-popper-step__dot--active': step_idx == index,
              'vue-popper-step__dot--clickable': allowJump
            }"
            @click="allowJump ? jumpClick(step_idx) : () => {}"
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
  import zenscroll from 'zenscroll'
  import Popper from '@comp/Popper'

  const isNil = (v) => { return v === undefined || v === null }
  const getType = (v) => { return v === undefined ? 'undefined' : v === null ? 'null' : v.constructor.name.toLowerCase() }

  /**
  * Find the nearest scrollable parent
  * copied from https://stackoverflow.com/questions/35939886/find-first-scrollable-parent
  *
  * @param Element element
  * @return Element
  */
  function _getScrollParent(element) {
    var style = window.getComputedStyle(element)
    var excludeStaticParent = (style.position === 'absolute')
    var overflowRegex = /(auto|scroll)/

    if (style.position === 'fixed') {
      return document.body
    }

    for (var parent = element; (parent = parent.parentElement);) {
      style = window.getComputedStyle(parent)
      if (excludeStaticParent && style.position === 'static') {
        continue
      }
      if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent
    }

    return document.body
  }

  export default {
    inheritAttrs: false,
    name: 'vue-popper-step',
    props: {
      target: {
        default: null
      },
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
        default: true
      },
      allowJump: {
        type: Boolean,
        default: false
      },
      popperOptions: {
        type: Object,
        default: () => {}
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
      },
    },
    components: {
      Popper
    },
    data() {
      return {
        is_scrolling: false
      }
    },
    mounted() {
      this.scrollToTarget()
    },
    methods: {
      scrollToTarget() {
        this.is_scrolling = true

        var target_el
        var type = getType(this.target)

        if (isNil(this.target)) {
          return
        } else if (type == 'string') {
          target_el = document.querySelector(this.target)
        } else if (type == 'htmldivelement') {
          target_el = this.target
        }

        var scroll_parent = _getScrollParent(target_el)

        if (scroll_parent === document.body) {
          zenscroll.center(target_el, 400, 0, () => { this.is_scrolling = false })
        } else {
          var scroller = zenscroll.createScroller(scroll_parent)
          scroller.center(target_el, 400, 0, () => { this.is_scrolling = false })
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .o-0
    opacity: 0

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
    font-size: 0.875rem
    color: #fff
    cursor: pointer
    display: inline-block
    line-height: 1rem
    outline: none
    padding: .375rem .625rem
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
    margin: 0.5rem 0 -0.5rem

  .vue-popper-step__dot
    display: inline-block
    background-color: #fff
    opacity: .25
    border-radius 100%
    width: 6px
    height: 6px
    margin: 0 3px
    transition: opacity .2s ease

  .vue-popper-step__dot--clickable
    cursor: pointer
    &:hover
      opacity: .5

  .vue-popper-step__dot--active
    opacity: 1
</style>
