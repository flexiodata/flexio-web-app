<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Build your first pipe</div>
      <div class="mv4 f4">
        <span class="ma1">I want to</span>
        <select name="" class="pa1 b--black-10">
          <option value="a">convert a CSV to JSON</option>
          <option value="b">filter a JSON file from the web</option>
          <option value="c" selected>copy and process files from cloud storage</option>
        </select>
      </div>
      <div class="flex flex-column flex-row-l">
        <div class="flex-fill mr4-l">
          <div
            class="pa3 mb3 f6 lh-copy bg-white ba b--black-10 css-onboarding-box"
            v-html="getStepCopy(step)"
            v-for="(step, index) in item1.steps"
          ></div>
        </div>
        <div class="flex-fill">
          <onboarding-code-editor
            cls="relative pa3 bg-black-05 br2"
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
  .css-onboarding-box h4 {
    margin-top: 0;
  }

  .css-onboarding-box p:last-child {
    margin-bottom: 0;
  }
</style>
