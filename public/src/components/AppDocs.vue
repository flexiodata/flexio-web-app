<template>
  <div class="overflow-y-auto pb5 bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Documentation</div>
    </div>
    <div class="flex flex-row flex-wrap ma3">
      <a
        target="_blank"
        class="flex flex-column db w-100 mw6-l ma3 bg-white br2 pa3 pa4-ns no-underline ba bw1 b--black-10 hover-b--blue"
        :href="item.link.href"
        v-for="(item, index) in doc_items"
      >
        <div class="f3 black">{{item.title}}</div>
        <p class="lh-copy black-70 flex-fill">
          {{item.description}}
        </p>
      </a>
    </div>
  </div>
</template>

<script>
  var doc_items = require('json-loader!yaml-loader!../data/doc-items.yml')

  export default {
    data() {
      return {
        doc_items
      }
    }
  }
</script>
