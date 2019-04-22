<template>
  <div class="flex flex-column justify-center bg-white ba b--black-10" :style="inner_style" v-if="is_fetching">
    <Spinner size="large" message="Loading preview..." />
  </div>
  <div v-else-if="is_image">
    <img :stream-eid="streamEid" :src="stream_content_url" :style="inner_style" class="dib">
  </div>
  <div v-else-if="is_flexio_html" :style="inner_style" class="bg-white ba b--black-10">
    <iframe :stream-eid="streamEid" :src="stream_content_url" class="absolute top-0 left-0 w-100 h-100" height="100%" width="100%" frameborder="0" allowfullscreen></iframe>
  </div>
  <div v-else-if="is_pdf" :style="inner_style" class="bg-white relative overflow-hidden">
    <iframe :stream-eid="streamEid" :src="stream_content_url" class="absolute top-0 left-0 w-100 h-100" height="100%" width="100%" frameborder="0" allowfullscreen></iframe>
  </div>
  <div v-else-if="is_json || is_html || is_text" :style="inner_style" class="bg-white ba b--black-10">
    <StreamText
      :stream-eid="streamEid"
      :content-url="stream_content_url"
      :query-params="stream_query_params"
      :is-json="is_json"
      :is-html="is_html"
    />
  </div>
  <div v-else-if="is_table" :style="inner_style" class="bg-white ba b--black-10">
    <Grid
      :data-url="stream_content_url"
      :live-scroll="false"
    />
  </div>
</template>

<script>
  import * as mt from '../constants/mimetype'
  import { API_V2_ROOT } from '../api/resources'
  import Grid from 'vue-grid2'
  import Spinner from 'vue-simple-spinner'
  import StreamText from '@comp/StreamText'

  var containsSubstrings = function(arr, str) {
    var bools = _.map(arr, (a) => {
      return str.indexOf(a) != -1
    })

    return _.some(bools, Boolean)
  }

  export default {
    props: {
      streamEid: {
        type: String,
        default: ''
      },
      height: {
        type: Number,
        default: 360
      }
    },
    components: {
      Grid,
      Spinner,
      StreamText
    },
    computed: {
      stream() {
        return _.get(this.$store, 'state.objects.'+this.streamEid)
      },
      stream_content_url() {
        if (this.is_flexio_html) {
          return API_V2_ROOT + '/me/streams/' + this.streamEid + '/content?content_type=text/html'
        }

        return API_V2_ROOT + '/me/streams/' + this.streamEid + '/content'
      },
      stream_query_params() {
        return containsSubstrings([
          mt.MIMETYPE_TEXT_PLAIN,
          mt.MIMETYPE_TEXT_CSV
        ], this.mime_type) ? { encode: 'UTF-8' } : {}
      },
      is_fetched() {
        return _.get(this.stream, 'is_fetched', false)
      },
      is_fetching() {
        return _.get(this.stream, 'is_fetching', false)
      },
      mime_type() {
        return _.get(this.stream, 'mime_type', '')
      },
      is_image() {
        return containsSubstrings([
          mt.MIMETYPE_IMAGE_JPG,
          mt.MIMETYPE_IMAGE_PNG,
          mt.MIMETYPE_IMAGE_BMP,
          mt.MIMETYPE_IMAGE_GIF,
          mt.MIMETYPE_IMAGE_TIFF
        ], this.mime_type)
      },
      is_flexio_html() {
        return this.mime_type.indexOf(mt.MIMETYPE_APPLICATION_VND_HTML) != -1
      },
      is_pdf() {
        return this.mime_type.indexOf(mt.MIMETYPE_APPLICATION_PDF) != -1
      },
      is_html() {
        return this.mime_type.indexOf(mt.MIMETYPE_TEXT_HTML) != -1
      },
      is_json() {
        return this.mime_type.indexOf(mt.MIMETYPE_APPLICATION_JSON) != -1
      },
      is_text() {
        return containsSubstrings([
          mt.MIMETYPE_APPLICATION_XML,
          mt.MIMETYPE_APPLICATION_OCTET_STREAM,
          mt.MIMETYPE_TEXT_PLAIN,
          mt.MIMETYPE_TEXT_CSV
        ], this.mime_type)
      },
      is_table() {
        return this.mime_type.indexOf(mt.MIMETYPE_APPLICATION_VND_FLEXIO_TABLE) != -1
      },
      inner_style() {
        if (this.height <= 0)
          return ''

        return this.is_image ? 'max-height: ' + this.height + 'px' : 'height: ' + this.height + 'px'
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
        if (!this.streamEid) {
          return
        }

        this.$store.dispatch('v2_action_fetchStream', { eid: this.streamEid }).catch(error => {
          // TODO: add error handling?
        })
      }
    }
  }
</script>
