<template>
  <article class="bg-nearer-white br2 ma2 pv1 ph2" style="" :class="cls">
    <div class="flex flex-row items-center pointer na1 pa1" @click="toggleDetails">
      <span class="black-30 mr1">
        <i
          class="material-icons db v-mid trans-a"
          :class="{ 'rotate-90': !show_details }"
          v-if="has_details"
        >chevron_right</i>
        <div
          class="flex flex-row items-center justify-center"
          style="width: 24px; height: 24px"
          v-else
        >
          <Spinner :size="16" :line-size="2" v-if="item.is_running" />
        </div>
      </span>
      <span class="f6 fw6 mr2">{{item.id}}</span>
      <div class="flex-fill">&nbsp;</div>
      <div class="tr pl3 f7 fw6">{{item.message}}</div>
      <div class="f6 pl3 pr1 tr ttu b" style="width: 5rem">
        <div class="dark-green" v-if="has_details && is_passed===true">Passed</div>
        <div class="dark-red" v-if="has_details && !is_passed===true">Failed</div>
        <div class="yellow" v-if="is_xhr_error">&nbsp;Error</div>
      </div>
    </div>
    <div class="pv1" style="margin-left: 24px" v-if="is_xhr_error">
      <table class="w-100 css-test-table">
        <tr>
          <td class="v-top f6 b w3">&nbsp;</td>
          <td class="v-top f6"><pre class="ma0">{{item.error_text}}</pre></td>
        </tr>
      </table>
    </div>
    <div class="pv1" style="margin-left: 24px" v-if="item.details && item.details.length > 0" v-show="!show_details">
      <table class="w-100 css-test-table">
        <tr
          :class="!detail.passed ? 'css-row-error' : ''"
          :key="detail.name"
          v-for="(detail, index) in item.details"
        >
          <td class="v-top f6 b w3">{{detail.name}}</td>
          <td class="v-top f6 min-w6 mw6">
            <div>{{detail.description}}</div>
            <div class="flex flex-row mh3" style="margin-top: 2px; margin-bottom: 2px; max-height: 8rem" v-if="!detail.passed && detail.message && detail.message.length > 0">
              <div class="pa1 f8 code overflow-auto br1 bg-white" style="box-shadow: 0 1px 3px -1px rgba(0,0,0,0.4)">
                {{detail.message}}
              </div>
            </div>
          </td>
          <td class="v-top f7 tr">
            <div class="dib pr1">
              <div class="flex flex-row items-center nowrap ttu br1 white bg-dark-green" style="padding: 1px 5px" v-if="detail.passed">
                <span class="f6 monospace">Passed</span>
              </div>
              <div class="flex flex-row items-center nowrap ttu br1 white bg-dark-red" style="padding: 1px 5px" v-if="!detail.passed">
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
      is_xhr_error() {
        return _.get(this.item, 'xhr_error', false)
      },
      cls() {
        return {
          'css-test-error': this.is_xhr_error === true,
          'css-test-success': this.is_passed === true,
          'css-test-failure': this.is_passed === false
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

<style lang="stylus" scoped>
  .css-test-success
    background-color: rgba(0,255,0,0.1)

  .css-test-failure
    background-color: rgba(255,0,0,0.1)

  .css-test-error
    background-color: rgba(255,255,0,0.1)

  .css-test-table
    tr:hover
      background-color: rgba(0,0,0,0.05)
    td
      padding: 2px 0 2px 3px
</style>
