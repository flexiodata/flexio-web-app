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
              v-html="getStepCopy(step)"
              v-for="(step, index) in item1.steps"
            ></div>
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

  </div>
</template>

<script>
  import marked from 'marked'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'

  const item1 = require('json-loader!yaml-loader!../data/onboarding/copy-file-directory-to-cloud-storage.yml')

  export default {
    components: {
      OnboardingCodeEditor
    },
    data() {
      return {
        item1,
        active_idx: 0
      }
    },
    methods: {
      getStepCopy(step) {
        return marked(step.blurb.trim())
      },
      isStepActive(idx) {
        return idx <= this.active_idx
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

  .css-onboarding-box-inactive button:nth-child(n+3) {
    display:none;
  }
</style>
