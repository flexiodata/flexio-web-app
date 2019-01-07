<template>
  <div class="flex flex-row items-center">
    <el-pagination
      layout="prev"
      :page-size="pageSize"
      :current-page="currentPage"
      :total="totalCount"
      v-on="$listeners"
    />
    <div class="f6">{{start + 1}} - {{end}} of {{totalCount}}</div>
    <el-pagination
      layout="next"
      :page-size="pageSize"
      :current-page="currentPage"
      :total="totalCount"
      v-on="$listeners"
    />
  </div>
</template>

<script>
  export default {
    props: {
      currentPage: {
        type: Number
      },
      pageSize: {
        type: Number
      },
      totalCount: {
        type: Number
      }
    },
    computed: {
      start() {
        return ((this.currentPage - 1) * this.pageSize)
      },
      end() {
        return Math.min((this.start + this.pageSize - 1) + 1, this.totalCount)
      }
    }
  }
</script>
