<template>
  <div class="overflow-y-auto pb5 bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Documentation</div>
    </div>
    <div class="flex flex-row flex-wrap ma3">
      <a
        target="_blank"
        class="db w-100 mw6-l ma3 bg-white pa3 pa4-ns no-underline ba bw1 b--transparent hover-b--blue css-white-box"
        :href="item.link.href"
        v-for="(item, index) in doc_items"
      >
        <div class="f3 black lh-title">{{item.title}}</div>
        <p class="lh-copy black-70 flex-fill">
          {{item.description}}
        </p>
      </a>
    </div>
  </div>
</template>

<script>
  const doc_items = require('json-loader!yaml-loader!../data/doc-items.yml')

  export default {
    data() {
      return {
        doc_items
      }
    }
  }
</script>
