<template>
  <article
    class="no-select trans-pm"
    :class="isHeader ? 'thead' : 'tbody pointer'"
    @click="openPipe"
  >
    <div class="flex flex-row items-center" :class="isHeader ? 'th' : 'td'">
      <div
        class="flex-none"
        style="padding: 16px 10px"
        v-if="showSelectionCheckbox"
      >
        <el-checkbox
          :checked="selected"
          @change="$emit('select-all', !selected)"
          v-if="isHeader"
        />
        <el-checkbox
          :checked="selected"
          @change="$emit('select', !selected, item.eid)"
          v-else
        />
      </div>
      <div
        class="flex-fill"
        style="padding: 16px 10px"
        v-if="isHeader"
      >
        Name
        <SortArrows
          sort-key="name"
          :is-active="sort == 'name'"
          :direction="sortDirection"
          @sort="onSort"
        />
      </div>
      <div
        class="flex-fill link "
        :class="show_description_tooltip ? 'hint--bottom hint--large' : ''"
        :to="pipe_route"
        :aria-label="item.description"
        v-else
      >
        <div style="padding: 16px 10px">
          <div class="flex-l flex-row items-center">
            <h3 class="f6 f5-ns fw6 lh-title dark-gray mv0 mr2 truncate title">{{item.name}}</h3>
          </div>
          <div class="dn db-ns" v-if="has_description">
            <h5 class="f6 fw4 mt1 mb0 lh-copy light-silver truncate description" ref="description">{{item.description}}</h5>
          </div>
        </div>
      </div>
      <div
        class="dn db-l tr"
        style="width: 110px"
      >
        <div v-if="isHeader">
          Executions
          <SortArrows
            sort-key="execution_cnt"
            :is-active="sort == 'execution_cnt'"
            :direction="sortDirection"
            @sort="onSort"
          />
        </div>
        <div class="f6" v-else>{{execution_cnt}}</div>
      </div>
      <div
        class="dn db-l ml3 ml4-l tr"
        style="width: 110px"
      >
        <div v-if="isHeader">Deployment</div>
        <div v-else>
          <div
            class="hint--top"
            style="margin-right: -1px"
            :aria-label="schedule_tooltip"
          >
            <i
              class="material-icons md-21"
              :class="is_deployed_schedule ? 'blue' : 'o-10'"
            >
              schedule
            </i>
          </div>
          <div
            class="hint--top"
            style="margin-right: -1px"
            :aria-label="api_endpoint_tooltip"
          >
            <i
              class="material-icons md-21"
              :class="is_deployed_api ? 'blue' : 'o-10'"
            >
              code
            </i>
          </div>
          <div
            class="hint--top"
            style="margin-right: -1px"
            :aria-label="email_tooltip"
          >
            <i
              class="material-icons md-21"
              :class="is_deployed_email ? 'blue' : 'o-10'"
            >
              email
            </i>
          </div>
          <div
            class="hint--top"
            style="margin-right: -1px"
            :aria-label="runtime_tooltip"
          >
            <i
              class="material-icons md-21"
              :class="is_deployed_ui ? 'blue' : 'o-10'"
            >
              offline_bolt
            </i>
          </div>
        </div>
      </div>
      <div class="flex-none nt3 nb3 ml3 ml4-l tc" style="width: 90px">
        <div v-if="isHeader">
          Status
          <SortArrows
            sort-key="deploy_mode"
            :is-active="sort == 'deploy_mode'"
            :direction="sortDirection"
            @sort="onSort"
          />
        </div>
        <div class="pv3" @click.stop v-else>
          <LabelSwitch
            class="dib hint--top"
            active-color="#13ce66"
            :width="58"
            :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
            v-model="is_deployed"
          />
        </div>
      </div>
      <div class="flex-none pl2" @click.stop>
        <el-dropdown
          trigger="click"
          :class="{ 'invisible': isHeader }"
          @command="onCommand"
        >
          <span
            class="el-dropdown-link dib pointer black-30 hover-black"
            style="padding: 16px 10px"
          >
            <i class="material-icons v-mid">expand_more</i>
          </span>
          <el-dropdown-menu style="min-width: 10rem; margin-top: -0.5rem" slot="dropdown">
            <el-dropdown-item class="flex flex-row items-center ph2" command="open"><i class="material-icons mr3">edit</i> Edit</el-dropdown-item>
            <el-dropdown-item class="flex flex-row items-center ph2" command="duplicate"><i class="material-icons mr3">content_copy</i> Duplicate</el-dropdown-item>
            <div class="mv2 bt b--black-10"></div>
            <el-dropdown-item class="flex flex-row items-center ph2" command="delete"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  import pipe_util from '../utils/pipe'
  import { ROUTE_PIPES } from '../constants/route'
  import LabelSwitch from './LabelSwitch.vue'
  import SortArrows from './SortArrows.vue'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  const ACTIVE = 'A'
  const INACTIVE = 'I'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      isHeader: {
        type: Boolean,
        default: false
      },
      showSelectionCheckbox: {
        type: Boolean,
        default: false
      },
      selected: {
        type: Boolean,
        default: false
      },
      sort: {
        type: String,
        default: 'none'
      },
      sortDirection: {
        type: String,
        default: 'none' // 'asc', 'desc', 'none'
      }
    },
    components: {
      LabelSwitch,
      SortArrows
    },
    data() {
      return {
        is_mounted: false
      }
    },
    computed: {
      has_description() {
        return _.get(this.item, 'description', '').length > 0
      },
      is_deployed_schedule() {
        return _.get(this.item, 'deploy_schedule', INACTIVE) == ACTIVE
      },
      is_deployed_api() {
        return _.get(this.item, 'deploy_api', INACTIVE) == ACTIVE
      },
      is_deployed_ui() {
        return _.get(this.item, 'deploy_ui', INACTIVE) == ACTIVE
      },
      is_deployed_email() {
        return _.get(this.item, 'deploy_email', INACTIVE) == ACTIVE
      },
      is_deployed: {
        get() {
          return _.get(this.item, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
        },
        set(value) {
          var doSet = () => {
            var deploy_mode = value === false ? DEPLOY_MODE_BUILD : DEPLOY_MODE_RUN

            var attrs = {
              deploy_mode
            }

            this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
          }

          if (value === false) {
            this.$confirm('This pipe is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn pipe off?', {
              confirmButtonClass: 'ttu b',
              cancelButtonClass: 'ttu b',
              confirmButtonText: 'Turn pipe off',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              doSet()
              this.$store.track("Turned Pipe Off")
            }).catch(() => {
              this.$store.track("Turned Pipe Off (Canceled)")
            })
          } else {
            doSet()
            this.$store.track("Turned Pipe On")
          }
        }
      },
      pipe_route() {
        return { name: ROUTE_PIPES, params: { eid: this.item.eid } }
      },
      execution_cnt() {
        return parseInt(_.get(this.item, 'stats.total_count', '0'))
      },
      schedule_str() {
        return pipe_util.getDeployScheduleStr(this.item.schedule)
      },
      schedule_tooltip() {
        return this.is_deployed_schedule ? 'Scheduler ON: ' + this.schedule_str : 'Scheduler OFF'
      },
      api_endpoint_tooltip() {
        return this.is_deployed_api ? 'API Endpoint ON' : 'API Endpoint OFF'
      },
      email_tooltip() {
        return this.is_deployed_email ? 'Email Trigger ON' : 'Email Trigger OFF'
      },
      runtime_tooltip() {
        return this.is_deployed_ui ? 'Flex.io Web Interface ON' : 'Flex.io Web Interface OFF'
      },
      show_description_tooltip() {
        return false

        if (this.is_mounted) {
          var el = this.$refs.description
          if (el) {
            return (el.offsetWidth < el.scrollWidth) ? true : false
          }
        }
        return false
      }
    },
    mounted() {
      this.$nextTick(() => { this.is_mounted = true })
    },
    methods: {
      openPipe() {
        if (this.isHeader) {
          return
        }

        this.$store.track('Opened Pipe', this.getAnalyticsPayload(this.item))
        this.$router.push(this.pipe_route)
      },
      getAnalyticsPayload(pipe) {
        var analytics_payload = _.pick(pipe, ['eid', 'name', 'alias'])
        _.assign(analytics_payload, { title: pipe.name })

        return analytics_payload
      },
      onCommand(cmd) {
        switch (cmd) {
          case 'open':      return this.openPipe()
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      },
      onSort(sort_key, direction) {
        this.$emit('update:sort', sort_key)
        this.$emit('update:sortDirection', direction)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  // match Element UI's .el-table thead styling
  .thead
    border-bottom: 1px solid #ebeef5
    color: #909399
    font-size: 14px
  .th
    font-weight: bold
  .tbody
    &:hover
      .td
        background-color: #f5f7fa
      .title
      .description
        color: #409eff
  .td
    border-bottom: 1px solid #ebeef5
</style>
