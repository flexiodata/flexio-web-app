<template>
  <div>
    <div class="tc">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIgAAAAoCAMAAAASeEKOAAAAgVBMVEVMaXEiHh8iHh8iHh8iHh8iHh92vB4iHh8iHh8iHh9zth4iHR8iHh8iHh8iHh92vB52vB4iHh8iHh8iHh8iHh8iHh8iHh92vB4iHh92vB4iHh92vB52vB52vB52vB52vB4iHh8iHh92vB4iHh92vB52vB52vB52vB52vB52vB4iHh9CYC3gAAAAKXRSTlMA8Agx6dvppBYmB3TWSx7IMveymT/B4vKHJNAVnGOre1djjv3aQLlROAQZyksAAARtSURBVFjDxZjr1qogEIZRTFPT8pjHrMz68v4vcCMMCYZlq9ba86uU4nHmnYMgxM12rTC0Mm+N/qvpp0LriQVG6Dnsko9/xJTvysVr/bAfzbIHsszoDVf/HqM87++XeuHiddKLtvIQcukn92sO53wf7LZstdvLZqF1QT8UX7skNynIdZlDUpkjiJG9Yr75WiZ/DGTrLFnsAUARZdbAZOkcZPM1SLmlINUnkUl88hlHvYbR70DQbXDJNl+0NmIgMWANCv0dCPo7H5qF+WvRTQ1x0x+CfGDWszCVID6O49jWf1Ljuu4vd9Qg6WuQtRsWhqYZmyTDoHErImZlwu9iy6KXRtj8XBE7NMKmu+Z6Mfd787I9dx+D6O5qTG+D7W1v2NdshGXVR6yDuz3NmssDJK/YFWr76+0zEDuUK01BhR0H9IvmTVQfCX+0k+uIU5t32apSDeKoQPBmUnp7IxbyvgDcYyt9VYA41f3Jtju2EluJweppQu30DGKv+idLB6HooegCvuyI5kBUHCRslAQbkx2yJxCdt0QjiaKQrw8HQWIWnPY4OpY0qlmQ82Nz83IZY7Qtx7C+AoEAtBHNFjtqhQeHeykpyieIkz8LcuMyvdZ5Web1gZOcyd3wLciaCSRwJx2BdmYeHEu3Dd4w50Cg7dzNmqcQV+6+ezzHCxBYkUzF3XrCwta1euUEI4DUsKuQsdxHB3L7VKQQ6JSaOwEBhYhP6rWCGCCH++fMnYA4V4iDeBvg7oNedZgTNewPtp6A+NrUIZwNpiZxqirW8yA5e3pTbsQQrkZwtaYuaDGIAAsWjhk8ymT4ixjNg9wgCvJ9cEklgvhKEJBIQPoMN0OuqLzUE6GgFyCN+OzjkCDivQRx+zkLeOWCitqH6BUIFJHJQA8B+w2IGyhLyDIQcynIaRakBZAxb8LPQ7Nb7BGYrDfWk7FK++j96vegEaRWivW2WKx2MBt/sbyJTVkNArI05fm1UqSvGkQvoMrMcEDooCo+FZIRpLwoYgNavXfvQVCmaKr2Awu6txGH6tIqlHhQ60WoaLzabssFIHg6hxF5rngM+IjgIqxJClaA/MHTjy85j/mkRgtAdBBBetL5EQbxgsE2zMbhxGXVJLXBVW521OUxgG97qakDnO56lxzyBgRBg+/bxI0x9lzogsOZBXjLsIVSH1KZeOkwwegSCFcJQTk0zfnKx5N9hxaBoFP7aCaa9igapOfxwMB4mY45vF7By6M0oXV71ag4yvcdCPe6PMjjUce6nEDEPzFnkk8DbuYrjvcg6DSdbPuQ7OYxfa78SUkhisEtgOzk85FuO8Uw62lJCiSQVH4NxfJISQ+1ADYY88Qv+JTHghZg5BzkDjOcZIl22ImnAcZgKxHET+i15FGfdC8xAvqYbbDK6NJIo2vEyuGl7K9iZCdBYAxqyYkmTXEq21UmsOzNqpPef7FHTXy71mN6KRau+cfMCsPQilxMrzrsV57//LOjPRyX0vQlJM25lrYru6Y6EKuabiz3/wD8XkeE+HHPxgAAAABJRU5ErkJggg==" alt="Flex.io">
    </div>
    <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Thanks for signing up!</legend>
    <div class="mt4">
      <p class="lh-copy">Here's your API key. Copy and paste this API key for use with any of the templates.</p>
      <div class="flex flex-row">
        <input
          type="text"
          class="flex-fill input-reset pa2 bt bb bl b--black-20 br1 br--left"
          placeholder="API Key"
          autocomplete=off
          spellcheck="false"
          disabled
          :class="input_cls"
          v-model="api_key"
        >
        <btn
          btn-primary
          btn-square
          class="pv2 ph3 hint--top-left br1 br--right clipboardjs"
          aria-label="Copy to Clipboard"
          :data-clipboard-text="api_key"
        ><span class="ttu b">Copy</span></btn>
      </div>
      <div class="mt4">
        <p class="lh-copy">Open your dashboard if you need to get your API key again or need help getting started.</p>
        <a href="/app/dashboard" class="border-box no-select b ttu w-100 mt3 ph4 pv2a br1 white bg-blue b--blue darken-10 no-underline db tc" target="_blank" rel="noopener noreferrer">Open my dashboard</a>
      </div>
    </div>
  </div>
</template>

<script>
  import _ from 'lodash'
  import axios from 'axios'
  import Btn from './Btn.vue'

  export default {
    props: {
      eid: {
        type: String,
        required: true
      }
    },
    components: {
      Btn
    },
    data() {
      return {
        error_msg: '',
        label_submitting: 'Fetching API key...',
        is_submitting: false,
        api_key: '',
        input_cls: 'input-reset ba b--black-10 focus-b--transparent focus-outline focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    mounted() {
      this.fetchApiKey()
    },
    methods: {
      fetchApiKey() {
        this.is_submitting = true

        axios.get('/api/v1/users/'+this.eid+'/tokens').then(response => {
          this.is_submitting = false
          this.api_key = _.get(response, 'data[0].access_code', '')
        }).catch(error => {
          this.is_submitting = false
          this.error_msg = _.get(error, 'response.data.error.message', '')
        })
      }
    }
  }
</script>
