<template>
  <article class="flex flex-column justify-between w5a br2 pv3 ph3 ma3 bg-nearer-white ba b--black-10">
    <div class="tl">
      <highlight-step-list
        class="justify-center"
        size="medium"
        :steps="highlight_steps"
      ></highlight-step-list>
      <div class="mv3 center bb b--black-10"></div>
      <div class="mt3 mb2 f4">{{title}}</div>
      <div class="mt3 mb3 lh-copy measure f6 black-70">
        {{description}}
      </div>
    </div>
    <div class="tc mt2">
      <router-link
        class="db center link no-underline tc mw4 f6 f6-ns ttu b br1 white bg-blue darken-10 ph2 ph3-ns pv2"
        :to="copy_pipe_url"
      >
        <span>Use this</span>
      </router-link>
    </div>
  </article>
</template>

<script>
  import HighlightStepList from './HighlightStepList.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
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
      json_file_url() {
        return 'https://www.flex.io/examples/src/config/json/' + _.get(this.item, 'slug') + '.json'
      },
      copy_pipe_url() {
        return '/copypipe?path='+encodeURIComponent(this.json_file_url)
      }
    }
  }
</script>
