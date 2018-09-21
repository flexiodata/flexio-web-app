<template>
  <div v-if="!force_render">
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
    data() {
      return {
        force_render: false
      }
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
    },
    methods: {
      revert() {
        this.force_render = true
        this.$nextTick(() => { this.force_render = false })
      }
    }
  }
</script>
