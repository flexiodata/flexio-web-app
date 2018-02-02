<template>
  <div>
    <div class="flex flex-column flex-row-l">
      <div class="flex-fill mr4-l order-1 order-0-l">
        <div class="bg-white css-dashboard-box cf">
          <div
            class="ma3 pa4 f6 lh-copy bg-white ba b--black-10 overflow-hidden marked css-onboarding-box"
            :class="isStepActive(index) ? '' : 'o-40 pointer css-onboarding-box-inactive css-onboarding-box-hover'"
            @click="goStep(index)"
            v-for="(step, index) in all_steps"
          >
            <div v-html="getStepCopy(step)"></div>
            <button
              type="button"
              class="link dib blue underline-hover db ttu fw6 pa0 mt4"
              @click.stop="doStepAction(step.button.action, index)"
              v-if="step.button"
            >
              <span class="v-mid">{{step.button.label}}</span>
            </button>
          </div>
        </div>
      </div>
      <div class="flex-fill mb3 order-0 order-1-l">
        <onboarding-code-editor
          cls="relative"
          :api-key="apiKey"
          :sdk-options="sdkOptions"
          :buttons="['run']"
          :code="item.code"
        />
      </div>
    </div>

    <!-- pipe modal -->
    <pipe-props-modal
      ref="modal-pipe-props"
      @submit="tryCreatePipe"
      @hide="show_pipe_props_modal = false"
      v-if="show_pipe_props_modal"
    ></pipe-props-modal>

    <!-- storage props modal -->
    <storage-props-modal
      ref="modal-storage-props"
      @submit="tryUpdateConnection"
      @hide="show_storage_props_modal = false"
      v-if="show_storage_props_modal"
    ></storage-props-modal>

    <ui-modal
      ref="deploy-dialog"
      :title="'Save & Deploy'"
      @hide="show_deploy_modal = false"
      v-if="show_deploy_modal"
    >
      <div class="relative w-100 tc">
        <div class="f2 pt4 mbv">Welcome to Flex.io</div>
        <div class="mv3 f5 fw6 black-60 lh-copy mw7 center">Below are three simple templates to show how pipes work. Click on one below to get started, and then you can modify them with your own inputs and commands.</div>
      </div>
    </ui-modal>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import StoragePropsModal from './StoragePropsModal.vue'
  import PipePropsModal from './PipePropsModal.vue'
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
      }
    },
    components: {
      StoragePropsModal,
      PipePropsModal,
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
        show_storage_props_modal: false,
        show_pipe_props_modal: false,
        show_deploy_modal: false,
        connection: {},
        pipe: {}
      }
    },
    computed: {
      active_username() {
        return _.get(this.getActiveUser(), 'user_name', '')
      },
      all_steps() {
        var final_step = {
          button: {
            label: 'Save & Deploy',
            action: 'save'
          },
          blurb: `
### Step 5. Save and Deploy

You should now have a functioning pipe. Next, click the "Save & Deploy" button. This will save the pipe to your account and will provide you with external deployment options, such as a cURL call.  Alternatively, you can go to your pipe list and schedule the pipe to run using the drop-down menu.

If you have any questions, please send us a note using the chat button at the bottom right of the screen; were more than happy to help! Thanks.
`
        }

        var arr = [].concat(this.item.steps).concat([final_step])
        return _.compact(arr)
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
            this.show_storage_props_modal = true
            this.$nextTick(() => { this.$refs['modal-storage-props'].open() })
            analytics.track('Clicked `Connect to Storage` button in Onboarding')
            return
          case 'save':
            var attrs = {
              name: _.get(this.item, 'name', ''),
              ename: _.get(this.item, 'id', '')
            }

            // add username as the alias prefix
            attrs.ename = _.kebabCase(this.active_username + '-' + attrs.ename)

            this.show_pipe_props_modal = true
            this.$nextTick(() => { this.$refs['modal-pipe-props'].open(attrs) })
            analytics.track('Clicked `Save & Deploy` button in Onboarding')
            return
        }
      },
      showDeployModal() {
        this.show_deploy_modal = true
        this.$nextTick(() => { this.$refs['deploy-dialog'].open() })
      },
      tryUpdateConnection(attrs, modal) {
        var eid = attrs.eid
        var ctype = _.get(attrs, 'connection_type', '')
        var is_pending = _.get(attrs, 'eid_status', '') === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'ename', 'description', 'connection_info'])
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
              var analytics_payload = _.pick(attrs, ['name', 'ename', 'description'])
              _.set(analytics_payload, 'eid', eid)
              _.set(analytics_payload, 'connection_type', ctype)
              analytics.track('Created Connection in Onboarding', analytics_payload)
            }

            if (!_.isNil(modal))
              modal.close()

            this.connection = _.assign({}, connection)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryCreatePipe(attrs, modal) {
        if (!_.isObject(attrs))
          attrs = { name: 'Untitled Pipe' }

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            var pipe = response.body
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'ename'])

            // add Segment-friendly keys
            _.assign(analytics_payload, {
              createdAt: _.get(pipe, 'created')
            })

            analytics.track('Created Pipe in Onboarding', analytics_payload)

            if (!_.isNil(modal))
              modal.close()

            this.pipe = _.assign({}, pipe)
            this.showDeployModal()
          }
           else
          {
            analytics.track('Created Pipe in Onboarding (Error)')
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
