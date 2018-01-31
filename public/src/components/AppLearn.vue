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
              class="pa4 ma3 f6 lh-copy bg-nearer-white overflow-hidden css-onboarding-box"
              style="box-shadow: 0 1px 4px -2px rgba(0,0,0,0.4)"
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

  const item1 = require('json-loader!yaml-loader!../data/onboarding/copy-and-process-files-from-cloud-storage.yml')

  export default {
    components: {
      OnboardingCodeEditor
    },
    data() {
      return {
        item1
      }
    },
    methods: {
      getStepCopy(step) {
        return marked(step.blurb.trim())
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
</style>
