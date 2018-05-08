<template>
  <div class="dn db-ns" v-if="isStatic">
    <div class="flex flex-column flex-row-ns">
      <div class="mr3-ns fw6 w-30-ns">{{key}}</div>
      <div class="mr2-ns fw6 w-70-ns">{{val}}</div>
    <div class="o-0">
      <span class="pointer f3">
        &times;
      </span>
    </div>
    </div>
  </div>
  <div class="flex flex-column flex-row-ns hide-child mb2" v-else>
    <div class="mr2-ns w-30-ns">
      <el-input
        :placeholder="keyPlaceholder"
        @input="onInputChange"
        v-model="key"
      />
    </div>
    <div class="mr2-ns w-70-ns">
      <el-input
        :placeholder="valPlaceholder"
        @input="onInputChange"
        v-model="val"
      />
    </div>

    <div
      class="dn db-ns"
      :class="index >= count-1 ? 'o-0 no-pointer-events' : ''"
    >
      <span
        class="pointer f3 lh-copy b child o-0"
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
      'count': {
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
