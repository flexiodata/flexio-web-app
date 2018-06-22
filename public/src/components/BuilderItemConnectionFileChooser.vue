<template>
  <div>
    <BuilderItemConnectionChooser
      :item="item"
      :index="index"
      :show-title="false"
    />
    <BuilderItemFileChooser
      :item="item"
      :index="index"
      :show-title="false"
    />
  </div>
</template>

<script>
  import BuilderItemConnectionChooser from './BuilderItemConnectionChooser.vue'
  import BuilderItemFileChooser from './BuilderItemFileChooser.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    components: {
      BuilderItemConnectionChooser,
      BuilderItemFileChooser
    }
  }
</script>
