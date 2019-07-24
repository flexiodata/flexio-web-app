<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title">Schedule '{{pipe.short_description}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <!-- use <el-form> classes for consistent spacing -->
    <div class="el-form el-form--cozy el-form__label-tiny">
      <div
        class="el-form-item flex flex-row items-center"
        v-if="showToggle"
      >
        <el-switch
          class="hint--bottom"
          :aria-label="is_scheduled ? 'Scheduled' : 'Not Scheduled'"
          v-model="is_scheduled"
        />
        <span
          class="f5 pl2 pointer"
          @click.stop="is_scheduled = !is_scheduled"
        >
          <transition name="el-zoom-in-center" mode="out-in">
            <span v-bind:key="is_scheduled">
              {{is_scheduled ? 'Scheduled' : 'Not Scheduled'}}
            </span>
          </transition>
        </span>
      </div>
      <div class="el-form-item flex flex-row">
        <div class="flex-fill mr3">
          <label class="el-form-item__label">Frequency</label>
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
          <label class="el-form-item__label">Timezone</label>
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
      <div class="el-form-item" v-if="is_weekly">
        <label class="el-form-item__label">Run on the following days of the week</label>
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
      <div class="el-form-item" v-if="is_monthly">
        <label class="el-form-item__label">Run on the following days of the month</label>
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
      <div class="el-form-item" style="width: 270px" v-if="show_times">
        <label class="el-form-item__label" v-if="is_weekly || is_monthly">Run at the following time</label>
        <label class="el-form-item__label" v-else>Run at the following times</label>
        <time-chooser-list
          class="mb1 nl1"
          :times="edit_pipe.schedule.times"
          @item-change="updateTime"
          @item-delete="deleteTime"
        />
        <div
          class="ph1 nl1"
          v-if="!is_weekly && !is_monthly"
        >
          <el-button
            size="mini"
            class="w-100 ttu fw6"
            @click="addTime"
          >
            Add time
          </el-button>
        </div>
      </div>
    </div>

    <div class="mt4 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button
        class="ttu fw6"
        @click="onCancel"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu fw6"
        type="primary"
        @click="onSubmit"
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
  import TimeChooserList from '@/components/TimeChooserList'

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
      schedule: _.cloneDeep(schedule.SCHEDULE_DEFAULTS),
      deploy_schedule: schedule.SCHEDULE_STATUS_INACTIVE
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
      showToggle: {
        type: Boolean,
        default: false
      },
      pipe: {
        type: Object,
        default: () => {}
      }
    },
    components: {
      TimeChooserList
    },
    watch: {
      pipe: {
        handler: 'initPipe',
        immediate: true,
        deep: true
      },
      edit_pipe: {
        handler: 'updatePipe',
        deep: true
      },
      'edit_pipe.schedule.frequency'(val) {
        _.set(this.edit_pipe, 'schedule.days', [])

        // for weekly or monthly schedules...
        if (val == schedule.SCHEDULE_FREQUENCY_WEEKLY ||
            val == schedule.SCHEDULE_FREQUENCY_MONTHLY) {
          // ...only keep the first time
          var times = _.take(_.get(this.edit_pipe, 'schedule.times', []))
          _.set(this.edit_pipe, 'schedule.times', times)
        }
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
          return _.get(this.edit_pipe, 'deploy_schedule') == schedule.SCHEDULE_STATUS_ACTIVE ? true : false
        },
        set() {
          var status = this.is_scheduled ? schedule.SCHEDULE_STATUS_INACTIVE : schedule.SCHEDULE_STATUS_ACTIVE
          this.edit_pipe = _.assign({}, this.edit_pipe, { 'deploy_schedule': status })
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
      onClose() {
        this.initPipe()
        this.$emit('close')
      },
      onCancel() {
        this.initPipe()
        this.$emit('cancel')
      },
      onSubmit() {
        this.$emit('submit', this.edit_pipe)
      },
      initPipe() {
        var edit_pipe = _.cloneDeep(this.pipe)

        if (_.isNil(_.get(edit_pipe, 'schedule'))) {
          _.set(edit_pipe, 'schedule', _.get(defaultAttrs(), 'schedule'))
          edit_pipe.deploy_schedule = SCHEDULE_STATUS_INACTIVE
        }

        this.edit_pipe = edit_pipe
      },
      updatePipe() {
        this.$emit('change', this.edit_pipe)
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

<style lang="stylus" scoped>
  .el-form-item__label
    display: inline-block
    float: none
    text-align: left
</style>
