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
          <div
            class="ph3 mt2"
            v-if="item.key == 'deploy_schedule' && is_schedule_deployed"
          >
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

          <div
            class="ph3 mt3"
            v-if="item.key == 'deploy_api' && is_api_deployed"
          >
            <div v-if="false">
              <div class="ttu fw6 moon-gray">API Endpoint</div>
              <div>
                <span style="margin-right: 6px">{{api_endpoint_url}}</span>
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
            </div>

            <div v-if="api_key.length > 0">
              <div v-if="false">
                <div class="mt2 ttu fw6 moon-gray">API Key</div>
                <div>
                  <span style="margin-right: 6px">{{api_key}}</span>
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
              </div>

              <div class="mt2 ttu fw6 moon-gray">REST</div>
              <div>
                <span style="margin-right: 6px">{{example_http}}</span>
                <el-button
                  type="plain"
                  class="hint--top"
                  aria-label="Copy to Clipboard"
                  size="tiny"
                  :data-clipboard-text="example_http"
                >
                  Copy
                </el-button>
              </div>
              <div class="mt2 ttu fw6 moon-gray">cURL</div>
              <div>
                <span style="margin-right: 6px">{{example_curl}}</span>
                <el-button
                  type="plain"
                  class="hint--top"
                  aria-label="Copy to Clipboard"
                  size="tiny"
                  :data-clipboard-text="example_curl"
                >
                  Copy
                </el-button>
              </div>
            </div>
            <div v-else>
              <em class="fw4 moon-gray" style="margin-right: 6px">No API keys exist. An API key is required in order to run a pipe.</em>
              <el-button
                type="plain"
                size="tiny"
                @click="generateApiKey"
              >
                Generate API Key
              </el-button>
            </div>
          </div>

          <div
            class="ph3 mt3"
            v-if="item.key == 'deploy_ui' && is_web_deployed"
          >
            <div class="mt2 ttu fw6 moon-gray">Runtime URL</div>
            <div>
              <span style="margin-right: 6px">{{runtime_url}}</span>
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

              <div class="mv1">
                <el-button
                  class="ttu fw6"
                  type="primary"
                  size="tiny"
                  @click="show_runtime_configure = true"
                >
                  Edit web interface
                </el-button>
              </div>

          </div>

          <div
            class="ph3 mt2"
            v-if="item.key == 'deploy_email' && is_email_deployed"
          >
            <span style="margin-right: 6px">{{email_trigger}}</span>
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
              :data-clipboard-text="email_trigger"
            >
              Copy
            </el-button>
          </div>
        </div>
      </div>
    </el-checkbox-group>
    <div class="mt3 pv3 ph4 tc bg-nearer-white deploy-on-off-panel" v-if="false">
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
  import { mapGetters } from 'vuex'
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
        'deploy_email',
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
            key: 'deploy_api',
            label: 'Run using an API endpoint',
          },
          {
            key: 'deploy_schedule',
            label: 'Run on a schedule',
          },
          {
            key: 'deploy_email',
            label: 'Trigger from an email',
          }/*,
          {
            key: 'deploy_ui',
            label: 'Run from a Flex.io Web Interface',
          }*/
        ]
      }
    },
    computed: {
      api_key() {
        return this.getFirstToken()
      },
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
      is_email_deployed() {
        return _.includes(this.checklist, 'deploy_email')
      },
      schedule_str() {
        var schedule = _.get(this.pipe, 'schedule')
        return pipe_util.getDeployScheduleStr(schedule)
      },
      api_endpoint_url() {
        var identifier = pipe_util.getIdentifier(this.pipe)
        return pipe_util.getDeployApiUrl(identifier)
      },
      email_trigger() {
        var current_username = _.get(this.getActiveUser(), 'username').toLowerCase()
        var pipe_identifier = pipe_util.getIdentifier(this.pipe)
        return `${current_username}|${pipe_identifier}@pipes.flex.io`
      },
      runtime_url() {
        var eid = _.get(this.pipe, 'eid', '')
        return pipe_util.getDeployRuntimeUrl(eid)
      },
      example_http() {
        return this.api_endpoint_url + '?api_key=' + this.api_key
      },
      example_curl() {
        return 'curl -X POST "' + this.api_endpoint_url + '" --header "Authorization: Bearer ' + this.api_key + '"'
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser',
        'getFirstToken'
      ]),
      generateApiKey() {
        this.$store.dispatch('v2_action_createToken', {}).catch(error => {
          // TODO: add error handling?
        })
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .deploy-on-off-panel
    // match blue background and text color from hover menu item
    //color: rgba(102, 177, 255, 1)
    border: 1px solid rgba(102, 177, 255, 0.25)
    background: rgba(236, 245, 255, 0.75)

    // old values
    //border: 1px solid rgba(64, 158, 255, 0.14)
    //background-color: rgba(64, 158, 255, 0.07)
</style>
