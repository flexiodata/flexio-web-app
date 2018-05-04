<template>
  <div class="bg-nearer-white ph4 pb4 overflow-y-auto relative" :id="doc_id">
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <Spinner size="large" message="Loading..." />
    </div>
    <div
      class="center"
      style="max-width: 60rem; margin-bottom: 6rem"
      v-else-if="is_fetched"
    >
      <div class="sticky bg-nearer-white relative z-1">
        <h1 class="db mv0 pv4 fw6 mid-gray">{{title}}</h1>
      </div>
      <div>
        <h3 class="mv4 fw6 mid-gray">Properties</h3>
        <el-form
          ref="form"
          style="max-width: 40rem"
          label-width="9rem"
          :model="form_values"
          v-if="form_values"
        >
          <el-form-item
            key="name"
            label="Name"
            prop="name"
          >
            <el-input
              placeholder="Enter name"
              v-model="form_values.name"
            />
          </el-form-item>
          <el-form-item
            key="alias"
            label="Path"
            prop="alias"
          >
            <el-input
              placeholder="Enter alias"
              v-model="form_values.alias"
            >
              <template slot="prepend">https://api.flex.io/v1/me/pipes/</template>
            </el-input>
          </el-form-item>
          <el-form-item
            key="description"
            label="Description"
            prop="description"
          >
            <el-input
              type="textarea"
              placeholder="Enter description"
              v-model="form_values.description"
            />
          </el-form-item>
        </el-form>

        <h3 class="mv4 fw6 mid-gray">Configuration</h3>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'

  export default {
    components: {
      Spinner
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      form_values: {
        handler: 'updateForm',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        form_values: null,
        stickybits_instance: null
      }
    },
    computed: {
      ...mapState({
        edit_pipe: state => state.pipe.edit_pipe,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      },
      doc_id() {
        return 'pipe-doc-' + this.eid
      },
      title() {
        return _.get(this.getOriginalPipe(), 'name', '')
      }
    },
    methods: {
      ...mapGetters('pipe', [
        'getOriginalPipe'
      ]),
      loadPipe() {
        this.$store.commit('pipe/FETCHING_PIPE', true)

        this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
          if (response.ok) {
            var pipe = response.data
            this.$store.commit('pipe/INIT_PIPE', pipe)
            this.$store.commit('pipe/FETCHING_PIPE', false)
          } else {
            this.$store.commit('pipe/FETCHING_PIPE', false)
          }

          setTimeout(() => { this.initSticky() }, 100)
        })
      },
      initSticky() {
        if (!_.isNil(this.stickybits_instance)) {
          this.stickybits_instance.cleanup()
        }

        this.stickybits_instance = stickybits('.sticky')
      },
      updateForm() {
        if (this.form_values === null) {
          var form_values = _.get(this.$store, 'state.pipe.edit_pipe')
          this.form_values = _.cloneDeep(form_values)
        } else {
          this.$store.commit('pipe/UPDATE_EDIT_PIPE', this.form_values)
        }
      }
    }
  }
</script>
