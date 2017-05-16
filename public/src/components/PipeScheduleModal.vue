<template>
  <ui-modal
    class="overflow-visible"
    ref="dialog"
    size="large"
    @hide="onHide"
  >
    <div slot="header" class="f4">Schedule '{{pipe.name}}'</div>

    <div class="flex flex-row items-center mb3">
      <toggle-button
        :checked="is_scheduled"
        @click.stop="toggleScheduled"
      ></toggle-button>
      <span
        class="f5 pl2 pointer"
        @click.stop="toggleScheduled"
      >{{is_scheduled ? 'Scheduled' : 'Not Scheduled'}}</span>
    </div>
    <div class="flex flex-row">
      <div class="flex-fill mr5">
        <value-select
          label="Frequency"
          :options="frequency_options"
          v-model="pipe.schedule.frequency"
        ></value-select>
      </div>
      <div class="flex-fill">
        <ui-select
          label="Timezone"
          has-search
          :options="timezone_options"
          v-model="pipe.schedule.timezone"
        ></ui-select>
      </div>
    </div>
    <div class="mb4" v-if="is_weekly">
      <value-select
        label="Run on the following days of the week"
        placeholder="Choose days of the week"
        multiple
        :options="day_options"
        v-model="pipe.schedule.days"
      ></value-select>
    </div>
    <div class="mb4" v-if="is_monthly">
      <value-select
        label="Run on the following days of the month"
        placeholder="Choose days of the month"
        multiple
        :options="month_options"
        v-model="pipe.schedule.days"
      ></value-select>
    </div>
    <div v-if="show_times">
      <div class="f8 mb3 css-input-label">Run at the following times</div>
      <time-chooser-list
        class="w-60 mb2"
        :times="pipe.schedule.times"
        @item-change="updateTime"
        @item-delete="deleteTime"
      ></time-chooser-list>
      <btn
        btn-sm
        btn-outline
        class="ttu b b--black-30 black-60"
        @click="addTime"
      >
        Add time
      </btn>
      </div>
    </div>

    <div slot="footer" class="flex flex-row items-end">
      <btn btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md class="b ttu blue" @click="submit()">Save Changes</btn>
    </div>
  </ui-modal>
</template>

<script>
  import { TIMEZONE_UTC } from '../constants/timezone'
  import { timezones } from '../constants/timezone'
  import * as schedule from '../constants/schedule'
  import Btn from './Btn.vue'
  import ToggleButton from './ToggleButton.vue'
  import ValueSelect from './ValueSelect.vue'
  import TimeChooserList from './TimeChooserList.vue'

  var day_options = [
    { label: 'Monday',    val: schedule.SCHEDULE_WEEK_DAY_MON },
    { label: 'Tuesday',   val: schedule.SCHEDULE_WEEK_DAY_TUE },
    { label: 'Wednesday', val: schedule.SCHEDULE_WEEK_DAY_WED },
    { label: 'Thursday',  val: schedule.SCHEDULE_WEEK_DAY_THU },
    { label: 'Friday',    val: schedule.SCHEDULE_WEEK_DAY_FRI },
    { label: 'Saturday',  val: schedule.SCHEDULE_WEEK_DAY_SAT },
    { label: 'Sunday',    val: schedule.SCHEDULE_WEEK_DAY_SUN }
  ]

  var month_options = [
    { label: 'First day',     val: schedule.SCHEDULE_MONTH_DAY_FIRST     },
    { label: 'Fifteenth day', val: schedule.SCHEDULE_MONTH_DAY_FIFTEENTH },
    { label: 'Last day',      val: schedule.SCHEDULE_MONTH_DAY_LAST      }
  ]

  var frequency_options = [
    //{ label: 'Every minute', val: schedule.SCHEDULE_FREQUENCY_ONE_MINUTE },
    { label: 'Every hour',   val: schedule.SCHEDULE_FREQUENCY_HOURLY     },
    { label: 'Every day',    val: schedule.SCHEDULE_FREQUENCY_DAILY      },
    { label: 'Every week',   val: schedule.SCHEDULE_FREQUENCY_WEEKLY     },
    { label: 'Every month',  val: schedule.SCHEDULE_FREQUENCY_MONTHLY    }
  ]

  const defaultAttrs = () => {
    return {
      schedule: {
        frequency: schedule.SCHEDULE_FREQUENCY_DAILY,
        timezone: TIMEZONE_UTC,
        days: [],
        times: [{ hour: 8, minute: 0 }]
      },
      schedule_status: ''
    }
  }

  export default {
    components: {
      Btn,
      ToggleButton,
      ValueSelect,
      TimeChooserList
    },
    data() {
      return {
        day_options: day_options,
        month_options: month_options,
        timezone_options: timezones,
        frequency_options: frequency_options,
        pipe: _.extend({}, defaultAttrs())
      }
    },
    computed: {
      is_weekly() {
        return this.getFrequency() == schedule.SCHEDULE_FREQUENCY_WEEKLY
      },
      is_monthly() {
        return this.getFrequency() == schedule.SCHEDULE_FREQUENCY_MONTHLY
      },
      show_times() {
        return _.includes([
          schedule.SCHEDULE_FREQUENCY_DAILY,
          schedule.SCHEDULE_FREQUENCY_WEEKLY,
          schedule.SCHEDULE_FREQUENCY_MONTHLY
        ], this.getFrequency())
      },
      is_scheduled() {
        return _.get(this.pipe, 'schedule_status') == schedule.SCHEDULE_STATUS_ACTIVE ? true : false
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
        this.$emit('submit', this.pipe, this)
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

          this.pipe = _.cloneDeep(p)
        }
         else
        {
          this.pipe = _.assign({}, defaultAttrs())
        }
      },
      getFrequency() {
        return _.get(this.pipe, 'schedule.frequency', schedule.SCHEDULE_FREQUENCY_DAILY)
      },
      toggleScheduled() {
        this.pipe.schedule_status = this.is_scheduled
          ? schedule.SCHEDULE_STATUS_INACTIVE
          : schedule.SCHEDULE_STATUS_ACTIVE
      },
      updateTime(item, index) {
        var times = _.get(this.pipe, 'schedule.times', [])
        if (index >= times.length)
          return

        times[index] = item
        this.pipe.schedule.times = [].concat(times)
      },
      addTime() {
        var times = _.get(this.pipe, 'schedule.times', [])
        times.push({ hour: 8, minute: 0 })
        this.pipe.schedule.times = [].concat(times)
      },
      deleteTime(item, index) {
        var times = _.get(this.pipe, 'schedule.times', [])
        if (index >= times.length)
          return

        times = _.reject(times, (t, tindex) => { return tindex == index })
        this.pipe.schedule.times = [].concat(times)
      },
      onHide() {
        this.$emit('hide', this)
      }
    }
  }
</script>

<style lang="less" scoped>
  // match Vue Material's label color
  .css-input-label {
    color: rgba(0, 0, 0, 0.54);
  }
</style>
