<template>
  <ui-modal
    ref="dialog"
    remove-close-button
  >
    <div slot="header" class="f4" v-if="has_title">{{title}}</div>

    <slot></slot>

    <div slot="footer" class="flex-fill flex flex-row">
      <div class="flex-fill">&nbsp;</div>
      <btn btn-md class="flex-none" :class="okCls" @click="close">{{okLabel}}</btn>
    </div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'ok-cls': {
        type: String,
        default: 'ttu b blue'
      },
      'ok-label': {
        type: String,
        default: 'Ok'
      }
    },
    components: {
      Btn
    },
    computed: {
      has_title() {
        return _.get(this, 'title', '').length > 0
      }
    },
    methods: {
      open() {
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      }
    }
  }
</script>
