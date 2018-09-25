<template>
  <div>
    <p class="mt0 ttu fw6 f7 moon-gray">How do you want to run this pipe?</p>
    <div
      class="bb b--black-05 pv3 ph4"
      :class="index == 0 ? 'bt' : ''"
      v-for="(item, index) in deployment_options"
    >
      <el-checkbox
      >
        {{item.label}}
      </el-checkbox>
      <el-tag
        class="ttu b ml1"
        size="mini"
        v-if="item.is_pro"
      >
        Pro
      </el-tag>
      <span
        class="ml1"
        v-if="item.key == 'schedule'"
      >
        <el-button
          type="text"
          style="padding: 0"
          @click="show_schedule = true"
        >
          Configure...
        </el-button>
      </span>

    </div>
  </div>
</template>

<script>
  export default {
    props: {
      showSchedule: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        deployment_options: [
          {
            key: 'manual',
            value: false,
            label: 'Run manually',
            always_on: true,
            is_pro: false
          },
          {
            key: 'api',
            value: false,
            label: 'Run this pipe from an API endpoint',
            always_on: false,
            is_pro: true
          },
          {
            key: 'schedule',
            value: false,
            label: 'Schedule the pipe to run at a set time',
            always_on: false,
            is_pro: false
          }
        ]
      }
    },
    computed: {
      show_schedule: {
        get() {
          return this.showSchedule
        },
        set(value) {
          this.$emit('update:showSchedule', value)
        }
      }
    }
  }
</script>


schedule, api, trigger (or email input)
