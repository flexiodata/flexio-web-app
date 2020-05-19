<template>
  <div>
    <p class="tc f3 lh-title">Your spreadsheet template is ready to go! </p>
    <p class="tc">Would you like to open this template in Google Sheets or Microsoft Excel?</p>
    <div class="flex-l flex-row-l">
      <div class="flex-fill">
        <el-button
          class="w-100 fw6"
          plain
          :disabled="gsheetsSpreadsheetId.length == 0"
          @click="$emit('target-click', 'gsheets', gsheetsSpreadsheetId)"
        >
          <div class="flex flex-row items-center justify-center">
            <img src="../assets/icon/icon-google-sheets-128.png" alt="Google Sheets" style="height: 32px" />
            <div class="ml2 fw6 f5">Google Sheets</div>
          </div>
        </el-button>
        <div
          class="tc mt2 f6 i"
          v-if="gsheetsSpreadsheetId.length == 0"
        >
          Google Sheets template coming soon!
        </div>
      </div>

      <div class="flex-fill pt3 pt0-l pl4-l">
        <el-button
          class="w-100 fw6"
          plain
          :disabled="excelDownloadUrl.length == 0"
          @click="$emit('target-click', 'excel', excelDownloadUrl)"
        >
          <div class="flex flex-row items-center justify-center">
            <img src="../assets/icon/icon-excel-128.png" alt="Microsoft Excel" style="height: 32px" />
            <div class="ml3 fw6 f5">Microsoft Excel 365</div>
          </div>
        </el-button>
        <div
          class="tc mt2 f6 i"
          v-if="excelDownloadUrl.length == 0"
        >
          Excel template coming soon!
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      gsheetsSpreadsheetId: {
        type: String,
        default: ''
      },
      excelDownloadUrl: {
        type: String,
        default: ''
      }
    }
  }
</script>
