<template>
  <div class="flex flex-row items-center pa1 br1 darken-05 hide-child">
    <div class="flex-fill mr2">
      <el-select
        size="mini"
        v-model="hour"
      >
        <el-option
          :label="option.label"
          :value="option.val"
          :key="option.val"
          v-for="option in hour_options"
        />
      </el-select>
    </div>
    <div class="flex-fill mr2">
      <el-select
        size="mini"
        v-model="minute"
      >
        <el-option
          :label="option.label"
          :value="option.val"
          :key="option.val"
          v-for="option in minute_options"
        />
      </el-select>
    </div>
    <div class="flex-fill mr2">
      <el-select
        size="mini"
        v-model="period"
      >
        <el-option
          :label="option.label"
          :value="option.val"
          :key="option.val"
          v-for="option in period_options"
        />
      </el-select>
    </div>
    <div
      class="pointer f3 black-30 hover-black-60 ph2 hint--top"
      aria-label="Delete"
      :class="index == 0 ? 'invisible' : ''"
      @click="onDeleteClick"
    >
      &times;
    </div>
  </div>
</template>

<script>
  export default {
    props: ['item', 'index'],
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
        period_options: [
          { label: 'AM', val: 'AM'  },
          { label: 'PM', val: 'PM'  }
        ]
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
