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
          v-if="item.is_pro"
        >
          Pro
        </el-tag>
        <span
          class="ml2"
          v-if="item.key == 'schedule' && is_schedule_deployed"
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

    </el-checkbox-group>
  </div>
</template>

<script>
  export default {
    props: {
      isModeRun: {
        type: Boolean,
        required: true
      },
      deploymentItems: {
        type: Array
      },
      showSchedulePanel: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        deployment_options: [
          {
            key: 'manual',
            label: 'Run manually',
            always_on: true,
            is_pro: false
          },
          {
            key: 'api',
            label: 'Run this pipe from an API endpoint',
            always_on: false,
            is_pro: true
          },
          {
            key: 'schedule',
            label: 'Schedule the pipe to run at a set time',
            always_on: false,
            is_pro: false
          }
        ]
      }
    },
    computed: {
      checklist: {
        get() {
          return _.uniq(['manual'].concat(this.deploymentItems))
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
      }
    }
  }
</script>


schedule, api, trigger (or email input)
