<template>
  <div class="flex flex-row items-center justify-center">
    <div v-if="is_loading">
      <spinner class="mb5" size="large"></spinner>
      <div class="f5 fw6 mid-gray">Loading "{{short_json_filename}}"</div>
    </div>
    <div class="mw7 mt4 self-start">
      <div class="f5 fw6 mid-gray" v-if="error_str.length > 0">{{error_str}}</div>
      <pre class="pa3 bg-black-10" v-if="false">{{json_payload}}</pre>
    </div>
  </div>
</template>

<script>
  import { ROUTE_PIPEHOME } from '../constants/route'
  import Spinner from './Spinner.vue'
  import axios from 'axios'

  export default {
    components: {
      Spinner
    },
    data() {
      return {
        is_loading: true,
        json_payload: '',
        loading_str: '',
        error_str: ''
      }
    },
    computed: {
      json_filename() {
        return _.get(this.$route, 'query.path', '')
      },
      short_json_filename() {
        var idx = this.json_filename.lastIndexOf('/')
        return this.json_filename.substring(idx > 0 ? idx+1 : 0)
      }
    },
    mounted() {
      if (this.json_filename.length > 0)
      {
        axios.get(this.json_filename).then(response => {
          this.json_payload = JSON.stringify(response.data, null, 2)
          this.tryCreatePipe(JSON.parse(this.json_payload))
        }).catch(response => {
          this.is_loading = false
          this.error_str =
            '# File not found\n\n' +
            'We were unable to load the pipe JSON from `'+this.json_filename+'`.\n\n' +
            'Make sure the `'+this.json_filename+'` exists to copy the pipe to your project.'
        })
      }
    },
    methods: {
      tryCreatePipe(attrs) {
        // TODO: remove hard-coding
        var attrs = _.assign({}, attrs, { parent_eid: 'q57nw82zl3gm' })

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            this.openPipe(response.body.eid)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      openPipe(eid) {
        this.$router.push({ name: ROUTE_PIPEHOME, params: { eid } })
      }
    }
  }
</script>
