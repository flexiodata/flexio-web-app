<template>
  <article class="pa1 ma1 ba b--black-05" :class="cls">
    <div class="flex flex-row items-center pointer na1 pa1" @click="toggleDetails">
      <span class="black-30 mr1">
        <i
          class="material-icons db v-mid"
          :class="{ 'rotate-90': !show_details }"
          v-if="has_details"
        >chevron_right</i>
      </span>
      <span class="f6 fw6 mr2">{{item.id}}</span>
      <spinner :size="24" v-if="item.is_running"></spinner>
      <div class="flex-fill">&nbsp;</div>
      <div class="tr pl3 f7 fw6">{{item.message}}</div>
      <div class="f4 pl3 pr1 tr monospace ttu b dark-green" v-if="has_details && is_passed===true">Passed</div>
      <div class="f4 pl3 pr1 tr monospace ttu b dark-red" v-if="has_details && !is_passed===true">Failed</div>
      <div class="f4 pl3 pr1 tr monospace ttu b yellow" v-if="is_xhr_ok===false">&nbsp;Error</div>
    </div>
    <div class="pt2 pl2 f6" v-if="is_xhr_ok===false">
      <pre class="ma0">{{item.error_text}}</pre>
    </div>
    <div class="pt1" v-if="item.details && item.details.length > 0" v-show="!show_details">
      <table class="w-100 css-test-table">
        <tr :class="!detail.passed ? 'bg-black-05' : ''" v-for="(detail, index) in item.details">
          <td class="v-top f6 b w3">{{detail.name}}</td>
          <td class="v-top f6 min-w6 mw6">
            <div>{{detail.description}}</div>
            <div class="flex flex-row mr3 max-h4" style="margin: 2px 0" v-if="!detail.passed && detail.message && detail.message.length > 0">
              <div class="f6 monospace overflow-auto ba b--black-40 bg-white-60" style="padding: 2px 3px">
                {{detail.message}}
              </div>
            </div>
          </td>
          <td class="v-top f7 tr">
            <div class="dib">
              <div class="flex flex-row items-center nowrap ttu white bg-dark-green" style="padding: 1px 4px 1px 2px" v-if="detail.passed">
                <i class="material-icons f7" style="margin-right: 2px">check</i>
                <span class="f6 monospace">Passed</span>
              </div>
              <div class="flex flex-row items-center nowrap ttu white bg-dark-red" style="padding: 1px 4px 1px 2px" v-if="!detail.passed">
                <i class="material-icons f7" style="margin-right: 2px">close</i>
                <span class="f6 monospace">Failed</span>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </article>
</template>

<script>
  import Spinner from 'vue-simple-spinner'

  export default {
    props: ['item'],
    components: {
      Spinner
    },
    data() {
      return {
        show_details: _.get(this.item, 'passed', false)
      }
    },
    watch: {
      is_passed: function(val, old_val) {
        this.show_details = val ? true : false
      }
    },
    computed: {
      has_details() {
        return this.item.details ? true : false
      },
      is_passed() {
        return this.item.passed
      },
      is_xhr_ok() {
        return _.get(this.item, 'xhr.ok', true)
      },
      cls() {
        return {
          'css-test-error': this.is_xhr_ok === false,
          'css-test-success': this.is_passed === true,
          'css-test-failure': this.is_passed === false,
          'bg-nearer-white': true
        }
      }
    },
    methods: {
      toggleDetails() {
        this.show_details = !this.show_details
      }
    }
  }
</script>

<style lang="less">
  .css-test-success {
    background-color: rgba(0,255,0,0.1)
  }
  .css-test-failure {
    background-color: rgba(255,0,0,0.1)
  }
  .css-test-error {
    background-color: rgba(255,255,0,0.1)
  }
  .css-test-table {
    tr:hover {
      background-color: rgba(0,0,0,0.1);
    }
    td {
      padding: 0 0 0 3px;
    }
  }
</style>
