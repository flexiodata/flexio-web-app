<template>
  <div>
    <div class="tl pb3" v-show="!is_shown">
      <h3 class="fw6 f3 mid-gray mt0 mb2">Summary</h3>
    </div>
    <div class="tc" v-show="is_shown">
      <div class="dib mb2">
        <i class="el-icon-success v-mid dark-green f2"></i>
      </div>
      <h3 class="fw6 f3 mid-gray mt0 mb2">You're all set!</h3>
      <p class="mv4 f4">Your pipe is configured and ready to be run.</p>
      <div class="mt4">
        <el-button
          class="ttu b"
          type="primary"
          @click="runPipe"
        >
          Run now
        </el-button>
        <span class="ph2">or</span>
        <el-button
          class="ttu b"
          type="primary"
          @click="goDashboard"
        >
          View my dashboard
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { ROUTE_HOME_PIPES } from '../constants/route'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    computed: {
      ...mapState({
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      is_shown() {
        return this.index <= this.active_prompt_idx
      }
    },
    methods: {
      runPipe() {
        alert('Go to pipe builder and run it.')
      },
      goDashboard() {
        this.$router.push({ name: ROUTE_HOME_PIPES })
      }
    }
  }
</script>
