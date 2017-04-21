<template>
  <div class="ma4">
    <div class="f6 fw6 mid-gray mb3">{{json_filename}}</div>
    <div class="pa3 bg-black-10" v-if="error_str.length > 0">{{error_str}}</div>
    <pre class="pa3 bg-black-10" v-else>{{json_payload}}</pre>

  </div>
</template>

<script>
  import axios from 'axios'

  export default {
    data() {
      return {
        json_payload: '',
        error_str: ''
      }
    },
    computed: {
      json_filename() {
        return _.get(this.$route, 'query.path', '')
      }
    },
    mounted() {
      if (this.json_filename.length > 0)
      {
        axios.get(this.json_filename).then(response => {
          this.json_payload = JSON.stringify(response.data, null, 2)
        }).catch(response => {
          this.error_str =
            '# File not found\n\n' +
            'We were unable to load the pipe JSON from `'+this.json_filename+'`.\n\n' +
            'Make sure the `'+this.json_filename+'` exists to copy the pipe to your project.'
        })
      }
    }
  }
</script>
