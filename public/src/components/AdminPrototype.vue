<template>
  <div class="pa5 overflow-y-scroll">
    <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box trans-mw" style="max-width: 800px">
      <BuilderItemForm
        :key="prompt.name"
        :item="prompt"
        :visible="index == active_idx"
        :cancel-button-visible="active_idx > 0"
        :cancel-button-text="'Back'"
        :submit-button-text="is_last_item ? 'Done' : 'Next'"
        @cancel-click="onBackClick"
        @submit-click="onNextClick"
        v-for="(prompt, index) in def.prompts"
      />
    </div>
  </div>
</template>

<script>
  import hubspot_def from '../data/builder/hubspot-example.yml'
  import BuilderItemForm from '@/components/BuilderItemForm'

  const getDefaultState = (def) => {
    return {
      def,
      active_idx: 0,
    }
  }

  export default {
    metaInfo: {
      title: '[Admin] Prototype'
    },
    components: {
      BuilderItemForm
    },
    data() {
      return getDefaultState(hubspot_def)
    },
    computed: {
      is_last_item() {
        return this.active_idx == this.def.prompts.length - 1
      }
    },
    methods: {
      onBackClick() {
        if (this.active_idx > 0) {
          this.active_idx--
        }
      },
      onNextClick() {
        if (this.is_last_item) {
          this.$emit('done-clicked')
        } else {
          this.active_idx++
        }
      }
    }
  }
</script>
