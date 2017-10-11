<template>
  <div class="dn db-ns" v-if="isStatic">
    <div class="flex flex-column flex-row-ns">
      <div class="mb2 mr1-ns ph2 fw6 w-third-ns">{{key}}</div>
      <div class="mb2 ph2 fw6 w-two-thirds-ns">{{val}}</div>
    </div>
  </div>
  <div class="flex flex-column flex-row-ns" v-else>
    <input
      type="text"
      class="mb1 mr1-ns ph2 pv1 w-third-ns lh-copy ba b--light-gray"
      :placeholder="keyPlaceholder"
      @input="onInputChange"
      v-model="key"
    >
    <input
      type="text"
      class="mb1 ph2 pv1 w-two-thirds-ns lh-copy ba b--light-gray"
      :placeholder="valPlaceholder"
      @input="onInputChange"
      v-model="val"
    >
    <div class="db dn-ns bb mv3 b--light-gray"></div>
  </div>
</template>

<script>
  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'index': {
        type: Number,
        default: 0
      },
      'key-placeholder': {
        type: String,
        default: 'Key'
      },
      'val-placeholder': {
        type: String,
        default: 'Value'
      },
      'is-static': {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        key: _.get(this.item, 'key', ''),
        val: _.get(this.item, 'val', '')
      }
    },
    methods: {
      onInputChange() {
        this.$emit('change', { key: this.key, val: this.val }, this.index)
      }
    }
  }
</script>
