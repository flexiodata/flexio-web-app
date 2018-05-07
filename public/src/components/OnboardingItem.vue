<template>
  <div>
    <div class="flex flex-column flex-row-l">
      <div class="flex-fill mr4-l order-1 order-0-l">
        <div class="bg-white css-dashboard-box cf">
          <div
            class="ma3 pa4 f6 lh-copy bg-white ba overflow-hidden marked css-onboarding-box"
            :class="isStepActive(index) ? 'bw1 b--blue' : 'b--black-20 o-40 pointer css-onboarding-box-inactive css-onboarding-box-hover'"
            @click="goStep(index)"
            v-for="(step, index) in all_steps"
          >
            <div v-html="getStepCopy(step)"></div>
            <button
              type="button"
              class="link dib blue underline-hover ttu fw6 pa0 mt4"
              @click.stop="doStepAction(step.button.action, index)"
              v-if="step.button"
            >
              <span class="v-mid">{{step.button.label}}</span>
            </button>
          </div>
        </div>
      </div>
      <div class="flex-fill mb3 order-0 order-1-l">
        <OnboardingCodeEditor
          ref="code"
          cls="relative"
          :title="item.name"
          :api-key="apiKey"
          :sdk-options="sdkOptions"
          :buttons="['save', 'run']"
          :code="edit_item.code"
          @save="showPipeSaveDialog"
        />
      </div>
    </div>

    <!-- connect to storage dialog -->
    <el-dialog
      custom-class="no-header no-footer"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_connection_new_dialog"
    >
      <connection-edit-panel
        @close="show_connection_new_dialog = false"
        @cancel="show_connection_new_dialog = false"
        @submit="tryUpdateConnection"
        v-if="show_connection_new_dialog"
      />
    </el-dialog>

    <!-- pipe save dialog -->
    <el-dialog
      custom-class="no-header no-footer"
      width="36rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_save_dialog"
    >
      <pipe-edit-panel
        title="Save Pipe"
        :pipe="pipe_attrs"
        @close="show_pipe_save_dialog = false"
        @cancel="show_pipe_save_dialog = false"
        @submit="tryCreatePipe"
        v-if="show_pipe_save_dialog"
      />
    </el-dialog>

    <!-- pipe deploy dialog -->
    <el-dialog
      custom-class="no-header no-footer"
      width="56rem"
      top="8vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :visible.sync="show_pipe_deploy_dialog"
    >
      <pipe-deploy-panel
        :pipe="pipe"
        :is-onboarding="true"
        @close="show_pipe_deploy_dialog = false"
      />
    </el-dialog>

  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'
  import PipeEditPanel from './PipeEditPanel.vue'
  import PipeDeployPanel from './PipeDeployPanel.vue'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'

  export default {
    props: {
      'item': {
        type: Object,
        default: () => { return {} }
      },
      'api-key': {
        type: String,
        default: ''
      },
      'sdk-options': {
        type: Object,
        default: () => { return {} }
      },
      'username': {
        type: String,
        required: true
      }
    },
    components: {
      ConnectionEditPanel,
      PipeEditPanel,
      PipeDeployPanel,
      OnboardingCodeEditor
    },
    watch: {
      item() {
        this.active_step = 0
      }
    },
    data() {
      return {
        active_step: 0,
        show_connection_new_dialog: false,
        show_pipe_save_dialog: false,
        show_pipe_deploy_dialog: false,
        connection_alias: 'home',
        pipe: {},
        pipe_alias: '',
        pipe_name: ''
      }
    },
    computed: {
      edit_item() {
        var item = _.assign({
          code: '',
          steps: []
        }, this.item)

        item = _.cloneDeep(item)

        item.code = item.code.replace('{{username}}', this.username)

        _.each(item.steps, (step) => {
          step.blurb = step.blurb.replace(/{{connection_alias}}/g, this.connection_alias)
          step.blurb = step.blurb.replace(/{{pipe_alias}}/g, this.pipe_alias)
          step.blurb = step.blurb.replace(/{{username}}/g, this.username || '')
        })

        return item
      },
      all_steps() {
        var final_step = {
          button: {
            label: 'Save & Deploy',
            action: 'save'
          },
          blurb: `
### Step 5. Save and deploy

You should now have a functioning pipe. Next, click the "Save & Deploy" button. This will save the pipe to your account and will provide you with external deployment options, such as a cURL call or scheduling.

If you have any questions, please send us a note using the chat button at the bottom right of the screen; we're more than happy to help! Thanks.
`
        }

        var arr = [].concat(this.edit_item.steps).concat([final_step])
        return _.compact(arr)
      },
      pipe_attrs() {
        var attrs = {
          name: _.get(this.edit_item, 'name', ''),
          alias: _.get(this.edit_item, 'id', ''),
          description: ''
        }

        // clean up alias
        attrs.alias = attrs.alias.trim()
        attrs.alias = attrs.alias.toLowerCase().replace(/\s/g, '-')

        return attrs
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      getStepCopy(step) {
        return marked(step.blurb.trim())
      },
      isStepActive(idx) {
        return idx == this.active_step
      },
      goStep(idx) {
        this.active_step = idx
        /*
        // scroll back to the top of the pipe list when the process starts
        this.$scrollTo('#'+this.pipeEid, {
          container: '#'+this.pipeEid,
          duration: 400,
          easing: 'ease-out'
        })
        */
      },
      doStepAction(action, idx) {
        switch (action)
        {
          case 'next':
            this.goStep(idx + 1)
            return
          case 'storage':
            this.showNewConnectionDialog()
            return
          case 'save':
            this.showPipeSaveDialog()
            return
        }
      },
      showNewConnectionDialog() {
        var edit_code = this.$refs['code'].getEditCode()

        this.show_connection_new_dialog = true
        this.$store.track('Clicked `Create Connection` Button In Onboarding', {
          title: this.item.name,
          code: edit_code
        })
      },
      showPipeSaveDialog() {
        var edit_code = this.$refs['code'].getEditCode()

        this.show_pipe_save_dialog = true
        this.$store.track('Clicked `Save & Deploy` Button In Onboarding', {
          title: this.item.name,
          code: edit_code
        })
      },
      showPipeDeployDialog() {
        this.show_pipe_deploy_dialog = true
      },
      tryUpdateConnection(attrs) {
        var eid = attrs.eid
        var ctype = _.get(attrs, 'connection_type', '')
        var is_pending = _.get(attrs, 'eid_status', '') === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'alias', 'description', 'connection_info'])
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            var connection = response.body

            // try to connect to the connection
            this.$store.dispatch('testConnection', { eid, attrs })

            if (is_pending)
            {
              var analytics_payload = _.pick(attrs, ['eid', 'name', 'alias', 'description', 'connection_type'])
              this.$store.track('Created Connection In Onboarding', analytics_payload)
            }

            this.connection_alias = _.get(connection, 'alias', '')

            this.show_connection_new_dialog = false

            this.goStep(this.active_step + 1)
          }
           else
          {
              this.$store.track('Created Connection In Onboarding (Error)')
          }
        })
      },
      tryCreatePipe(attrs) {
        if (!_.isObject(attrs))
          attrs = { name: 'Untitled Pipe' }

        var task = this.$refs['code'].getTaskJSON()
        _.assign(attrs, { task })

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            var pipe = response.body
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'alias', 'created'])

            this.$store.track('Created Pipe In Onboarding', analytics_payload)

            this.pipe_name = _.get(pipe, 'name', '')
            this.pipe_alias = _.get(pipe, 'alias', '')
            this.pipe = _.cloneDeep(pipe)

            this.$nextTick(() => { this.show_pipe_save_dialog = false })
            this.show_pipe_deploy_dialog = true
          }
           else
          {
            this.$store.track('Created Pipe In Onboarding (Error)')
          }
        })
      }
    }
  }
</script>

<style>
  .css-onboarding-box h3 {
    margin-top: 0;
  }

  .css-onboarding-box p:last-child {
    margin-bottom: 0;
  }

  .css-onboarding-box-hover:hover {
    border-color: rgba(0,0,0,0.2);
    box-shadow: 0 0 12px rgba(0,0,0,0.3);
  }

  .css-onboarding-box-inactive p:nth-child(n+3) {
    display:none;
  }

  .css-onboarding-box-inactive pre:nth-child(n+3) {
    display:none;
  }

  .css-onboarding-box-inactive button {
    display:none;
  }
</style>
