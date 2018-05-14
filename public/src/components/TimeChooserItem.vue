<template>
  <div class="flex flex-row items-center darken-10 hide-child">
    <div class="flex-fill mr3">
      <value-select
        :options="hour_options"
        v-model="hour"
      ></value-select>
    </div>
    <div class="flex-fill mr3">
      <value-select
        :options="minute_options"
        v-model="minute"
      ></value-select>
    </div>
    <div class="flex-fill mr3">
      <ui-select
        :options="period_options"
        v-model="period"
      ></ui-select>
    </div>
    <div class="flex-none mh2">
      <div
        class="pointer f3 lh-solid ph1 b hint--top"
        aria-label="Delete"
        @click="onDeleteClick"
      >
        &times;
      </div>
    </div>
  </div>
</template>

<script>
  import ValueSelect from './ValueSelect.vue'

  export default {
    props: ['item', 'index'],
    components: {
      ValueSelect
    },
    data() {
      return {
        hour: 8,
        minute: 0,
        period: 'AM',
        hour_options: [
          { label: '1',  val: 1  },
          { label: '2',  val: 2  },
          { label: '3',  val: 3  },
          { label: '4',  val: 4  },
          { label: '5',  val: 5  },
          { label: '6',  val: 6  },
          { label: '7',  val: 7  },
          { label: '8',  val: 8  },
          { label: '9',  val: 9  },
          { label: '10', val: 10 },
          { label: '11', val: 11 },
          { label: '12', val: 0  }
        ],
        minute_options: [
          { label: '00', val: 0  },
          { label: '05', val: 5  },
          { label: '10', val: 10 },
          { label: '15', val: 15 },
          { label: '20', val: 20 },
          { label: '25', val: 25 },
          { label: '30', val: 30 },
          { label: '35', val: 35 },
          { label: '40', val: 40 },
          { label: '45', val: 45 },
          { label: '50', val: 50 },
          { label: '55', val: 55 }
        ],
        period_options: ['AM', 'PM']
      }
    },
    watch: {
      item(val, old_val) {
        this.$nextTick(() => { this.initFromItem() })
      },
      hour() {
        this.fireChangeEvent()
      },
      minute() {
        this.fireChangeEvent()
      },
      period() {
        this.fireChangeEvent()
      }
    },
    mounted() {
      this.initFromItem()
    },
    methods: {
      initFromItem() {
        var h = _.get(this.item, 'hour', 8)
        var m = _.get(this.item, 'minute', 0)

        this.hour = h % 12
        this.minute = m - (m % 5)
        this.period = h > 11 ? 'PM' : 'AM'
      },
      getTime() {
        var t = _.assign({}, this.$data)
        if (t.period == 'PM')
          t.hour += 12

        return _.pick(t, ['hour', 'minute'])
      },
      fireChangeEvent() {
        this.$emit('change', this.getTime(), this.index)
      },
      onDeleteClick() {
        this.$emit('delete', this.getTime(), this.index)
      }
    }
  }
</script>
