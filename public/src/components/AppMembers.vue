<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100 bg-nearer-white">
      <Spinner size="large" message="Loading members..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div>
      <div class="h3"></div>
      <div class="center mw-doc pa4 bg-white br2 css-white-box">
        <div class="flex flex-row items-start">
          <h3 class="flex-fill mt0 fw6 f3">Team Members</h3>
          <el-button
            class="ttu fw6"
            type="primary"
          >
            Add Member
          </el-button>
        </div>
        <table class="el-table w-100">
          <thead>
            <tr>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
            <tr
              :key="member.eid"
              v-for="member in members"
            >
              <td>
                <div class="flex flex-row items-center">
                  <img :src="getGravatarUrl(member)" class="br-100"/>
                  <div class="ml2">
                    <div>{{member.first_name}} {{member.last_name}}</div>
                    <div class="f6 black-30" style="margin-top: 2px">{{member.email}}</div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'

  export default {
    metaInfo() {
      return {
        title: 'Members'
      }
    },
    components: {
      Spinner
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'members_fetching',
        'is_fetched': 'members_fetched',
        'active_team_name': 'active_team_name'
      }),
      members() {
        return this.getAllMembers()
      }
    },
    created() {
      this.tryFetchMembers()
    },
    methods: {
      ...mapGetters([
        'getAllMembers'
      ]),
      getGravatarUrl(member) {
        return 'https://secure.gravatar.com/avatar/' + member.email_hash + '?d=mm&s=32'
      },
      tryFetchMembers() {
        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('v2_action_fetchMembers', { team_name: this.active_team_name }).catch(error => {
            // TODO: add error handling?
          })
        }
      }
    }
  }
</script>
