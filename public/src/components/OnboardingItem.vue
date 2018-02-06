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
          ref="code"
          cls="relative"
          :api-key="apiKey"
          :sdk-options="sdkOptions"
          :buttons="['save', 'run']"
          :code="edit_item.code"
          @save="showPipePropsModal"
        />
      </div>
    </div>

    <!-- pipe modal -->
    <pipe-props-modal
      title="Save Pipe"
      ref="modal-pipe-props"
      @submit="tryCreatePipe"
      @hide="show_pipe_props_modal = false"
      v-if="show_pipe_props_modal"
    ></pipe-props-modal>

    <!-- storage props modal -->
    <storage-props-modal
      title="Connect to Storage"
      ref="modal-storage-props"
      @submit="tryUpdateConnection"
      @hide="show_storage_props_modal = false"
      v-if="show_storage_props_modal"
    ></storage-props-modal>

    <ui-modal
      size="large"
      ref="deploy-dialog"
      :remove-header="true"
      @hide="show_deploy_modal = false"
      v-if="show_deploy_modal"
    >
      <div class="lh-copy cf">
        <div class="fr">
          <div class="ui-modal__close-button" @click="$refs['deploy-dialog'].close()"><button aria-label="Close" type="button" class="ui-close-button ui-close-button--size-normal ui-close-button--color-black"><div class="ui-close-button__icon"><span class="ui-icon material-icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18.984 6.422L13.406 12l5.578 5.578-1.406 1.406L12 13.406l-5.578 5.578-1.406-1.406L10.594 12 5.016 6.422l1.406-1.406L12 10.594l5.578-5.578z"></path></svg></span></div> <span class="ui-close-button__focus-ring"></span> <div class="ui-ripple-ink"></div></button></div>
        </div>

        <h2 class="flex flex-row items-center f3 mt0"><i class="material-icons v-mid dark-green mr2">check_circle</i> Success!</h2>

        <p>The <strong>{{pipe_name}}</strong> pipe has been added to your account.</p>
        <p>To deploy your pipe in the wild, try one of these options:</p>

        <div class="ml3">
          <h4 class="mb2">cURL:</h4>
          <div class="marked">
            <code class="db">curl -s -X POST 'https://www.flex.io/api/v1/pipes/{{pipe_alias}}' -H 'Authorization: Bearer {{apiKey}}'</code>
          </div>

          <h4 class="mb2">http:</h4>
          <div class="marked">
            <pre><code>$.ajax({
  type: 'POST',
  url: 'http://www.flex.io/api/v1/pipes/{{pipe_alias}}',
  beforeSend: function(xhr) {
    xhr.setRequestHeader('Authorization', 'Bearer {{apiKey}}')
  }
})</code></pre>
          </div>
          <h4 class="mb2">CRON:</h4>
          <p class="mt0">If you want to stay in-app, you may schedule your pipe to run as desired from the drop-down menu in the pipe list.</p>
        </div>
        <hr class="center w-30 mv4 bb-0 b--black-10">
        <p>If you have any questions about deployment, please send us a note using the chat button at the bottom right of the screen; we're more than happy to help! Thanks.</p>
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
        connection_alias: 'home',
        pipe_alias: '',
        pipe_name: ''
      }
    },
    computed: {
      active_username() {
        return _.get(this.getActiveUser(), 'user_name', '')
      },
      edit_item() {
        var item = _.assign({
          code: '',
          steps: []
        }, this.item)

        item = _.cloneDeep(item)

        item.code = item.code.replace('{username}', this.active_username)

        _.each(item.steps, (step) => {
          step.blurb = step.blurb.replace(/{connection_alias}/g, this.connection_alias)
          step.blurb = step.blurb.replace(/{pipe_alias}/g, this.pipe_alias)
          step.blurb = step.blurb.replace(/{username}/g, this.active_username || '')
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

You should now have a functioning pipe. Next, click the "Save & Deploy" button. This will save the pipe to your account and will provide you with external deployment options, such as a cURL call.  Alternatively, you can go to your pipe list and schedule the pipe to run using the drop-down menu.

If you have any questions, please send us a note using the chat button at the bottom right of the screen; we're more than happy to help! Thanks.
`
        }

        var arr = [].concat(this.edit_item.steps).concat([final_step])
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
            this.showStoragePropsModal()
            return
          case 'save':
            this.showPipePropsModal()
            return
        }
      },
      showStoragePropsModal() {
        this.show_storage_props_modal = true
        this.$nextTick(() => { this.$refs['modal-storage-props'].open() })
        analytics.track('Clicked `Connect to Storage` button in Onboarding')
      },
      showPipePropsModal() {
        var attrs = {
          name: _.get(this.edit_item, 'name', ''),
          ename: _.get(this.edit_item, 'id', '')
        }

        // add username as the alias prefix
        attrs.ename = _.kebabCase(this.active_username + '-' + attrs.ename)

        this.show_pipe_props_modal = true
        this.$nextTick(() => { this.$refs['modal-pipe-props'].open(attrs) })
        analytics.track('Clicked `Save & Deploy` button in Onboarding')
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

            this.connection_alias = _.get(connection, 'ename', '')

            this.goStep(this.active_step + 1)
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

        var task = this.$refs['code'].getTaskJSON()
        _.assign(attrs, { task })

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

            this.pipe_name = _.get(pipe, 'name', '')
            this.pipe_alias = _.get(pipe, 'ename', '')
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
