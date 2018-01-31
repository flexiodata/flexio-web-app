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
          <div class="bg-black-05 css-dashboard-box cf">
            <div
              class="pa4 ma3 f6 lh-copy bg-white ba b--black-10 overflow-hidden marked css-onboarding-box"
              :class="isStepActive(index) ? '' : 'o-40 no-pointer-events css-onboarding-box-inactive'"
              v-for="(step, index) in item1.steps"
            >
              <div v-html="getStepCopy(step)"></div>
              <button type="button" class="link dib blue underline-hover db ttu fw6 pa0 mt4" @click="doStepAction(step, index)">
                <span class="v-mid">{{step.button.label}}</span>
              </button>
            </div>
          </div>
        </div>
        <div class="flex-fill">
          <onboarding-code-editor
            cls="relative"
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
  import StoragePropsModal from './StoragePropsModal.vue'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'

  const item1 = require('json-loader!yaml-loader!../data/onboarding/copy-file-directory-to-cloud-storage.yml')

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
    methods: {
      getStepCopy(step) {
        return marked(step.blurb.trim())
      },
      isStepActive(idx) {
        return idx <= this.active_idx
      },
      doStepAction(step, idx) {
        switch (step.button.action)
        {
          case 'next':
            this.active_idx = idx + 1
            return
          case 'storage':
            this.show_storage_props_modal = true
            this.$nextTick(() => { this.$refs['modal-storage-props'].open() })
            return
        }
      },
      tryUpdateConnection(a, b) {
        this.$nextTick(() => { this.$refs['modal-storage-props'].close() })
        this.active_idx += 1
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
