<template>
  <div>
    <KeypairList
      ref="input-list"
      :header="{ key: 'Key', val: 'Value' }"
      v-model="edit_value"
    />
  </div>
</template>

<script>
  import KeypairList from './KeypairList.vue'

  export default {
    props: {
      value: {
        type: Object,
        required: true
      }
    },
    components: {
      KeypairList
    },
    computed: {
      edit_value: {
        get() {
          return this.value
        },
        set(value) {
          this.$emit('input', value)
        }
      }
    }
  }
</script>
