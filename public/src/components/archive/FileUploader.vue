<template>
  <uploader
    ref="uploader"
    title="Upload file"
    :auto="true"
    post-action="./post.php"
    :events="uploadEvents"
  ></uploader>
</template>

<script>
  import Vue from 'vue'
  import Uploader from 'vue-upload-component'

  /*
  Vue.filter('formatSize', function(size) {
    if (size > 1024 * 1024 * 1024 * 1024) {
      return (size / 1024 / 1024 / 1024 / 1024).toFixed(2) + ' TB'
    } else if (size > 1024 * 1024 * 1024) {
      return (size / 1024 / 1024 / 1024).toFixed(2) + ' GB'
    } else if (size > 1024 * 1024) {
      return (size / 1024 / 1024).toFixed(2) + ' MB'
    } else if (size > 1024) {
      return (size / 1024).toFixed(2) + ' KB'
    }
    return size.toString() + ' B'
  })
  */

  var upload_data = {
    accept: 'image/*',
    size: 1024 * 1024 * 10,
    multiple: true,
    extensions: 'gif,jpg,png',
    // extensions: ['gif','jpg','png'],
    // extensions: /\.(gif|png|jpg)$/i,
    files: [],
    upload: [],
    title: 'Test files',
    drop: true,
    auto: false,

    name: 'file',

    postAction: './post.php',
    putAction: './put.php',

    headers: {
      "X-Csrf-Token": "xxxx",
    },

    data: {
      "_csrf_token": "xxxxxx",
    },

    events: {
      add(file, component) {
        console.log('add')
        if (this.auto) {
          component.active = true
        }
        file.headers['X-Filename'] = encodeURIComponent(file.name)
        file.data.finename = file.name

        // file.putAction = 'xxx'
        // file.postAction = 'xxx'
      },
      progress(file, component) {
        console.log('progress ' + file.progress)
      },
      after(file, component) {
        console.log('after')
      },
      before(file, component) {
        console.log('before')
      }
    }
  }

  export default {
    components: {
      Uploader
    },
    data() {
      return {
        uploadEvents: {
          add(file, component) {
            console.log('add')
          }
        }
      }
    },
    mounted() {
      var uploader = this.$refs['uploader']
      var data = uploader.$data
    }
  }
</script>
