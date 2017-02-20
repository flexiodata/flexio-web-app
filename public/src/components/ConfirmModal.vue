<template>
  <ui-modal
    ref="dialog"
    remove-close-button
  >
    <div slot="header" class="f4" v-if="has_title">{{title}}</div>

    <slot></slot>

    <div slot="footer" class="flex-fill flex flex-row">
      <div class="flex-fill">&nbsp;</div>
      <btn btn-md class="flex-none" :class="cancelCls" @click="close()">{{cancelLabel}}</btn>
      <btn btn-md class="flex-none" :class="submitCls" @click="submit()">{{submitLabel}}</btn>
    </div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'

  export default {
    props: {
      'title': {
        type: String
      },
      'cancel-cls': {
        type: String,
        default: 'b ttu blue mr2'
      },
      'submit-cls': {
        type: String,
        default: 'b ttu blue'
      },
      'cancel-label': {
        type: String,
        default: 'Cancel'
      },
      'submit-label': {
        type: String,
        default: 'Ok'
      }
    },
    components: {
      Btn
    },
    computed: {
      has_title() {
        return _.has(this, 'title')
      }
    },
    methods: {
      open() {
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
        this.$emit('confirm', this)
      }
    }
  }
</script>
