# Flex.io Web Application

## This build process is based on vue-loader. For detailed explanation on how things work, consult the [docs for vue-loader](http://vuejs.github.io/vue-loader).

### We're using the following main libraries in the development of the front-end Flex.io web app

* Vue.js (https://vuejs.org/) -- reactive UI library akin to React which utilizes a virtual DOM
* Vuex (https://vuex.vuejs.org/en/) -- local store for state management
* Vue-Router (https://router.vuejs.org/en) -- map URL routes to Vue.js components
* Vue-Resource (https://github.com/pagekit/vue-resource) -- service for making web requests and handling responses using XHR or JSONP

### Build Setup

* Download and install node.js (https://nodejs.org/en/download/current/) -- Windows Installer (.msi) / 64-bit
* Download and install yarn (https://yarnpkg.com/en/docs/install)
* If you still wish to use npm, you're welcome to... however, yarn is now preferred over npm: Google and Facebook collaborated to create it; it's faster and less bug prones -- https://www.sitepoint.com/yarn-vs-npm
* Both the yarn calls and npm calls are shown below in the bash script, however, when running commands, 'npm run' and 'yarn run' are interchangeable, so we'll only include examples using yarn below
* From the 'c:\fxsite\flexio\public' folder, run the following commands to get up and running:

  ``` bash
  # install dependencies (either command)
  yarn
  npm install

  # serve with hot reload at localhost:8080
  yarn run dev

  # build for development at localhost (app.js, vendor.js, etc. will not include hash suffix)
  yarn run build

  # 'build-debug' is an alias for the above command
  yarn run build-debug

  # build for production with minification (app.js, vendor.js, etc. will include hash suffix)
  yarn run build-production

  # 'build-release' is an alias for the above command
  yarn run build-release
  ```

### FINAL NOTES

* You may want to add an exclusion to Windows Defender for your 'c:\fxsite\flexio\public' folder as it greatly slows down the package manager (since each file that is pulled from NPM has to be run through Windows Defender)
