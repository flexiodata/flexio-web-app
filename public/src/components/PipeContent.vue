<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading preview..."></spinner>
  </div>
  <div v-else-if="is_image">
    <img :stream-eid="streamEid" :src="stream_content_url" class="dib max-h-100">
  </div>
  <div v-else-if="is_pdf" class="bg-white relative overflow-hidden">
    <iframe :stream-eid="streamEid" :src="stream_content_url" class="absolute top-0 left-0 w-100 h-100" height="100%" width="100%" frameborder="0" allowfullscreen></iframe>
  </div>
  <div v-else-if="is_json || is_text" class="bg-white ba b--black-10">
    <stream-text :stream-eid="streamEid" :content-url="stream_content_url" :is-json="is_json"></stream-text>
  </div>
  <div v-else-if="is_table" class="bg-white ba b--black-10">
    <stream-grid :stream-eid="streamEid" :content-url="stream_content_url" :task-json="taskJson"></stream-grid>
  </div>
</template>

<script>
  import * as mt from '../constants/mimetype'
  import Spinner from './Spinner.vue'
  import StreamText from './StreamText.vue'
  import StreamGrid from './StreamGrid.vue'
  import { API_ROOT } from '../api/resources'

  export default {
    props: ['stream-eid', 'task-json'],
    components: {
      Spinner,
      StreamText,
      StreamGrid
    },
    computed: {
      stream() {
        return _.get(this.$store, 'state.objects.'+this.streamEid)
      },

      stream_content_url() {
        return API_ROOT+'/streams/'+this.streamEid+'/content'
      },

      is_fetched() {
        return _.get(this.stream, 'is_fetched', false)
      },

      is_fetching() {
        return _.get(this.stream, 'is_fetching', false)
      },

      mime_type() {
        return _.get(this.stream, 'mime_type')
      },

      is_image() {
        return _.includes([
          mt.MIMETYPE_IMAGE_JPG,
          mt.MIMETYPE_IMAGE_PNG,
          mt.MIMETYPE_IMAGE_BMP,
          mt.MIMETYPE_IMAGE_GIF,
          mt.MIMETYPE_IMAGE_TIFF
        ], this.mime_type)
      },

      is_pdf() {
        return this.mime_type == mt.MIMETYPE_APPLICATION_PDF
      },

      is_json() {
        return this.mime_type == mt.MIMETYPE_APPLICATION_JSON
      },

      is_text() {
        return _.includes([
          mt.MIMETYPE_APPLICATION_XML,
          mt.MIMETYPE_APPLICATION_OCTET_STREAM,
          mt.MIMETYPE_TEXT_PLAIN,
          mt.MIMETYPE_TEXT_HTML,
          mt.MIMETYPE_TEXT_CSV
        ], this.mime_type)
      },

      is_table() {
        return this.mime_type == mt.MIMETYPE_APPLICATION_VND_FLEXIO_TABLE
      }
    },
    mounted() {
      this.tryFetchStream()
    },
    watch: {
      streamEid() {
        this.tryFetchStream()
      }
    },
    methods: {
      tryFetchStream() {
        if (!this.streamEid)
          return

        // for now, always fetch the stream
        //if (!this.is_fetched)
        //{
          this.$store.dispatch('fetchStream', { eid: this.streamEid })
        //}
      }
    }
  }
</script>
