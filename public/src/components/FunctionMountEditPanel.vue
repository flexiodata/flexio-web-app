<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <!-- body -->

    <!-- footer -->
    <ButtonBar
      class="mt4"
      :submit-button-text="submit_label"
      @cancel-click="onCancel"
      @submit-click="submit"
      v-show="showFooter"
    />
  </div>
</template>

<script>
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultState = (component) => {
    return {
      edit_mount: {}
    }
  }

  export default {
    props: {
      title: {
        type: String,
        default: ''
      },
      showHeader: {
        type: Boolean,
        default: true
      },
      showFooter: {
        type: Boolean,
        default: true
      },
      mount: {
        type: Object,
        required: true
      },
      mode: {
        type: String,
        default: 'add' // 'add' or 'edit'
      }
    },
    components: {
      HeaderBar,
      ButtonBar
    },
    watch: {
      mount: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      fname() {
        return _.get(this.mount, 'name', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit' ? `Edit "${this.fname}" Function Mount` : 'New Function Mount'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create function mount'
      },
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_mount = _.assign({}, this.edit_mount, _.cloneDeep(this.mount))

        // reset the form
        if (this.$refs.form) {
          this.$refs.form.resetFields()
        }
      },
      onClose() {
        this.initSelf()
        this.$emit('close')
      },
      onCancel() {
        this.initSelf()
        this.$emit('cancel')
      },
      submit() {
        this.$emit('update-mount', {})
      },
    }
  }
</script>
