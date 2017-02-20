<template>
  <div class="flex flex-column">
    <div class="flex-fill flex flex-row items-stretch relative">
      <code-editor :val="code_text"></code-editor>
    </div>
  </div>
</template>

<script>
  import CodeEditor from './CodeEditor.vue'

  var default_text = "\
def greet(name):\n\
    print 'Hello', name\n\
greet('Jack')\n\
greet('Jill')\n\
greet('Bob')\n\
"

  export default {
    components: {
      CodeEditor
    },
    data() {
      return {
        code_text: default_text
      }
    }
  }
</script>
