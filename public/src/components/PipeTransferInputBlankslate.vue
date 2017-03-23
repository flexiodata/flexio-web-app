<template>
  <div>
    <slot></slot>
    <div class="mb3">
      <btn
        btn-md
        btn-primary
        class="ttu b"
        @click="$emit('choose-connection')"
      >
        Choose a connection
      </btn>
    </div>
    <div v-if="false">
      <text-separator class="mv3 ttu"></text-separator>
      <div class="mv3">
        <btn
          btn-md
          btn-primary
          class="ttu b"
        >
          Upload files
        </btn>
      </div>
    </div>
    <text-separator class="mv3 ttu"></text-separator>
    <div class="mt3">
      <div class="f6 fw6 ttu">Use the command line (CLI)</div>
      <p class="lh-copy mb0 mid-gray f6">The Flex.io Command Line Interface (CLI) is a tool for managing and running your Flex.io services from the command line. <a href="https://www.flex.io/docs/api/#command-line-interface" class="blue no-underline underline-hover" target="_blank">Learn more...</a></p>
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
