<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Learn to create data feeds in a few simple steps</div>
      <div class="mt4 mb3 f5">
        <span class="ma1">I want to</span>
        <select name="" class="pa1 ba b--black-10">
          <option value="a" selected>copy a file directory to cloud storage</option>
          <option value="b">collect data from an API</option>
          <option value="c">process and move tabular data</option>
        </select>
      </div>
      <div class="flex flex-column flex-row-l">
        <div class="flex-fill mr4-l">
          <div class="bg-white css-dashboard-box cf">
            <div
              class="ma3 pa4 f6 lh-copy bg-white ba b--black-10 pointer overflow-hidden marked css-onboarding-box"
              :class="isStepActive(index) ? '' : 'o-40 css-onboarding-box-inactive css-onboarding-box-hover'"
              @click="goStep(index)"
              v-for="(step, index) in item1.steps"
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
        <div class="flex-fill">
          <onboarding-code-editor
            cls="relative"
            :api-key="api_key"
            :sdk-options="sdk_options"
            :buttons="['run']"
            :code="item1.code"
          />
        </div>
      </div>
    </div>

    <!-- storage props modal -->
    <storage-props-modal
      ref="modal-storage-props"
      @submit="tryUpdateConnection"
      @hide="show_storage_props_modal = false"
      v-if="show_storage_props_modal"
    ></storage-props-modal>

  </div>
</template>

<script>
  import marked from 'marked'
  import { mapState, mapGetters } from 'vuex'
  import StoragePropsModal from './StoragePropsModal.vue'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'

  const item1 = require('json-loader!yaml-loader!../data/onboarding/copy-file-directory-to-cloud-storage.yml')
  const item2 = require('json-loader!yaml-loader!../data/onboarding/collect-data-from-api.yml')
  const item3 = require('json-loader!yaml-loader!../data/onboarding/process-tabular-data.yml')

  export default {
    components: {
      StoragePropsModal,
      OnboardingCodeEditor
    },
    data() {
      return {
        item1,
        active_idx: 0,
        show_storage_props_modal: false
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      api_key() {
        var tokens = this.getAllTokens()

        if (tokens.length == 0)
          return ''

        return _.get(tokens, '[0].access_code', '')
      },
      sdk_options() {
        return { baseUrl: 'https://' + window.location.hostname + '/api/v1' }
      }
    },
    mounted() {
      this.tryFetchTokens()
    },
    methods: {
      ...mapGetters([,
        'getAllTokens'
      ]),
      getStepCopy(step) {
        return marked(step.blurb.trim())
      },
      isStepActive(idx) {
        return idx == this.active_idx
      },
      goStep(idx) {
        this.active_idx = idx
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
            return
        }
      },
      tryUpdateConnection(a, b) {
        this.$nextTick(() => { this.$refs['modal-storage-props'].close() })
        this.goStep(this.active_idx + 1)
      },
      tryFetchTokens() {
        this.$store.dispatch('fetchUserTokens', { eid: this.active_user_eid })
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
