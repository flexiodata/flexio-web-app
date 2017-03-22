<template>
  <div>
    <div class="mv4">
      <btn
        btn-md
        btn-primary
        class="ttu b v-mid"
      >
        Choose a connection
      </btn>
    </div>
    <div class="mv4">
      <div class="v-mid dib bb b--black-10 w2"></div>
      <div class="v-mid dib black-30 f6 fw6 ttu">or</div>
      <div class="v-mid dib bb b--black-10 w2"></div>
    </div>
    <div class="mv4">
      <div class="f6 fw6 ttu">Use the REST API</div>
      <p class="tl lh-copy mid-gray f6">Flex.io pipes can be run and have their results returned via an AJAX call to a pipe's REST API endpoint. <a href="https://www.flex.io/docs/api/#pipes" class="blue" target="_blank">Learn more...</a></p>
    </div>
  </div>
</template>

<script>
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    }
  }
</script>
