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
    <h3>Function Packs</h3>
    <p>Choose from one of the pre-configured function packs below.</p>
    <IconList
      class="ph2"
      items="mounts"
      @item-click="onFunctionPackClick"
    />
    <TextSeparator class="w5 center mv4" />
    <h3>Host Your Own</h3>
    <p>Host your own function pack using one of the services below. Visit our <a href="#" class="blue" target="_blank">online documentation</a> to learn how to <a href="#" class="blue" target="_blank">create and host your own function pack</a>.</p>
    <IconList
      class="ph2"
      items="services"
      :filter-by="filterByFunctionMount"
      @item-click="onServiceClick"
    />

    <!-- footer -->
    <ButtonBar
      class="mt4"
      :submit-button-text="submit_label"
      @cancel-click="onCancel"
      @submit-click="submit"
      v-show="showFooter && show_footer"
    />
  </div>
</template>

<script>
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import TextSeparator from '@/components/TextSeparator'
  import MixinConnection from '@/components/mixins/connection'

  const getDefaultState = (component) => {
    return {
      edit_mount: {},
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
    mixins: [MixinConnection],
    components: {
      HeaderBar,
      ButtonBar,
      IconList,
      TextSeparator,
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
      show_footer() {
        return true
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
      filterByFunctionMount(connection) {
        return this.$_Connection_isFunctionMount(connection)
      },
      onFunctionPackClick(item) {
        alert(item.id)
      },
      onServiceClick(item) {
        alert(item.connection_type)
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
