<template>
  <el-select
    placeholder="Choose a state"
    filterable
    :value="value"
    @input="handleInput"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <el-option
      :label="val"
      :value="key"
      :key="key"
      v-for="(val, key) in states"
    />
  </el-select>
</template>

<script>
  import states from '@/data/us-states.yml'

  export default {
    props: ['value'],
    data() {
      return {
        states,
      }
    },
    methods: {
      handleInput(val) {
        this.$emit('input', val)
      }
    }
  }

</script>
