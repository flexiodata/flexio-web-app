<template>
  <transition name="flexio-modal">
    <div class="flexio-modal-mask" @click="maskClick">
      <div class="flexio-modal-wrapper">
        <div class="flex flex-column flexio-modal-container" :class="containerCls" :style="containerStyle"  @click.stop>
          <div class="flex-none cf flexio-modal-header" v-if="showHeader">
            <div class="pointer f3 lh-solid b child black-30 hover-black-60 fr" @click="cancelClick" v-if="showCloseButton">&times;</div>
            <slot name="header"><div class="f4" v-if="title.length > 0">{{title}}</div></slot>
          </div>

          <div class="flex-fill flexio-modal-body">
            <slot></slot>
          </div>

          <div class="flex-none flexio-modal-footer" v-if="showFooter">
            <slot name="footer">
              <div class="flex flex-row">
                <div class="flex-fill">&nbsp;</div>
                <button
                  type="button"
                  class="border-box no-select ph4 pv2a lh-title br2 darken-10"
                  @click="cancelClick"
                >
                  {{cancelLabel}}
                </button>
                <button
                  type="button"
                  class="border-box no-select ph4 pv2a lh-title br2 darken-10"
                  @click="submitCls"
                >
                  {{submitLabel}}
                </button>
              </div>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
  export default {
    props: {
      'title': {
        type: String,
        default: 'Modal Title'
      },
      'container-cls': {
        type: String,
        default: ''
      },
      'container-style': {
        type: String,
        default: ''
      },
      'cancel-cls': {
        type: String,
        default: 'ttu fw6 blue mr2'
      },
      'submit-cls': {
        type: String,
        default: 'ttu fw6 white bg-blue'
      },
      'cancel-label': {
        type: String,
        default: 'Cancel'
      },
      'submit-label': {
        type: String,
        default: 'Ok'
      },
      'click-outside-to-close': {
        type: Boolean,
        default: true
      },
      'show-header': {
        type: Boolean,
        default: true
      },
      'show-footer': {
        type: Boolean,
        default: true
      },
      'show-close-button': {
        type: Boolean,
        default: true
      }
    },
    methods: {
      maskClick() {
        if (this.clickOutsideToClose === true)
          this.$emit('cancel', this)
      },
      cancelClick() {
        this.$emit('cancel', this)
      },
      submitClick() {
        this.$emit('submit', this)
      }
    }
  }
</script>

<style>
  .flexio-modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    display: table;
    transition: opacity .3s ease;
  }

  .flexio-modal-wrapper {
    display: table-cell;
    vertical-align: middle;
  }

  .flexio-modal-container {
    width: 24rem;
    margin: 0 auto;
    max-height: 100vh;
    max-width: 100vw;
    background-color: #fff;
    border-radius: 0.125rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
  }

  .flexio-modal-body {
    padding: 1rem 1.5rem;
    overflow-y: auto;
  }

  .flexio-modal-header {
    padding: 1.25rem 1.5rem 0;
    margin-bottom: 1rem;
  }

  .flexio-modal-footer {
    padding: 0 1rem 1rem;
    margin-top: 1rem;
  }

  /*
   * The following styles are auto-applied to elements with
   * transition="flexio-modal" when their visibility is toggled
   * by Vue.js.
   *
   * You can easily play with the modal transition by editing
   * these styles.
   */

  .flexio-modal-enter {
    opacity: 0;
  }

  .flexio-modal-leave-active {
    opacity: 0;
  }

  .flexio-modal-enter .flexio-modal-container {
    transform: scale(0.1);
  }

  .flexio-modal-leave-active .flexio-modal-container {
    transform: scale(0.95);
  }
</style>
