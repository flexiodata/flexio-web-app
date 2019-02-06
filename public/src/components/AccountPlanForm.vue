<template>
  <div>
    <div class="flex flex-row items-stretch justify-between nl2 nr2">
      <div
        class="mh2 ph3 tc br3 cursor-default trans-a"
        style="box-shadow: inset 0 -4px 12px rgba(0,0,0,0.075)"
        :class="isPlanMatch(current_plan, plan['Name']) ? 'bg-blue white' : 'bg-nearer-white'"
        v-for="plan in plans"
      >
        <div class="mv4 fw6">{{plan['Name']}}</div>
        <div class="nt2 nb2">
          <span class="f1">${{plan['Price']}}</span><span class="f6">/mo</span>
        </div>
        <div class="mt4 mb3">
          <div>{{plan['Executions']}} executions</div>
          <div class="mt1 o-40" style="font-size: 12px; line-height: 1.25">+ ${{plan['Extra Executions']}} per additional execution</div>
        </div>
        <div class="mv3 pt2">
          <div
            v-if="isPlanMatch(current_plan, plan['Name'])"
          >
            <i class="el-icon-success f2" style="color: #fff"></i>
          </div>
          <el-button
            type="primary"
            class="w-100 ttu fw6"
            @click="choosePlan(plan['Name'])"
            v-else
          >
            Choose
          </el-button>
        </div>
      </div>
    </div>
    <div class="mt3" v-if="false">
      <el-button
        type="primary"
        class="ttu fw6"
      >
        Change Plan
      </el-button>
    </div>
  </div>
</template>

<script>
  import plans from '../data/usage-plans.yml'
  import { mapGetters } from 'vuex'

  export default {
    data() {
      return {
        plans: plans,
        current_plan: ''
      }
    },
    created() {
      this.current_plan = _.get(this.getActiveUser(), 'usage_tier', '')
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      isPlanMatch(plan1, plan2) {
        return plan1.toLowerCase() == plan2.toLowerCase()
      },
      choosePlan(plan) {
        this.current_plan = plan['Name'].toLowerCase()
      }
    }
  }
</script>
