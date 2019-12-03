<template>
  <el-select
    placeholder="Choose a country"
    filterable
    :value="value"
    @input="handleInput"
  >
    <el-option
      :label="val"
      :value="key"
      :key="key"
      v-for="(val, key) in countries"
    />
  </el-select>
</template>

<script>
  import countries from '@/data/countries.yml'

  export default {
    props: ['value'],
    data() {
      return {
        countries,
      }
    },
    methods: {
      handleInput(val) {
        this.$emit('input', val)
      }
    }
  }

</script>
