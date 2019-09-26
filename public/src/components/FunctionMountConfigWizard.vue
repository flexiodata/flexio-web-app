<template>
  <div>
    <BuilderItemForm
      :key="prompt.name"
      :item="prompt"
      :visible="index == active_idx"
      :cancel-button-visible="active_idx > 0"
      :cancel-button-text="'Back'"
      :submit-button-text="is_last_item ? 'Done' : 'Next'"
      @values-change="onValuesChange"
      @cancel-click="onBackClick"
      @submit-click="onNextClick"
      v-for="(prompt, index) in prompts"
    />
  </div>
</template>

<script>
  import BuilderItemForm from '@/components/BuilderItemForm'

  const getDefaultState = () => {
    return {
      active_idx: 0,
      config: {}
    }
  }

  export default {
    props: {
      manifest: {
        type: Object,
        default: () => {}
      }
    },
    components: {
      BuilderItemForm
    },
    data() {
      return getDefaultState()
    },
    computed: {
      prompts() {
        return _.get(this.manifest, 'prompts', [])
      },
      is_last_item() {
        return this.active_idx == this.prompts.length - 1
      },
      url() {
        return _.get(this.manifest, 'image.src', '')
      },
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
          this.$emit('submit', this.config)
        } else {
          this.active_idx++
        }
      }
    }
  }
</script>
