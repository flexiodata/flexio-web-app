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
  import { mapState, mapGetters } from 'vuex'
  import { ROUTE_HOME_PIPES } from '../constants/route'
  import Flexio from 'flexio-sdk-js'

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
    watch: {
      is_active() {
        if (this.is_active) {
          this.createPipe()
        }
      }
    },
    computed: {
      ...mapState({
        def: state => state.builder.def,
        active_prompt_idx: state => state.builder.active_prompt_idx,
        code: state => state.builder.code
      }),
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_shown() {
        return this.index <= this.active_prompt_idx
      },
      api_key() {
        var tokens = this.getAllTokens()

        if (tokens.length == 0)
          return ''

        return _.get(tokens, '[0].access_code', '')
      },
      sdk_options() {
        if (window.location.hostname == 'www.flex.io')
          return {}

        return { baseUrl: 'https://' + window.location.host + '/api/v2' }
      },
      save_code() {
        var name = _.get(this.def, 'title', 'Untitled Pipe')
        return this.code + '.save({ name: "' + name + '" })'
      }
    },
    methods: {
      ...mapGetters([
        'getAllTokens'
      ]),
      runPipe() {
        alert('Go to pipe builder and run it.')
      },
      goDashboard() {
        this.$router.push({ name: ROUTE_HOME_PIPES })
      },
      createPipe() {
        var pipe_fn = (Flexio, callback) => {
          eval(this.save_code)
        }

        Flexio.setup(this.api_key, this.sdk_options)

        pipe_fn.call(this, Flexio, (err, response) => {
          // TODO: error reporting?
        })
      }
    }
  }
</script>
