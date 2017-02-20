/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	var App = __webpack_require__(1);

	new Vue({
	  el: '#app',
	  components: {
	    'app': App
	  }
	})


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var AppNavbar = __webpack_require__(2);
	var AppContent = __webpack_require__(3);

	var initializeJquery = __webpack_require__(7);
	initializeJquery(jQuery);

	module.exports = {
	  template: '\
	    <div>\
	      <app-navbar></app-navbar>\
	      <app-content style="padding-top: 60px"></app-content>\
	    </div>\
	  ',
	  components: {
	    'app-navbar': AppNavbar,
	    'app-content': AppContent
	  }
	}


/***/ },
/* 2 */
/***/ function(module, exports) {

	module.exports = {
	  template: '\
	    <nav class="dt w-100 bg-white bb b--black-20 border-box pa2 ph4-ns fixed top-0">\
	      <div class="dtc v-mid w-25">\
	        <a soft href="/" class="dib link" title="Home">\
	          <img src="/dist/img/logo/logo-flexio-navbar.png" class="dib" alt="Flex.io">\
	        </a>\
	      </div>\
	      <div class="dtc v-mid w-75 tr">\
	        <a soft href="#" class="link reverse-dim dib f6 f6-ns ttu b dark-gray mr2 mr4-ns"><i18n>Sign in</i18n></a>\
	        <a soft href="#" class="link reverse-dim dib f6 f6-ns ttu b br1 white bg-orange ph3 pv2"><i18n>Sign up for free</i18n></a>\
	      </div>\
	    </nav>\
	  '
	}


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var Timer = __webpack_require__(4);
	var ButtonCounterExample = __webpack_require__(5);

	module.exports = {
	  template: '\
	    <div>\
	      <div class="flex justify-center mt3">\
	        <timer></timer>\
	      </div>\
	      <div class="flex justify-center mt3">\
	        <div class="tc ba b--black-20 br2 pa pv4 ph5">\
	          <button-counter-example></button-counter-example>\
	        </div>\
	      </div>\
	    </div>\
	  ',
	  components: {
	    'timer': Timer,
	    'button-counter-example': ButtonCounterExample
	  }
	}


/***/ },
/* 4 */
/***/ function(module, exports) {

	module.exports = {
	  template: '<p>This example was started <b><span>{{seconds}}</span> seconds</b> ago.</p>',
	  data: function () {
	    return {
	      elapsed: 0
	    }
	  },
	  computed: {
	    seconds: function() {
	      var elapsed = Math.round(this.elapsed / 10);
	      var seconds = (elapsed / 100).toFixed(2);

	      return seconds;
	    }
	  },
	  mounted: function() {
	    this.start = new Date();
	    this.timer = setInterval(this.tick, 47);
	  },
	  beforeDestroy: function() {
	    clearInterval(this.timer);
	  },
	  methods: {
	    tick: function() {
	      this.elapsed = new Date() - this.start;
	    }
	  }
	}


/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var ButtonCounter = __webpack_require__(6);

	module.exports = {
	  template: '\
	    <div>\
	      <p class="mt0">{{ total }}</p>\
	      <button-counter v-on:increment="incrementTotal"></button-counter>\
	      <button-counter v-on:increment="incrementTotal"></button-counter>\
	    </div>\
	  ',
	  components: {
	    'button-counter': ButtonCounter
	  },
	  data: function () {
	    return {
	      total: 0
	    }
	  },
	  methods: {
	    incrementTotal: function () {
	      this.total += 1
	    }
	  }
	}


/***/ },
/* 6 */
/***/ function(module, exports) {

	module.exports = {
	  template: '<a btn href="#" class="link reverse-dim dib f5 br1 white bg-blue ph3 pv2" v-on:click="increment">{{ counter }}</a>',
	  data: function () {
	    return {
	      counter: 0
	    }
	  },
	  methods: {
	    increment: function () {
	      this.counter += 1
	      this.$emit('increment')
	    }
	  }
	}


/***/ },
/* 7 */
/***/ function(module, exports) {

	module.exports = function($) {
	  $(document).on('click', 'a[btn], a[soft]', function(evt) { evt.preventDefault(); })
	}


/***/ }
/******/ ]);
