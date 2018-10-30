<template>
  <div>
    <el-table
      class="w-100 activity-list"
      size="large"
      :data="limited_pipes"
      @sort-change="onSortChange"
    >
      <el-table-column
        prop="name"
        label="Name"
        :sortable="true"
      >
        <template slot-scope="scope">
          <div class="link">
            <h3 class="f6 f5-ns fw6 lh-title dark-gray mv0 mr2 truncate title">{{scope.row.name}}</h3>
            <div class="dn db-ns" v-if="hasDescription(scope.row)">
              <h5 class="f6 fw4 mt1 mb0 lh-copy light-silver truncate description" ref="description">{{scope.row.description}}</h5>
            </div>
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="execution_cnt"
        label="Executions"
        align="right"
        :sortable="true"
        :width="120"
      >
        <template slot-scope="scope">
          <span class="f6">{{scope.row.execution_cnt}}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="Deployment"
        align="right"
        :width="130"
      >
        <template slot-scope="scope">
          <div class="flex flex-row items-center justify-end">
            <div
              class="hint--top"
              style="margin-left: 3px"
              :aria-label="item.tooltip"
              v-for="item in getDeploymentIcons(scope.row)"
            >
              <i class="db material-icons md-21" :class="item.is_deployed ? 'blue' : 'o-10'">{{item.icon}}</i>
            </div>
          </div>
        </template>
      </el-table-column>

      <el-table-column
        prop="deploy_mode"
        label="Status"
        align="center"
        :sortable="true"
        :width="100"
      >
        <template slot-scope="scope">
          <div class="flex flex-row items-center justify-center mr1">
            <LabelSwitch
              class="db"
              active-color="#13ce66"
              :width="58"
            />
          </div>
        </template>
      </el-table-column>

      <el-table-column
        label=""
        align="right"
        :width="50"
      >
        <template slot-scope="scope">
          <div @click.stop>
            <el-dropdown
              trigger="click"
              @command="onCommand"
            >
              <span class="el-dropdown-link dib pointer black-30 hover-black">
                <i class="material-icons v-mid">expand_more</i>
              </span>
              <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
                <el-dropdown-item class="flex flex-row items-center ph2" command="open"><i class="material-icons mr3">edit</i> Edit</el-dropdown-item>
                <el-dropdown-item class="flex flex-row items-center ph2" command="duplicate"><i class="material-icons mr3">content_copy</i> Duplicate</el-dropdown-item>
                <div class="mv2 bt b--black-10"></div>
                <el-dropdown-item class="flex flex-row items-center ph2" command="delete"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </div>
        </template>
      </el-table-column>
      <div slot="empty">No pipes to show</div>
    </el-table>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import pipe_util from '../utils/pipe'
  import LabelSwitch from './LabelSwitch.vue'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  const ACTIVE = 'A'
  const INACTIVE = 'I'

  export default {
    props: {
      items: {
        type: Array
      },
      filterBy: {
        type: Function
      },
      start: {
        type: Number
      },
      limit: {
        type: Number,
        default: 50
      }
    },
    components: {
      LabelSwitch
    },
    computed: {
      all_pipes() {
        if (_.isArray(this.items)) {
          return this.items
        }

        return this.getAllPipes()
      },
      mapped_pipes() {
        return _.map(this.all_pipes, p => {
          return _.assign({}, p, {
            execution_cnt: parseInt(_.get(p, 'stats.total_count', '0'))
          })
        })
      },
      filtered_pipes() {
        var pipes = this.mapped_pipes
        return this.filterBy ? _.filter(pipes, this.filterBy) : pipes
      },
      limited_pipes() {
        if (!_.isNumber(this.start)) {
          return this.filtered_pipes
        }

        return _.filter(this.filtered_pipes, (item, idx) => {
          return idx >= this.start && idx < (this.start + this.limit)
        })
      },
      has_start() {
        return _.isNumber(this.start)
      }
    },
    methods: {
      ...mapGetters([
        'getAllPipes'
      ]),
      getPipeRoute(row) {
        return '/pipes/' + _.get(row, 'eid')
      },
      getIndex(idx) {
        return idx + this.start + 1
      },
      getScheduleStr(row) {
        return pipe_util.getDeployScheduleStr(row.schedule)
      },
      isDeployedSchedule(row) {
        return row.deploy_schedule === ACTIVE
      },
      isDeployedApi(row) {
        return row.deploy_api === ACTIVE
      },
      isDeployedUi(row) {
        return row.deploy_ui === ACTIVE
      },
      isDeployedEmail(row) {
        return row.deploy_email === ACTIVE
      },
      getTooltipSchedule(row) {
        return this.isDeployedSchedule(row) ? 'Scheduler ON: ' + this.getScheduleStr(row) : 'Scheduler OFF'
      },
      getTooltipApi(row) {
        return this.isDeployedApi(row) ? 'API Endpoint ON' : 'API Endpoint OFF'
      },
      getTooltipEmail(row) {
        return this.isDeployedEmail(row) ? 'Email Trigger ON' : 'Email Trigger OFF'
      },
      getTooltipUi(row) {
        return this.isDeployedUi(row) ? 'Flex.io Web Interface ON' : 'Flex.io Web Interface OFF'
      },

      getDeploymentIcons(row) {
        return [
          {
            icon: 'schedule',
            tooltip: this.getTooltipSchedule(row),
            is_deployed: this.isDeployedSchedule(row)
          },
          {
            icon: 'code',
            tooltip: this.getTooltipApi(row),
            is_deployed: this.isDeployedApi(row)
          },
          {
            icon: 'email',
            tooltip: this.getTooltipEmail(row),
            is_deployed: this.isDeployedEmail(row)
          },
          {
            icon: 'offline_bolt',
            tooltip: this.getTooltipUi(row),
            is_deployed: this.isDeployedUi(row)
          }
        ]
      },
      hasDescription(row) {
        return _.get(row, 'description', '').length > 0
      },
      onCommand(cmd) {
        /*
        switch (cmd) {
          case 'open':      return this.openPipe()
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
        */
      },
      onSortChange(obj) {
        this.$emit('sort-change', obj)
      },
      onItemEdit(item) {
        this.$emit('item-edit', item)
      },
      onItemDuplicate(item) {
        this.$emit('item-duplicate', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>

<style lang="stylus">
  //.pipe-item-move
  //  transition: transform 0.75s
</style>
