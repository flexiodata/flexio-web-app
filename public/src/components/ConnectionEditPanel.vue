<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <el-steps
      class="mb3"
      align-center
      :active="active_step_idx"
      v-show="showSteps"
    >
      <el-step title="Choose Source" />
      <el-step title="Authenticate" />
      <el-step title="Edit Properties" />
    </el-steps>

    <!-- step 1: choose source -->
    <IconList
      items="services"
      :filter-by="filterBy"
      v-show="active_step == 'choose-source'"
    />

    <!-- step 2: authenticate -->

    <!-- footer -->
    <ButtonBar
      class="mt4"
      @cancel-click="onCancel"
      @submit-click="onSubmit"
      v-bind="$attrs"
      v-show="showFooter && active_step != 'choose-source'"
    />
  </div>
</template>

<script>
  import IconList from '@/components/IconList'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultState = () => {
    return {
      edit_connection: {},
      active_step: 'choose-source'
    }
  }

  export default {
    inheritAttrs: false,
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
      showSteps: {
        type: Boolean,
        default: true
      },
      connection: {
        type: Object,
        default: () => {}
      },
      filterBy: {
        type: Function
      },
      mode: {
        type: String,
        default: 'add' // 'add' or 'edit'
      }
    },
    components: {
      IconList,
      HeaderBar,
      ButtonBar,
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
      cname() {
        return _.get(this.connection, 'name', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit' ? `Edit "${this.cname}" Connection` : 'New Connection'
      },
      active_step_idx() {
        switch (this.active_step) {
          case 'choose-source': return 0
          case 'authenticate': return 1
          case 'choose-name': return 2
        }

        return 1
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(this.connection))
      },
      onClose() {
        this.initSelf()
        this.$emit('close')
      },
      onCancel() {
        this.initSelf()
        this.$emit('cancel')
      },
      onSubmit() {
        if (_.get(this.edit_mount, 'eid_status') == OBJECT_STATUS_PENDING) {
          var team_name = this.active_team_name
          var eid = this.edit_mount.eid
          var eid_status = OBJECT_STATUS_AVAILABLE
          var attrs = { eid_status }

          this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
            this.edit_mount = _.assign({}, this.edit_mount, _.cloneDeep(response.data))
            this.$emit('submit', this.edit_mount)
          })
        } else {
          this.$emit('submit', this.edit_mount)
        }
      },
    }
  }
</script>
