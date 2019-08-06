<template>
  <div>
    <div class="mv5 center pa4 bg-white ba b--black-10 br3" style="max-width: 560px">
      <el-button @click="show_sign_up_modal = true" type="primary">Show Sign Up Modal</el-button>
      <SignUpModal
        @cancel="show_sign_up_modal = false"
        v-if="show_sign_up_modal"
      />
    </div>
    <div class="mv5 center pa4 bg-white ba b--black-10 br3" style="max-width: 560px">
      <FlexioEntryForm />
    </div>
    <div class="overflow-x-hidden overflow-y-auto" v-if="false">
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a1"  ref="a1" >1</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a2"  ref="a2" >2</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a3"  ref="a3" >3</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a4"  ref="a4" >4</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a5"  ref="a5" >5</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a6"  ref="a6" >6</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a7"  ref="a7" >7</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a8"  ref="a8" >8</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a9"  ref="a9" >9</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a10" ref="a10">10</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a11" ref="a11">11</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a12" ref="a12">12</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a13" ref="a13">13</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a14" ref="a14">14</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a15" ref="a15">15</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a16" ref="a16">16</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a17" ref="a17">17</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a18" ref="a18">18</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a19" ref="a19">19</div>
      <div class="ma3 pa3 ba b--black-10 bg-white" id="a20" ref="a20">20</div>

      <PopperStep :target="$refs['a1']" v-if="is_mounted && false">
        Test Content
      </PopperStep>

      <PopperTour v-if="is_mounted"
        :steps="steps"
      />
    </div>
  </div>
</template>

<script>
  import FlexioEntryForm from '@/components/FlexioEntryForm'
  import SignUpModal from '@/components/SignUpModal'
  import PopperStep from '@/components/PopperStep'
  import PopperTour from '@/components/PopperTour'

  export default {
    metaInfo: {
      title: '[Admin] Prototype'
    },
    components: {
      FlexioEntryForm,
      SignUpModal,
      PopperStep,
      PopperTour
    },
    data() {
      return {
        show_sign_up_modal: false,
        is_mounted: false,
        steps: [
          {
            target: '#a1',
            title: '<div class="tc mv1 f3">Welcome to Flex.io!</div>',
            content: '<div><div class="mb2 b">Step 1 of 6:</div>Here\'s a two-minute tour to help you get started with Flex.io.</div>',
            popperOptions: {
              placement: 'none'
            }
          },
          {
            target: '#a19',
            content: '<div class="mb3"><div class="mb2 b">Step 2 of 6:</div>Functions contain steps. New steps can be added by clicking on the plus button.</div>'
          },
          {
            target: '#a3',
            content: '<div class="mb3"><div class="mb2 b">Step 3 of 6:</div>Click on the "Test" button to run this function.</div>'
          },
          {
            target: '#a4',
            content: '<div class="mb3"><div class="mb2 b">Step 4 of 6:</div>And here\'s the function output.</div>',
            params: {
              placement: 'left'
            }
          },
          {
            target: '#a20',
            content: '<div class="mb3"><div class="mb2 b">Step 5 of 6:</div>Now we\'ve added a new step to your function. The output from the execute step is passed to the input of this email step.<br><br>Now test your function again and check your inbox.</div>',
            params: {
              placement: 'left'
            }
          },
          {
            target: '#a6',
            content: '<div class="mb3"><div class="mb2 b">Step 6 of 6:</div>Finally, click the "Schedule" button and set the function to run every five minutes.</div>'
          },
          {
            target: '#a7',
            content: '<div class="mb3"><div class="mb2 b">Thanks for checking out Flex.io!</div>Click the function list to see other examples or create your own function.</div>'
          }
        ]
      }
    },
    mounted() {
      this.is_mounted = true
    }
  }
</script>
