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
      v-show="showFooter && has_mount"
    />
  </div>
</template>

<script>
  import randomstring from 'randomstring'
  import { mapState } from 'vuex'
  import { OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import { slugify } from '@/utils'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import TextSeparator from '@/components/TextSeparator'
  import MixinConnection from '@/components/mixins/connection'

  const CONNECTION_MODE_RESOURCE = 'R'
  const CONNECTION_MODE_FUNCTION = 'F'

  const defaultAttrs = () => {
    var connection_info = {
      method: '',
      url: '',
      auth: 'none',
      username: '',
      password: '',
      token: '',
      access_token: '',
      refresh_token: '',
      expires: '',
      headers: {},
      data: {}
    }

    var suffix = getNameSuffix()

    return {
      eid: null,
      eid_status: OBJECT_STATUS_PENDING,
      name: `connection-${suffix}`,
      title: '',
      description: '',
      connection_type: '',
      connection_mode: CONNECTION_MODE_RESOURCE,
      connection_info
    }
  }

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

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
        default: () => {}
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
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      cname() {
        return _.get(this.mount, 'name', '')
      },
      has_mount() {
        var eid = _.get(this.edit_mount, 'eid', '')
        return eid.length > 0
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit' ? `Edit "${this.cname}" Function Mount` : 'New Function Mount'
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
      },
      filterByFunctionMount(connection) {
        return this.$_Connection_isFunctionMount(connection)
      },
      createPendingConnection(item) {
        var ctype = item.connection_type
        var ctitle = item.title || item.service_name
        var service_slug = slugify(ctitle)
        var team_name = this.active_team_name

        // when using this panel in 'add' mode, if the panel
        // is created with a `mount` prop, use the attributes of
        // this prop when creating the pending connection -- we use this
        // for creating 'function mount' connections
        var prop_mount_attrs = _.cloneDeep(this.mount)

        var attrs = _.assign({}, defaultAttrs(), prop_mount_attrs, {
          eid_status: OBJECT_STATUS_PENDING,
          name: `${service_slug}-` + getNameSuffix(16),
          title: ctitle,
          connection_type: ctype
        })

        this.$store.dispatch('connections/create', { team_name, attrs }).then(response => {
          var mount = _.cloneDeep(response.data)
          mount.name = `${service_slug}-` + getNameSuffix(4),
          this.omitMaskedValues(mount)
        })
      },
      omitMaskedValues(attrs) {
        var connection_info = _.get(attrs, 'connection_info', {})
        connection_info = _.omitBy(connection_info, (val, key) => { return val == '*****' })

        var update_attrs = _.assign({}, attrs, { connection_info })
        this.edit_mount = _.assign({}, this.edit_mount, update_attrs)
      },
      onFunctionPackClick(item) {
        alert(item.id)
      },
      onServiceClick(item) {
        this.createPendingConnection(item)
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
