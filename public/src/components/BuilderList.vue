<template>
  <div>
    Builder List
    <hr>
    <builder-item
      v-for="(item, index) in prompts"
      :item="item"
      :index="index"
      :key="index"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import BuilderItem from './BuilderItem.vue'

  export default {
    components: {
      BuilderItem
    },
    computed: {
      ...mapState({
        prompts: state => state.builder.prompts
      })
    }
  }
</script>
