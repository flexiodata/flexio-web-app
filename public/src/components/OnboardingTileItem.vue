<template>
  <article class="flex flex-column justify-between w5a br2 pv4 ph2 ma3 bg-nearer-white ba b--black-10">
    <div class="tl">
      <highlight-step-list
        class="justify-center"
        size="medium"
        :steps="highlight_steps"
      ></highlight-step-list>
      <div class="ph3">
        <div class="mv3 center bb b--black-10"></div>
        <div class="mt3 mb2 f4">{{title}}</div>
        <div class="mt3 mb3 lh-copy measure f6 black-70">
          {{description}}
        </div>
      </div>
    </div>
    <div class="tc mt2">
      <btn
        btn-md
        btn-primary
        class="ttu b ph4"
        @click="useTemplate"
      >
        <span>Use this</span>
      </btn>
    </div>
  </article>
</template>

<script>
  import Btn from './Btn.vue'
  import HighlightStepList from './HighlightStepList.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
      Btn,
      HighlightStepList
    },
    computed: {
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        return _.get(this.item, 'short_description', '')
      },
      highlight_steps() {
        return _.get(this.item, 'highlight_steps', [])
      },
      json_file_name() {
        return _.get(this.item, 'slug') + '.json'
      },
      json_file_url() {
        return 'https://www.flex.io/examples/src/config/json/' + this.json_file_name
      },
      copy_pipe_url() {
        return '/copypipe?path='+encodeURIComponent(this.json_file_url)
      }
    },
    methods: {
      useTemplate() {
        analytics.track('Closed Onboarding Modal: Template', { template: json_file_name })
        this.$router.push({ path: copy_pipe_url })
      }
    }
  }
</script>
