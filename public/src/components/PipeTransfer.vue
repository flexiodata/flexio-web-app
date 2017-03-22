<template>
  <div class="flex flex-row items-stretch bt b--black-10">
    <div class="flex-fill flex flex-column">
      <div class="f4 pa4 tc">1. Choose Input</div>
      <div v-if="has_input">Has Input</div>
      <div class="blankslate mh4" v-else>
        <div class="mv4">
          <btn
            btn-md
            btn-primary
            class="ttu b v-mid"
          >
            Choose a connection
          </btn>
        </div>
        <div class="mv4">
          <div class="v-mid dib bb b--black-10 w2"></div>
          <div class="v-mid dib black-30 f6 fw6 ttu">or</div>
          <div class="v-mid dib bb b--black-10 w2"></div>
        </div>
        <div class="mv4">
          <btn
            btn-md
            btn-primary
            class="ttu b v-mid"
          >
            Upload files
          </btn>
        </div>
        <div class="mv4">
          <div class="v-mid dib bb b--black-10 w2"></div>
          <div class="v-mid dib black-30 f6 fw6 ttu">or</div>
          <div class="v-mid dib bb b--black-10 w2"></div>
        </div>
        <div class="mv4">
          <div class="f6 fw6 ttu">Use the command line (CLI)</div>
          <p class="tl lh-copy mid-gray f6">The Flex.io Command Line Interface (CLI) is a tool for managing and running your Flex.io services from the command line. <a href="https://www.flex.io/docs/api/#command-line-interface" class="blue" target="_blank">Learn more...</a></p>
          <code>$ flexio pipes run pipe-alias file.txt</code>
        </div>
      </div>
    </div>
    <div class="flex-fill flex flex-column bl b--black-10">
      <div class="f4 pa4 tc">2. Choose Output</div>
      <div v-if="has_output">Has Output</div>
      <div class="blankslate mh4" v-else>
        <div class="mv4">
          <btn
            btn-md
            btn-primary
            class="ttu b v-mid"
          >
            Choose a connection
          </btn>
        </div>
        <div class="mv4">
          <div class="v-mid dib bb b--black-10 w2"></div>
          <div class="v-mid dib black-30 f6 fw6 ttu">or</div>
          <div class="v-mid dib bb b--black-10 w2"></div>
        </div>
        <div class="mv4">
          <div class="f6 fw6 ttu">Use the REST API</div>
          <p class="tl lh-copy mid-gray f6">Flex.io pipes can be run and have their results returned via an AJAX call to the a pipe's REST API endpoint. <a href="https://www.flex.io/docs/api/#pipes" class="blue" target="_blank">Learn more...</a></p>
          <code class="db tl pre">$.ajax({
  type: 'POST',
  url: 'https://www.flex.io/api/v1/pipes/pipe-alias/run?stream=0',
  success: function (coords) {
    ...
  }
})</code>
        </div>


      </div>
    </div>
    <div class="flex-fill">
      <div class="h-100 bl b--black-10">
        <div class="f4 pa4 tc">3. Choose Transformations</div>
        <div class="mh4">TODO; How do we want to expose these here?</div>
      </div>
    </div>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import Btn from './Btn.vue'

  export default {
    props: ['tasks', 'active-subprocess'],
    components: {
      Btn
    },
    computed: {
      has_input() {
        return _.find(this.tasks, { type: TASK_TYPE_INPUT })
      },
      has_output() {
        return _.find(this.tasks, { type: TASK_TYPE_OUTPUT })
      }
    }
  }
</script>
