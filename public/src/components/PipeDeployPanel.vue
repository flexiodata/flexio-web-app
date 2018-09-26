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
        <el-button
          size="tiny"
          style="margin-left: 8px"
          @click="show_schedule = true"
          v-if="item.key == 'schedule' && is_schedule_deployed"
        >
          Configure...
        </el-button>

        <div
          class="mt2 pa2 br2 ba b--black-05 bg-nearer-white"
          style="margin-left: 24px"
          v-if="false && item.key == 'schedule' && is_schedule_deployed"
        >
          <span class="f6">Your pipe will run every five minutes.</span>
          <el-button
            type="text"
            style="padding: 0; margin-left: 8px"
            @click="show_schedule = true"
          >
            Configure...
          </el-button>
        </div>
      </div>
    </el-checkbox-group>
    <div class="mt3 pa4 tc bg-nearer-white">
      <h4 class="fw6">Turn this pipe on to deploy and run it.</h4>
      <div
        class="flex flex-row items-center justify-center"
        style="padding: 5px 5px 6px 10px; border-radius: 3px"
      >
        <span class="ttu f6 fw6">Your pipe is</span>
        <LabelSwitch
          class="dib ml2 hint--bottom-left"
          active-color="#13ce66"
          aria-label="Turn pipe on"
          v-model="is_pipe_mode_run"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import LabelSwitch from './LabelSwitch.vue'

  export default {
    props: {
      isModeRun: {
        type: Boolean,
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
            label: 'Schedule the pipe to run at a set time',
            always_on: false,
            is_pro: false
          },
          {
            key: 'api',
            label: 'Run this pipe from an API endpoint',
            always_on: false,
            is_pro: true
          },
          {
            key: 'manual',
            label: 'Run manually',
            always_on: false,
            is_pro: false
          }
        ]
      }
    },
    computed: {
      is_pipe_mode_run: {
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
      }
    }
  }
</script>
