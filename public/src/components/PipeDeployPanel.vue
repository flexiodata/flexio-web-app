<template>
  <div>
    <p class="mt0 ttu fw6 f7 moon-gray">How do you want to run this pipe?</p>
    <el-checkbox-group v-model="checklist">
      <div
        class="bb b--black-05 pv3 ph4"
        :class="index == 0 ? 'bt' : ''"
        v-for="(item, index) in deployment_options"
      >
        <el-checkbox :label="item.key">{{item.label}}</el-checkbox>

        <div class="f8 fw6 lh-copy" style="margin-left: 24px">
          <div v-if="item.key == 'schedule' && is_schedule_deployed">
            <span style="margin-right: 6px">{{schedule_str}}</span>
            <el-button
              type="text"
              size="tiny"
              style="padding: 0; border: 0"
              @click="show_schedule = true"
            >
              Edit...
            </el-button>
            <el-button
              class="invisible"
              size="tiny"
            >
              Copy
            </el-button>
          </div>
          <div v-if="item.key == 'api' && is_api_deployed">
            <span style="margin-right: 6px">{{api_url}}</span>
            <el-button
              type="text"
              size="tiny"
              style="padding: 0; border: 0"
              @click="show_properties = true"
            >
              Edit...
            </el-button>
            <el-button
              type="plain"
              class="hint--top"
              aria-label="Copy to Clipboard"
              size="tiny"
              :data-clipboard-text="api_url"
            >
              Copy
            </el-button>
          </div>
          <div v-if="item.key == 'web' && is_web_deployed">
            <span style="margin-right: 6px">{{runtime_url}}</span>
            <el-button
              type="text"
              size="tiny"
              style="padding: 0; border: 0"
              @click="show_runtime_configure = true"
            >
              Edit...
            </el-button>
            <el-button
              type="plain"
              class="hint--top"
              aria-label="Copy to Clipboard"
              size="tiny"
              :data-clipboard-text="runtime_url"
            >
              Copy
            </el-button>
          </div>
        </div>
      </div>
    </el-checkbox-group>
    <div class="mt3 pv3 ph4 tc bg-nearer-white">
      <h4 class="fw6">
        <transition name="el-zoom-in-center" mode="out-in">
          <span v-bind:key="is_deployed">
            {{is_deployed ? 'Turn this pipe off to edit and test it.' : 'Turn this pipe on to deploy and run it.'}}
          </span>
        </transition>
      </h4>
      <div
        class="flex flex-row items-center justify-center mv3"
      >
        <span class="ttu f6 fw6">Your pipe is</span>
        <LabelSwitch
          class="dib ml2 hint--bottom-left"
          active-color="#13ce66"
          aria-label="Turn pipe on"
          v-model="is_deployed"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import * as sched from '../constants/schedule'
  import LabelSwitch from './LabelSwitch.vue'

  export default {
    props: {
      isModeRun: {
        type: Boolean,
        required: true
      },
      eid: {
        type: String,
        required: true
      },
      identifier: {
        type: String,
        required: true
      },
      schedule: {
        // must be an object or null
        required: true
      },
      deploymentItems: {
        type: Array,
        default: () => { return [] }
      },
      showSchedulePanel: {
        type: Boolean,
        default: false
      },
      showRuntimeConfigurePanel: {
        type: Boolean,
        default: false
      }
    },
    components: {
      LabelSwitch
    },
    data() {
      return {
        deployment_options: [
          {
            key: 'schedule',
            label: 'Run on a schedule',
          },
          {
            key: 'api',
            label: 'Run using an API endpoint',
          },
          {
            key: 'web',
            label: 'Run using a Flex.io Web Interface',
          }
        ]
      }
    },
    computed: {
      api_url() {
        return 'https://api.flex.io/v1/me/pipes/' + this.identifier
      },
      runtime_url() {
        return 'https://' + window.location.hostname + '/app/pipes/' + this.eid + '/run'
      },
      is_deployed: {
        get() {
          return this.isModeRun
        },
        set(value) {
          this.$emit('update:isModeRun', value)
        }
      },
      checklist: {
        get() {
          return this.deploymentItems
        },
        set(value) {
          this.$emit('update:deploymentItems', value)
        }
      },
      show_properties: {
        get() {
          return this.showPropertiesPanel
        },
        set(value) {
          this.$emit('update:showPropertiesPanel', value)
        }
      },
      show_runtime_configure: {
        get() {
          return this.showRuntimeConfigurePanel
        },
        set(value) {
          this.$emit('update:showRuntimeConfigurePanel', value)
        }
      },
      show_schedule: {
        get() {
          return this.showSchedulePanel
        },
        set(value) {
          this.$emit('update:showSchedulePanel', value)
        }
      },
      is_schedule_deployed() {
        return _.includes(this.checklist, 'schedule')
      },
      is_api_deployed() {
        return _.includes(this.checklist, 'api')
      },
      is_web_deployed() {
        return _.includes(this.checklist, 'web')
      },
      schedule_str() {
        var s = this.schedule
        switch (s.frequency) {
          case sched.SCHEDULE_FREQUENCY_ONE_MINUTE:
            return 'Every minute'
          case sched.SCHEDULE_FREQUENCY_FIVE_MINUTES:
            return 'Every 5 minutes'
          case sched.SCHEDULE_FREQUENCY_FIFTEEN_MINUTES:
            return 'Every 15 minutes'
          case sched.SCHEDULE_FREQUENCY_THIRTY_MINUTES:
            return 'Every 30 minutes'
          case sched.SCHEDULE_FREQUENCY_HOURLY:
            return 'Every hour'
          case sched.SCHEDULE_FREQUENCY_DAILY:
            return 'Every day at ' + this.getTimeStr()
          case sched.SCHEDULE_FREQUENCY_WEEKLY:
            return 'Every ' + this.getDayStr() + ' of every week at ' + this.getTimeStr()
          case sched.SCHEDULE_FREQUENCY_MONTHLY:
            return 'On the ' + this.getMonthDayStr() + ' of every month at ' + this.getTimeStr()
        }
      }
    },
    methods: {
      getTimeStr() {
        var times = _.get(this.schedule, 'times', [])
        times = _.map(times, (t) => {
          return moment().hour(t.hour).minute(t.minute).format('LT');
        })
        return times.join(', ')
      },
      getDayStr() {
        var days = _.get(this.schedule, 'days', [])
        days = _.map(days, (d) => {
          return moment().isoWeekday(d).format('dddd')
        })
        return days.join(', ')
      },
      getMonthDayStr() {
        var days = _.get(this.schedule, 'days', [])
        days = _.map(days, (d) => {
          switch (d) {
            case 1:
              return 'first day'
            case 15:
              return 'fifteenth day'
            case 'last':
              return 'last day'
          }
        })
        return days.join(', ')
      }
    }
  }
</script>
