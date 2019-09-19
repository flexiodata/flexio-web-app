<template>
  <div
    class="flex flex-column justify-center bg-white"
    :style="inner_style"
    v-if="is_fetching"
  >
    <Spinner size="large" message="Loading preview..." />
  </div>

  <img
    :stream-eid="streamEid"
    :src="stream_content_url"
    :style="inner_style"
    v-else-if="is_image"
  >

  <iframe
    class="absolute absolute--fill"
    height="100%"
    width="100%"
    frameborder="0"
    allowfullscreen
    :stream-eid="streamEid"
    :src="stream_content_url"
    :style="inner_style"
    v-else-if="is_flexio_html"
  ></iframe>

  <iframe
    class="absolute absolute--fill bg-white relative overflow-hidden"
    height="100%"
    width="100%"
    frameborder="0"
    allowfullscreen
    :stream-eid="streamEid"
    :src="stream_content_url"
    :style="inner_style"
    v-else-if="is_pdf"
  ></iframe>

  <StreamText
    :stream-eid="streamEid"
    :content-url="stream_content_url"
    :query-params="stream_query_params"
    :is-json="is_json"
    :is-html="is_html"
    :style="inner_style"
    v-else-if="is_json || is_html || is_text"
  />

  <div v-else>Could not read content type</div>
</template>

<script>
  import { mapState } from 'vuex'
  import * as mt from '@/constants/mimetype'
  import { API_ROOT } from '../api/resources'
  import Spinner from 'vue-simple-spinner'
  import StreamText from '@/components/StreamText'

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
      Spinner,
      StreamText
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      stream() {
        return _.get(this.$store, 'state.streams.items.'+this.streamEid)
      },
      stream_content_url() {
        var base_url = API_ROOT + '/' + this.active_team_name + '/streams/' + this.streamEid

        if (this.is_flexio_html) {
          return base_url + '/content?content_type=text/html'
        }

        return base_url + '/content'
      },
      stream_query_params() {
        return containsSubstrings([
          mt.MIMETYPE_TEXT_PLAIN,
          mt.MIMETYPE_TEXT_CSV
        ], this.mime_type) ? { encode: 'UTF-8' } : {}
      },
      is_fetched() {
        return _.get(this.stream, 'vuex_meta.is_fetched', false)
      },
      is_fetching() {
        return _.get(this.stream, 'vuex_meta.is_fetching', false)
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

        var team_name = this.active_team_name

        this.$store.dispatch('streams/fetch', { team_name, eid: this.streamEid }).catch(error => {
          // TODO: add error handling?
        })
      }
    }
  }
</script>
