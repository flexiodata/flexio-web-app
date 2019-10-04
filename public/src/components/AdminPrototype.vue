<template>
  <div class="pa5 overflow-y-scroll">
    <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box trans-mw" style="max-width: 800px">
      <BuilderItem
        :key="prompt.name"
        :item="prompt"
        :visible="index == active_idx"
        :cancel-button-visible="active_idx > 0"
        :cancel-button-text="'Back'"
        :submit-button-text="is_last_item ? 'Done' : 'Next'"
        @values-change="onValuesChange"
        @cancel-click="onBackClick"
        @submit-click="onNextClick"
        v-for="(prompt, index) in setup_template.prompts"
      />
    </div>
  </div>
</template>

<script>
  //import hubspot_setup_template from '@/data/builder/hubspot-example.yml'
  import dropbox_setup_template from '@/data/builder/dropbox-example.yml'
  import BuilderItem from '@/components/BuilderItem'

  const getDefaultState = (setup_template) => {
    return {
      setup_template,
      active_idx: 0,
      config: {}
    }
  }

  export default {
    metaInfo: {
      title: '[Admin] Prototype'
    },
    components: {
      BuilderItem
    },
    data() {
      return getDefaultState(dropbox_setup_template)
    },
    computed: {
      is_last_item() {
        return this.active_idx == this.setup_template.prompts.length - 1
      }
    },
    methods: {
      onValuesChange(values) {
        this.config = _.assign({}, this.config, values)
      },
      onBackClick() {
        if (this.active_idx > 0) {
          this.active_idx--
        }
      },
      onNextClick() {
        if (this.is_last_item) {
          this.$emit('submit')
        } else {
          this.active_idx++
        }
      }
    }
  }
</script>
