<template>
  <el-select
    ref="tag-select"
    multiple
    filterable
    allow-create
    default-first-option
    popper-class="dn"
    @visible-change="onVisibleChange"
    @keydown.native.tab="addTag"
    @keydown.native.prevent.space="addTag"
    @keydown.native.prevent.188="addTag"
    v-bind:value="value"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <el-option
      :label="option.label"
      :value="option.val"
      :key="option.val"
      v-for="option in options"
    />
  </el-select>
</template>

<script>
  export default {
    inheritAttrs: false,
    props: {
      value: {
        type: Array,
        default: () => []
      }
    },
    data() {
      return {
        options: []
      }
    },
    methods: {
      onVisibleChange(visible) {
        // this is somewhat of a hack, but it allows the final text
        // that was in the input to be added to the value array
        if (!visible) {
          this.addTag()
        }
      },
      addTag(evt) {
        var val = _.get(this.$refs['tag-select'], '$refs.input.value', '').trim()
        if (val.length > 0) {
          this.$emit('input', [].concat(this.value).concat([val]))
          evt && evt.preventDefault()
        }
      },
      focus() {
        this.$refs['tag-select'].focus()
      }
    }
  }
</script>
