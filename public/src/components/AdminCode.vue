<template>
  <div class="flex flex-column">
    <div class="flex-fill flex flex-row items-stretch relative">
      <CodeEditor2
        :options="{ mode: 'python' }"
        v-model="code"
      />
    </div>
  </div>
</template>

<script>
  import CodeEditor2 from './CodeEditor2.vue'

  var default_text = "\
def greet(name):\n\
    print 'Hello', name\n\
greet('Jack')\n\
greet('Jill')\n\
greet('Bob')\n\
"

  export default {
    components: {
      CodeEditor2
    },
    watch: {
      code() {
        this.$nextTick(() => { console.log(this.code) })
      }
    },
    data() {
      return {
        code: default_text
      }
    }
  }
</script>
