<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <div class="flex-fill tc">
          <span class="f3 b">Welcome to Flex.io, {{first_name}}!</span>
        </div>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="$emit('close')"></i>
      </div>
    </div>

    <div v-if="api_key.length > 0">
      <h3>This is your API key:</h3>
      <div class="mh3">
        <OnboardingCodeEditor
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :show-buttons="false"
          :code="api_key"
        />
      </div>
      <h3>Here's a simple pipe we created for you:</h3>
      <div class="mh3">
        <OnboardingCodeEditor
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :show-buttons="false"
          :code="pipe_code"
        />
      </div>
      <h3>Try running it:</h3>
      <div class="mh3">
        <OnboardingCodeEditor
          label="HTTP"
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :buttons="['copy']"
          :code="example_href"
        />
      </div>
      <div class="mh3 mt3">
        <OnboardingCodeEditor
          label="cURL"
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :buttons="['copy']"
          :code="example_curl"
        />
      </div>
      <div class="mt4 mb3 tc">
        <el-button type="primary" size="large" class="ttu fw6" @click="$emit('close')">Now build your own data feed</el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Flexio from 'flexio-sdk-js'
  import OnboardingCodeEditor from '@comp/OnboardingCodeEditor'

  export default {
    props: {
      'show-header': {
        type: Boolean,
        default: true
      }
    },
    components: {
      OnboardingCodeEditor
    },
    watch: {
      tokens_fetched: {
        handler: 'checkCreateApiKey',
        immediate: true
      }
    },
    computed: {
      ...mapState([
        'active_user_eid',
        'tokens_fetched'
      ]),
      pipe() {
        return _.find(this.getAllPipes(), (p) => { return _.includes(_.get(p, 'name'), 'convert-csv-to-json') })
      },
      pipe_identifier() {
        return _.get(this.pipe, 'name', '') || _.get(this.pipe, 'eid', '')
      },
      pipe_code() {
        if (this.pipe_identifier.length == 0)
          return 'Loading...'

        // do this until we fix the object ref issue in the Flex.io JS SDK
        var task_obj = _.cloneDeep(this.pipe.task)
        return Flexio.pipe(task_obj).toCode()
      },
      first_name() {
        return _.get(this.getActiveUser(), 'first_name', '')
      },
      api_key() {
        return this.getFirstToken()
      },
      example_href() {
        return 'https://api.flex.io/v1/me/pipes/' + this.pipe_identifier + '/run?api_key=' + this.api_key
      },
      example_curl() {
        return "curl -X POST 'https://api.flex.io/v1/me/pipes/" + this.pipe_identifier + "/run' -H 'Authorization: Bearer " + this.api_key + "'"
      }
    },
    mounted() {
      this.tryFetchPipes()
    },
    methods: {
      ...mapGetters([
        'getActiveUser',
        'getAllPipes',
        'getFirstToken'
      ]),
      tryFetchPipes() {
        this.$store.dispatch('v2_action_fetchPipes', {}).catch(error => {
          // TODO: add error handling?
        })
      },
      checkCreateApiKey() {
        if (this.tokens_fetched && this.api_key.length == 0) {
          this.$store.dispatch('v2_action_createToken', {}).catch(error => {
            // TODO: add error handling?
          })
        }
      }
    }
  }
</script>
