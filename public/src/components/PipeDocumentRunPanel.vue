<template>
  <div>
    <div class="db mv3 pv1">
      <i class="el-icon-success v-mid f1" style="color: #13ce66"></i>
    </div>
    <h3 class="fw6 f3 mt3 mb4">Your pipe is working!</h3>
    <div class="mv4 mw7 center">
      <p class="mt0 lh-copy">Click the button below or use the following link to run your pipe in a browser.</p>
      <el-input
        :readonly="true"
        v-model="runtime_link"
      >
        <template slot="append">
          <el-button
            class="hint--top"
            aria-label="Copy to Clipboard"
            :data-clipboard-text="runtime_link"
          ><span class="ttu b">Copy</span></el-button>
        </template>
      </el-input>
      <p>
        <el-button
          type="primary"
          class="ttu b"
          @click="openPipeInNewWindow"
        >
          <span class="ph4">Run pipe</span>
        </el-button>
      </p>
    </div>
    <div class="bb b--black-05 mv4"></div>
    <div class="flex flex-row items-center justify-center">
      <span class="ttu f6 fw6">Your pipe is</span>
      <LabelSwitch
        class="dib ml2"
        active-color="#13ce66"
        v-model="is_pipe_mode_run"
      />
    </div>
    <p class="mt2 moon-gray f8 i">(you may turn this pipe off to edit it)</p>
  </div>
</template>

<script>
  import LabelSwitch from './LabelSwitch.vue'

  export default {
    props: {
      eid: {
        type: String,
        required: true
      },
      isModeRun: {
        type: Boolean,
        required: true
      }
    },
    components: {
      LabelSwitch
    },
    computed: {
      runtime_link() {
        console.log(window.location)
        return 'https://' + window.location.hostname + '/app/pipes/' + this.eid + '/run'
      },
      is_pipe_mode_run: {
        get() {
          return this.isModeRun
        },
        set(value) {
          this.$emit('update:isModeRun', value)
        }
      }
    },
    methods: {
      openPipeInNewWindow() {
        window.open(this.runtime_link, '_blank')
      }
    }
  }
</script>
