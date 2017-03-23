<template>
  <div>
    <slot></slot>
    <div class="mb3">
      <btn
        btn-md
        btn-primary
        class="ttu b"
      >
        Choose a connection
      </btn>
    </div>
    <text-separator class="mv3 ttu"></text-separator>
    <div class="mt3">
      <div class="f6 fw6 ttu">Use the REST API</div>
      <p class="lh-copy mb0 mid-gray f6">Flex.io pipes can be run and have their results returned via an AJAX call to a pipe's REST API endpoint. <a href="https://www.flex.io/docs/api/#pipes" class="blue no-underline underline-hover" target="_blank">Learn more...</a></p>
    </div>
  </div>
</template>

<script>
  import Btn from './Btn.vue'
  import TextSeparator from './TextSeparator.vue'

  export default {
    components: {
      Btn,
      TextSeparator
    }
  }
</script>
