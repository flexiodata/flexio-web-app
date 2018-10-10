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
          <div v-if="item.key == 'deploy_schedule' && is_schedule_deployed">
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
          <div v-if="item.key == 'deploy_api' && is_api_deployed">
            <span style="margin-right: 6px"><span class="b">POST</span> {{api_endpoint_url}}</span>
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
              :data-clipboard-text="api_endpoint_url"
            >
              Copy
            </el-button>
          </div>
          <div v-if="item.key == 'deploy_ui' && is_web_deployed">
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
          class="dib ml2 hint--bottom"
          active-color="#13ce66"
          :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
          :width="58"
          v-model="is_deployed"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import pipe_util from '../utils/pipe'
  import * as sched from '../constants/schedule'
  import LabelSwitch from './LabelSwitch.vue'

  const ACTIVE = 'A'
  const INACTIVE = 'I'

  export default {
    props: {
      isModeRun: {
        type: Boolean,
        required: true
      },
      /*
        'eid'
        'pipe_identifier'
        'deploy_mode',
        'deploy_schedule',
        'deploy_api',
        'deploy_ui',
        'schedule'
      */
      pipe: {
        type: Object,
        required: true
      },
      showPropertiesPanel: {
        type: Boolean,
        default: false
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
            key: 'deploy_schedule',
            label: 'Run on a schedule',
          },
          {
            key: 'deploy_api',
            label: 'Run using an API endpoint',
          },
          {
            key: 'deploy_ui',
            label: 'Run using a Flex.io Web Interface',
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
      checklist: {
        get() {
          var arr = _.map(this.deployment_options, (d) => {
            return _.get(this.pipe, [d.key], INACTIVE) === ACTIVE ? d.key : false
          })
          return _.compact(arr)
        },
        set(value) {
          var obj = {}
          _.each(this.deployment_options, (d) => {
            obj[d.key] = _.includes(value, d.key) ? ACTIVE : INACTIVE
          })
          this.$emit('updated-deployment', obj)
        }
      },
      is_schedule_deployed() {
        return _.includes(this.checklist, 'deploy_schedule')
      },
      is_api_deployed() {
        return _.includes(this.checklist, 'deploy_api')
      },
      is_web_deployed() {
        return _.includes(this.checklist, 'deploy_ui')
      },
      schedule_str() {
        var schedule = _.get(this.pipe, 'schedule')
        return pipe_util.getDeployScheduleStr(schedule)
      },
      api_endpoint_url() {
        var identifier = pipe_util.getIdentifier(this.pipe)
        return pipe_util.getDeployApiUrl(identifier)
      },
      runtime_url() {
        var eid = _.get(this.pipe, 'eid', '')
        return pipe_util.getDeployRuntimeUrl(eid)
      }
    }
  }
</script>
