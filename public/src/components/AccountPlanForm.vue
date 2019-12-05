<template>
  <div>
    <!-- fetching -->
    <div v-if="is_fetching">
      <div class="pa1 flex flex-row items-center">
        <Spinner size="small" />
        <span class="ml2 f6">Loading...</span>
      </div>
    </div>
    <!-- fetched -->
    <div v-else>
      <div v-if="is_editing_plan">
        <div class="mb3 f7 silver ttu fw6">Choose a plan</div>
        <div class="flex flex-column flex-row-l items-stretch justify-between nl2 nr2">
          <div
            class="flex-fill mh2 mb3 mb0-l ph3 tc br3 cursor-default"
            style="box-shadow: inset 0 -4px 12px rgba(0,0,0,0.075)"
            :class="isCurrentPlan(plan, current_plan_id) ? 'bg-blue white' : 'bg-nearer-white'"
            :key="plan['id']"
            v-for="plan in plans"
          >
            <div class="mv4 fw6">{{plan['Name']}}</div>
            <div class="mv4">
              <span class="f1">${{plan['Price']}}</span><span class="f6">/user/mo</span>
            </div>
            <div class="mv4 mb3">
              <div>{{plan['Executions']}} executions</div>
              <div class="mt2">{{plan['Members']}} </div>
              <div class="mt2">{{plan['Teams']}} </div>
            </div>
            <div class="mv3 pt2 pb1">
              <div v-if="isCurrentPlan(plan, current_plan_id)">
                <i class="el-icon-success f2" style="color: #fff"></i>
              </div>
              <el-button
                type="primary"
                class="w-100 mw5 ttu fw6"
                @click="selectPlan(plan)"
                v-else-if="isPlanGreater(plan, current_plan)"
              >
                Select Plan
              </el-button>
              <el-button
                plain
                class="w-100 ttu fw6"
                @click="selectPlan(plan)"
                v-else
              >
                Select Plan
              </el-button>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <div class="mb3 f7 silver ttu fw6">Your Current Plan</div>
        <div class="blankslate" v-if="!hasPlan(current_plan_id)">
          <em>No plan has been selected</em>
          <div class="mt3">
            <el-button
              type="primary"
              class="ttu fw6"
              @click="is_editing_plan = true"
            >
              Choose a plan
            </el-button>
          </div>
        </div>
        <div
          class="mv2 f6 br2 pv2 ph3 bg-nearer-white ba b--black-05"
          v-else
        >
          <div class="flex flex-column flex-row-l items-center justify-between">
            <div class="ph2 pv2 f4 fw6 tc">{{current_plan['Name']}}</div>
            <div class="ph2 pv2 tc">
              <div>{{current_plan['Executions']}} executions</div>
            </div>
            <div class="ph2 pv2">
              <span class="f1">${{current_plan['Price']}}</span><span class="f6">/user/mo</span>
            </div>
            <div class="ph2 pv2">
              <el-button
                type="primary"
                class="ttu fw6"
                @click="is_editing_plan = true"
              >
                Change Plan
              </el-button>
            </div>
          </div>
        </div>
        <div class="tr">
          <FreeTrialNotice class="mt2 f7 dark-green" />
        </div>
      </div>
      <div class="mt4 mb3 f7 silver ttu fw6" v-if="is_editing_plan || is_editing_seats">Choose the number of seats</div>
      <div class="mt4 mb3 f7 silver ttu fw6" v-else>Number of seats</div>
      <p class="f6">
        You have <strong>{{plan_info.seat_cnt}} seat(s)</strong> on your current plan.
        <el-button
          type="text"
          style="border: none; padding: 0"
          @click="is_editing_seats = true"
          v-show="!is_editing_plan && !is_editing_seats"
        >
          Manage seats...
        </el-button>
      </p>
      <el-form
        class="mt3 el-form--cozy el-form__label-tiny"
        :model="$data"
        @submit.prevent.native
        v-if="is_editing_plan || is_editing_seats"
      >
        <el-form-item
          key="seat_options"
          prop="seat_options"
        >
          <el-select
            placeholder="Choose the number of seats"
            v-model="edit_plan_info.seat_cnt"
          >
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in seat_options"
            />
          </el-select>
        </el-form-item>
        <div class="mt4" v-show="is_editing_plan || is_editing_seats">
          <table class="f6 w-100">
            <thead class="ttu">
              <tr>
                <th class="bb b--black-10 tl w-60">Description</th>
                <th class="bb b--black-10 tr">Qty</th>
                <th class="bb b--black-10 tr">Unit Price</th>
                <th class="bb b--black-10 tr">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="bb b--black-10 tl w-60">{{current_plan['Name']}} Plan</td>
                <td class="bb b--black-10 tr">{{edit_plan_info.seat_cnt}}</td>
                <td class="bb b--black-10 tr">${{unit_price}}</td>
                <td class="bb b--black-10 tr">${{total_amount}}</td>
              </tr>
              <tr class="subtotal">
                <td class="tl w-60"></td>
                <td class="tr"></td>
                <td class="tr">Subtotal</td>
                <td class="tr">${{total_amount}}</td>
              </tr>
              <tr class="total">
                <td class="tl w-60"></td>
                <td class="tr"></td>
                <td class="tr b">Total</td>
                <td class="tr b">${{total_amount}}</td>
              </tr>
            </tbody>
          </table>
          <ButtonBar
            class="mt4"
            :submit-button-text="'Save Changes'"
            @cancel-click="cancelEdit"
            @submit-click="updatePlan"
          />
        </div>
      </el-form>
    </div>
  </div>
</template>

<script>
  import api from '@/api'
  import plans from '@/data/usage-plans.yml'
  import { isProduction } from '@/utils'
  import Spinner from 'vue-simple-spinner'
  import FreeTrialNotice from '@/components/FreeTrialNotice'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultPlanInfo = () => {
    return {
      subscription_id: '',
      subscription_item_id: '',
      plan_id: '',
      seat_cnt: 1
    }
  }

  const getDefaultState = () => {
    var my_plans = _.filter(plans, (p) => { return p['id'] != 'enterprise' })

    return {
      is_fetching: false,
      is_editing_plan: false,
      is_editing_seats: false,
      seat_options,
      plans: my_plans,
      plan_info: getDefaultPlanInfo(),
      edit_plan_info: getDefaultPlanInfo(),
      plan_error: '',
    }
  }

  var seat_options = []
  for (var i = 1; i < 100; ++i) {
    seat_options.push({ label: '' + i, val: i })
  }

  export default {
    components: {
      Spinner,
      FreeTrialNotice,
      ButtonBar
    },
    data() {
      return getDefaultState()
    },
    computed: {
      current_plan_id() {
        return _.get(this.edit_plan_info, 'plan_id', '')
      },
      current_plan() {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan = _.find(this.plans, (p) => {
          return p[plan_key] == this.current_plan_id
        })

        return _.defaultTo(plan, {})
      },
      unit_price() {
        var price = this.current_plan['Price']
        return price.toFixed(2)
      },
      total_amount() {
        var qty = this.edit_plan_info.seat_cnt
        var price = this.unit_price * qty
        return price.toFixed(2)
      }
    },
    mounted() {
      this.fetchPlan()
    },
    methods: {
      fetchPlan() {
        this.is_fetching = true

        api.fetchPlan().then(response => {
          this.plan_info = _.assign({}, response.data)
          this.edit_plan_info = _.assign({}, response.data)
          this.plan_error = ''
        }).catch(error => {
          this.plan_info = getDefaultPlanInfo()
          this.edit_plan_info = getDefaultPlanInfo()
          this.plan_error = JSON.stringify(error)
        }).finally(() => {
          this.is_fetching = false
        })
      },
      hasPlan(plan_id) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan_ids = _.map(this.plans, p => p[plan_key])

        return plan_ids.indexOf(plan_id) != -1
      },
      isCurrentPlan(plan, plan_id) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        return plan[plan_key] == plan_id
      },
      isPlanGreater(plan1, plan2) {
        if (plan1 && plan2) {
          return parseFloat(plan1['Price']) > parseFloat(plan2['Price'])
        } else {
          return true
        }
      },
      cancelEdit() {
        this.edit_plan_info = _.assign({}, this.plan_info)
        this.is_editing_seats = false
        this.is_editing_plan = false
      },
      selectPlan(plan) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan_id = plan[plan_key]
        this.edit_plan_info = _.assign({}, this.edit_plan_info, { plan_id })
      },
      updatePlan() {
        var payload = _.omit(this.edit_plan_info, ['subscription_id'])

        api.updatePlan('me', payload).then(response => {
          this.plan_info = _.assign({}, response.data)
          this.edit_plan_info = _.assign({}, response.data)
          this.is_editing_seats = false
          this.is_editing_plan = false
        })
      }
    }
  }
</script>

<style lang="stylus" scoped>
  th,
  td
    padding: 6px 8px
    font-size: .875rem
    line-height: 1.5
  tr:last-child
    td
      padding-bottom: 0
  tr.subtotal
    td
      padding-top: 12px
</style>
