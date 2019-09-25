<template>
  <div>
    <div class="flex flex-column br2 ba b--black-10 pa4">
      <div
        class="form-logo"
        v-show="showFormLogo && url.length > 0"
      >
        <ServiceIcon
          ref="icon"
          class="form-logo-icon"
          :url="url"
          :icon-height.sync="icon_height"
          :calc-height="true"
        />
      </div>
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
  </div>
</template>

<script>
  import ServiceIcon from '@/components/ServiceIcon'
  import BuilderItemForm from '@/components/BuilderItemForm'

  const getDefaultState = () => {
    return {
      icon_height: 0,
      active_idx: 0,
      config: {}
    }
  }

  export default {
    props: {
      manifest: {
        type: Object,
        default: () => {}
      },
      showFormLogo: {
        type: Boolean,
        default: true
      },
    },
    components: {
      ServiceIcon,
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

<style lang="stylus" scoped>
  .form-logo
    background: #fff
    margin: 0 auto 1.5rem
    padding: 0 8px
    position: relative

  .form-logo-icon
    border-radius: 4px
    max-height: 48px

  .form-title
    margin-bottom: 24px
</style>
