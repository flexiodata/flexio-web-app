<template>
  <div>
    <ServiceIconWrapper :url="setup_template_image_url">
      <BuilderItemForm
        :key="prompt.name"
        :item="prompt"
        :default-values="setupConfig"
        :visible="index == active_idx"
        :cancel-button-visible="active_idx > 0"
        :cancel-button-text="'Back'"
        :submit-button-text="'Next'"
        @values-change="onValuesChange"
        @cancel-click="onBackClick"
        @submit-click="onNextClick"
        v-for="(prompt, index) in prompts"
      />
      <slot name="no-prompts" v-if="prompts.length == 0">
        <div class="tc f6 fw4 lh-copy moon-gray"><em>No configuration is required for this function mount.</em></div>
      </slot>
    </ServiceIconWrapper>
  </div>
</template>

<script>
  import BuilderItemForm from '@/components/BuilderItemForm'
  import ServiceIconWrapper from '@/components/ServiceIconWrapper'

  const getDefaultState = () => {
    return {
      active_idx: 0,
      config: {}
    }
  }

  export default {
    props: {
      setupTemplate: {
        type: Object,
        default: () => {}
      },
      setupConfig: {
        type: Object,
        default: () => {}
      }
    },
    components: {
      BuilderItemForm,
      ServiceIconWrapper
    },
    data() {
      return getDefaultState()
    },
    computed: {
      prompts() {
        return _.get(this.setupTemplate, 'prompts', [])
      },
      is_last_item() {
        return this.active_idx == this.prompts.length - 1
      },
      setup_template_image_url() {
        return _.get(this.setupTemplate, 'image.src', '')
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
