<template>
  <div class="mid-gray">
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center" v-if="showHeader">
        <span class="flex-fill f4">Schedule '{{pipe.name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="$emit('close')"></i>
      </div>
    </div>

    <div class="flex flex-row items-center nt3 mb3">
      <el-switch
        class="hint--bottom"
        active-color="#009900"
        :aria-label="is_scheduled ? 'Scheduled' : 'Not Scheduled'"
        v-model="is_scheduled"
      />
      <span
        class="fw6 f5 pl2 pointer"
        @click.stop="is_scheduled = !is_scheduled"
      >
        <transition name="el-zoom-in-center" mode="out-in">
          <span v-bind:key="is_scheduled">
            {{is_scheduled ? 'Scheduled' : 'Not Scheduled'}}
          </span>
        </transition>
      </span>
    </div>
    <div class="flex flex-row mb3">
      <div class="flex-fill mr4">
        <label class="db f8">Frequency</label>
        <el-select
          class="w-100"
          placeholder="Frequency"
          v-model="edit_pipe.schedule.frequency"
        >
          <el-option
            :label="option.label"
            :value="option.val"
            :key="option.val"
            v-for="option in frequency_options"
          />
        </el-select>
      </div>
      <div class="flex-fill">
        <label class="db f8">Timezone</label>
        <el-select
          class="w-100"
          placeholder="Search for Timezone"
          filterable
          v-model="edit_pipe.schedule.timezone"
        >
          <el-option
            :label="option.label"
            :value="option.val"
            :key="option.val"
            v-for="option in timezone_options"
          />
        </el-select>
      </div>
    </div>
    <div class="mb3" v-if="is_weekly">
      <label class="db f8">Run on the following days of the week</label>
      <el-select
        class="w-100"
        placeholder="Choose days of the week"
        multiple
        v-model="edit_pipe.schedule.days"
      >
        <el-option
          :label="option.label"
          :value="option.val"
          :key="option.val"
          v-for="option in day_options"
        />
      </el-select>
    </div>
    <div class="mb3" v-if="is_monthly">
      <label class="db f8">Run on the following days of the month</label>
      <el-select
        class="w-100"
        placeholder="Choose days of the month"
        multiple
        v-model="edit_pipe.schedule.days"
      >
        <el-option
          :label="option.label"
          :value="option.val"
          :key="option.val"
          v-for="option in month_options"
        />
      </el-select>
    </div>
    <div v-if="show_times">
      <label class="db f8">Run at the following times</label>
      <time-chooser-list
        class="mb1"
        style="width: 20rem"
        :times="edit_pipe.schedule.times"
        @item-change="updateTime"
        @item-delete="deleteTime"
      />
      <div class="ph1" style="width: 20rem">
        <el-button
          size="mini"
          class="w-100 ttu b"
          @click="addTime"
        >
          Add time
        </el-button>
      </div>
    </div>

    <div class="mt4 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button
        class="ttu b"
        @click="$emit('cancel')"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu b"
        type="primary"
        @click="submit"
      >
        Save changes
      </el-button>
    </div>
  </div>
</template>

<script>
  import { TIMEZONE_UTC } from '../constants/timezone'
  import { timezones } from '../constants/timezone'
  import * as schedule from '../constants/schedule'
  import TimeChooserList from './TimeChooserList.vue'

  const day_options = [
    { label: 'Monday',    val: schedule.SCHEDULE_WEEK_DAY_MON },
    { label: 'Tuesday',   val: schedule.SCHEDULE_WEEK_DAY_TUE },
    { label: 'Wednesday', val: schedule.SCHEDULE_WEEK_DAY_WED },
    { label: 'Thursday',  val: schedule.SCHEDULE_WEEK_DAY_THU },
    { label: 'Friday',    val: schedule.SCHEDULE_WEEK_DAY_FRI },
    { label: 'Saturday',  val: schedule.SCHEDULE_WEEK_DAY_SAT },
    { label: 'Sunday',    val: schedule.SCHEDULE_WEEK_DAY_SUN }
  ]

  const month_options = [
    { label: 'First day',     val: schedule.SCHEDULE_MONTH_DAY_FIRST     },
    { label: 'Fifteenth day', val: schedule.SCHEDULE_MONTH_DAY_FIFTEENTH },
    { label: 'Last day',      val: schedule.SCHEDULE_MONTH_DAY_LAST      }
  ]

  const frequency_options = [
    { label: 'Every 5 minutes',  val: schedule.SCHEDULE_FREQUENCY_FIVE_MINUTES    },
    { label: 'Every 15 minutes', val: schedule.SCHEDULE_FREQUENCY_FIFTEEN_MINUTES },
    { label: 'Every hour',       val: schedule.SCHEDULE_FREQUENCY_HOURLY          },
    { label: 'Every day',        val: schedule.SCHEDULE_FREQUENCY_DAILY           },
    { label: 'Every week',       val: schedule.SCHEDULE_FREQUENCY_WEEKLY          },
    { label: 'Every month',      val: schedule.SCHEDULE_FREQUENCY_MONTHLY         }
  ]

  const timezone_options = _.map(timezones, (tz) => {
    return { label: tz, val: tz }
  })

  const defaultAttrs = () => {
    return {
      schedule: {
        frequency: schedule.SCHEDULE_FREQUENCY_DAILY,
        timezone: TIMEZONE_UTC,
        days: [],
        times: [{ hour: 8, minute: 0 }]
      },
      schedule_status: schedule.SCHEDULE_STATUS_INACTIVE
    }
  }

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'show-header': {
        type: Boolean,
        default: true
      },
      'show-footer': {
        type: Boolean,
        default: true
      },
      'pipe': {
        type: Object,
        default: () => { return {} }
      }
    },
    components: {
      TimeChooserList
    },
    watch: {
      pipe: {
        handler: 'updatePipe',
        immediate: true,
        deep: true
      },
      'edit_pipe.schedule.frequency'() {
        _.set(this.edit_pipe, 'schedule.days', [])
      }
    },
    data() {
      return {
        day_options,
        month_options,
        frequency_options,
        timezone_options,
        edit_pipe: defaultAttrs()
      }
    },
    computed: {
      frequency() {
        return _.get(this.edit_pipe, 'schedule.frequency', schedule.SCHEDULE_FREQUENCY_DAILY)
      },
      is_weekly() {
        return this.frequency == schedule.SCHEDULE_FREQUENCY_WEEKLY
      },
      is_monthly() {
        return this.frequency == schedule.SCHEDULE_FREQUENCY_MONTHLY
      },
      is_scheduled: {
        get() {
          return _.get(this.edit_pipe, 'schedule_status') == schedule.SCHEDULE_STATUS_ACTIVE ? true : false
        },
        set() {
          var status = this.is_scheduled ? schedule.SCHEDULE_STATUS_INACTIVE : schedule.SCHEDULE_STATUS_ACTIVE
          _.set(this.edit_pipe, 'schedule_status', status)
        }
      },
      show_times() {
        return _.includes([
          schedule.SCHEDULE_FREQUENCY_DAILY,
          schedule.SCHEDULE_FREQUENCY_WEEKLY,
          schedule.SCHEDULE_FREQUENCY_MONTHLY
        ], this.frequency)
      }
    },
    methods: {
      submit() {
        this.$emit('submit', this.edit_pipe)
      },
      reset(attrs) {
        if (_.isObject(attrs))
        {
          // we need to do a deep clone here since we have nested objects in the pipe
          // and Javascript objects keep their reference even when using _.assign()
          var p = _.cloneDeep(attrs)

          // pipes that have never been scheduled have a null schedule object
          if (_.isNil(_.get(p, 'schedule')))
            p = _.assign(p, defaultAttrs())

          this.edit_pipe = _.cloneDeep(p)
        }
         else
        {
          this.pipe = _.assign({}, defaultAttrs())
        }
      },
      updatePipe() {
        var edit_pipe = _.cloneDeep(this.pipe)
        var default_schedule = _.get(defaultAttrs(), 'schedule')

        if (_.isNil(_.get(edit_pipe, 'schedule')))
          _.set(edit_pipe, 'schedule', default_schedule)

        this.edit_pipe = edit_pipe
      },
      updateTime(item, index) {
        var times = _.get(this.edit_pipe, 'schedule.times', [])
        if (index >= times.length)
          return

        times[index] = item
        this.edit_pipe.schedule.times = [].concat(times)
      },
      addTime() {
        var times = _.get(this.edit_pipe, 'schedule.times', [])
        times.push({ hour: 8, minute: 0 })
        this.edit_pipe.schedule.times = [].concat(times)
      },
      deleteTime(item, index) {
        var times = _.get(this.edit_pipe, 'schedule.times', [])
        if (index >= times.length)
          return

        times = _.reject(times, (t, tindex) => { return tindex == index })
        this.edit_pipe.schedule.times = [].concat(times)
      }
    }
  }
</script>
