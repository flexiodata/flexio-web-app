<template>
  <div class="dn db-ns" v-if="isStatic">
    <div class="flex flex-column flex-row-ns">
      <div class="mb2 mr1-ns ph1 fw6 w-third-ns">{{key}}</div>
      <div class="mb2 mr1-ns ph1 fw6 w-two-thirds-ns">{{val}}</div>
    <div class="o-0">
      <span class="pointer f3">
        &times;
      </span>
    </div>
    </div>
  </div>
  <div class="flex flex-column flex-row-ns items-center hide-child" v-else>
    <input
      type="text"
      class="mb1 mb0-ns mr1-ns ph2 pv1 w-third-ns f6 lh-copy ba b--light-gray focus-b--transparent focus-outline focus-ow1 focus-o--blue"
      :placeholder="keyPlaceholder"
      @input="onInputChange"
      v-model="key"
    >
    <input
      type="text"
      class="mb1 mb0-ns mr1-ns ph2 pv1 w-two-thirds-ns f6 lh-copy ba b--light-gray focus-b--transparent focus-outline focus-ow1 focus-o--blue"
      :placeholder="valPlaceholder"
      @input="onInputChange"
      v-model="val"
    >
    <div class="mb1 mb0-ns">
      <span
        class="pointer f3 lh-copy b child"
        @click="onDeleteClick"
      >
        &times;
      </span>
    </div>
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
      getItem() {
        return { key: this.key, val: this.val }
      },
      onInputChange() {
        this.$emit('change', this.getItem(), this.index)
      },
      onDeleteClick() {
        this.$emit('delete', this.getItem(), this.index)
      }
    }
  }
</script>
