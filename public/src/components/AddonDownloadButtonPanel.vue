<template>
  <div class="flex-l flex-row-l nl3 nr3">
    <div class="flex-fill mv4 mv0-l mh3 pa4 bg-nearer-white br3">
      <div class="flex flex-row items-center justify-center">
        <img src="../assets/icon/icon-google-sheets-128.png" alt="Google Sheets" style="height: 48px" />
        <div class="ml2 fw6 f4">Google Sheets</div>
      </div>
      <div class="center mw6 mt4">
        <el-button
          class="w-100 ttu fw6"
          plain
          @click="openGoogleSheetsAddonDownload"
        >
          <span class="ph2">Get the Google Sheets add-on</span>
        </el-button>
      </div>
    </div>

    <div class="flex-fill mv4 mv0-l mh3 pa4 bg-nearer-white br3">
      <div class="flex flex-row items-center justify-center">
        <img src="../assets/icon/icon-excel-128.png" alt="Microsoft Excel" style="height: 48px" />
        <div class="ml3 fw6 f4">Microsoft Excel 365</div>
      </div>
      <div class="center mw6 mt4">
        <el-button
          class="w-100 ttu fw6"
          plain
          @click="openExcelAddonDownload"
        >
          <span class="ph2">Get the Excel add-in</span>
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      openLinksInNewWindow: {
        type: Boolean,
        default: false
      }
    },
    methods: {
      openGoogleSheetsAddonDownload() {
        if (this.openLinksInNewWindow) {
          window.open('https://gsuite.google.com/marketplace/app/flexio/919566304535', '_blank')
        } else {
          window.location = 'https://gsuite.google.com/marketplace/app/flexio/919566304535'
        }
      },
      openExcelAddonDownload() {
        if (this.openLinksInNewWindow) {
          window.open('https://appsource.microsoft.com/en-us/product/office/WA200000394', '_blank')
        } else {
          window.location = 'https://appsource.microsoft.com/en-us/product/office/WA200000394'
        }
      }
    }
  }
</script>
