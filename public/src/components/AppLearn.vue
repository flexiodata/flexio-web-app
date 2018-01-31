<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Learn to create pipes in a few simple steps</div>
      <div class="mt3 pv4 f4 bt b--black-10">
        <span class="ma1">I want to</span>
        <select name="" class="pa1 b--black-10">
          <option value="a">convert a CSV to JSON</option>
          <option value="b">filter a JSON file from the web</option>
          <option value="c" selected>copy and process files from cloud storage</option>
        </select>
      </div>
      <div class="flex flex-column flex-row-ns">
        <div class="flex-fill mr4">
          <div
            class="pa3 mb3 f6 lh-copy bg-white ba b--black-10 onboarding-box"
            v-html="getStepCopy(step)"
            v-for="(step, index) in steps"
          ></div>
        </div>
        <div class="flex-fill">
          <onboarding-code-editor
            cls="relative pa3 bg-black-05 br2 box-shadow"
            code="
Flexio.pipe()
  .echo('Hello, World.')
  .run(callback)
"
          />
        </div>
      </div>
    </div>

  </div>
</template>

<script>
  import marked from 'marked'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'

  export default {
    components: {
      OnboardingCodeEditor
    },
    data() {
      return {
        steps: [
          {
            blurb:
`
#### Step 1.  Access a CSV file from a directory

The first step in this pipe is to access a CSV file.  A CSV file could be located in a URL, a cloud storage service or could be sent to your pipe via email or web upload.  In this case, we'll get this from an AWS S3 directory as seen in the the 'read' task in the console to the right. Click the 'Run' button to show the output.
You could also select a different CSV file and re-run the pipe, using another file from the S3 directory or choosing from a web URL, respectively:

\`.read('/flexio-public-s3/bar.csv')\` or
\`.request('https://raw.githubusercontent.com/flexiodata/data/master/pandas-pivot-sample/sales-funnel-sample.csv')\`

Now let's add a task to manipulate the file.

[Next button]
`
          },
          {
            blurb:
`
#### Step 2.  Add a processing step

Let's add a simple \`select\` step that selects only a few of the columns in our file. Add this below the \`read\` or \`request\` task:

\`.select('firstname', 'lastname', 'city')\`

Now re-run the pipe and you'll see only these three columns in the output.  You could also use tasks like \`convert\` or  \`filter\` or \`execute\` with python to do further table manipulation.  Now, let's send this file somewhere.

[Next button]
`
          },
          {
            blurb:
`
#### Step 3. Transfer the file to a cloud storage account

Now that we've accessed our file and performed some basic processing, let's send it somewhere.  If you have a cloud storage account, like dropbox, box, s3, etc., let's set that up now:

[Add Storage button]

Or, if you already have an account set up in the Storage area, you can see a list of your connection ID or aliases here:

[See connection IDs and aliases button (pop up a modal that where you can copy the alias or ID]

Now, let's add your output. In the following code, replace \`{storage-alias}\` is your Storage ID or alias and {path} with your directory where you wish to place the file.  We'll use \`myfile.csv\` as the file name:

\`.write('\{storage-alias}\{path}\myfile.csv')\`

Now re-run your pipe and check your directory to see your new file.  If you don't have a cloud storage account handy, you can also simply email the file to yourself:

'.email('foo... foo... foo [I don't know the SDK syntax]')

That's it! You can now go to the Pipes tab to add a new pipe of your own.  If you have any questions, please just let us know by clicking on the chat button at the bottom right of this page and we'll be happy to help!
`
          }
        ]
      }
    },
    methods: {
      getStepCopy(step) {
        return marked(step.blurb.trim())
      }
    }
  }
</script>

<style lang="less">
  .onboarding-box {
    h4 {
      margin-top: 0
    }

    p:last-child {
      margin-bottom: 0
    }
  }
</style>
