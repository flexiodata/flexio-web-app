<template>
  <div class="bg-white pa4 overflow-y-scroll">
    <BuilderItemForm
      class="mb5"
      :key="prompt.name"
      :item="prompt"
      v-for="prompt in def.prompts"
    />
  </div>
</template>

<script>
  import test_def from '../data/builder/test2-def.yml'
  import BuilderItemForm from '@/components/BuilderItemForm'

  export default {
    metaInfo: {
      title: '[Admin] Prototype'
    },
    components: {
      BuilderItemForm
    },
    data() {
      return {
        def: test_def
      }
    },
  }
</script>
