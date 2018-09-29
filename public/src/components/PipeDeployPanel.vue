<template>
  <div>
    <p class="mt0 ttu fw6 f7 moon-gray">How do you want to run this pipe?</p>
    <el-checkbox-group v-model="checklist">
      <div
        class="bb b--black-05 pv3 ph4"
        :class="index == 0 ? 'bt' : ''"
        v-for="(item, index) in deployment_options"
      >
        <el-checkbox
          :label="item.key"
          :disabled="item.always_on"
        >
          {{item.label}}
        </el-checkbox>
        <el-tag
          class="ttu b ml2"
          size="mini"
          v-if="false && item.is_pro"
        >
          Pro
        </el-tag>
        <div
          class="mt1 f8 lh-copy"
          style="margin-left: 24px"
          v-if="item.key == 'schedule' && is_schedule_deployed"
        >
          <span class="mr1">{{schedule_str}}</span>
          <el-button
            type="text"
            size="tiny"
            style="padding: 0; border: 0"
            @click="show_schedule = true"
          >
            Configure...
          </el-button>
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
      schedule: {
        type: Object,
        required: true
      },
      deploymentItems: {
        type: Array,
        default: () => { return [] }
      },
      showSchedulePanel: {
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
            always_on: false,
            is_pro: false
          },
          {
            key: 'api',
            label: 'Run using an API endpoint',
            always_on: false,
            is_pro: true
          },
          {
            key: 'manual',
            label: 'Run using a Flex.io Web Interface',
            always_on: false,
            is_pro: false
          }
        ]
      }
    },
    computed: {
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
          //return _.uniq(['manual'].concat(this.deploymentItems))
          return this.deploymentItems
        },
        set(value) {
          this.$emit('update:deploymentItems', value)
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
      schedule_str() {
        var s = this.schedule
        switch (s.frequency) {
          case sched.SCHEDULE_FREQUENCY_ONE_MINUTE:
            return 'Your pipe will run every minute'
          case sched.SCHEDULE_FREQUENCY_FIVE_MINUTES:
            return 'Your pipe will run every 5 minutes'
          case sched.SCHEDULE_FREQUENCY_FIFTEEN_MINUTES:
            return 'Your pipe will run every 15 minutes'
          case sched.SCHEDULE_FREQUENCY_THIRTY_MINUTES:
            return 'Your pipe will run every 30 minutes'
          case sched.SCHEDULE_FREQUENCY_HOURLY:
            return 'Your pipe will run every hour'
          case sched.SCHEDULE_FREQUENCY_DAILY:
            return 'Your pipe will run every day at ' + this.getTimeStr()
          case sched.SCHEDULE_FREQUENCY_WEEKLY:
            return 'Your pipe will run every ' + this.getDayStr() + ' of every week at ' + this.getTimeStr()
          case sched.SCHEDULE_FREQUENCY_MONTHLY:
            return 'Your pipe will run on the ' + this.getMonthDayStr() + ' of every month at ' + this.getTimeStr()
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
