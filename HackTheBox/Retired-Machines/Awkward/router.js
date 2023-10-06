__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue_router__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-router */ "./node_modules/vue-router/dist/vue-router.mjs");
/* harmony import */ var vue_cookie_next__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue-cookie-next */ "./node_modules/vue-cookie-next/dist/vue-cookie-next.esm.js");
/* harmony import */ var _Base_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../Base.vue */ "./src/Base.vue");
/* harmony import */ var _HR_vue__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../HR.vue */ "./src/HR.vue");
/* harmony import */ var _Dashboard_vue__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../Dashboard.vue */ "./src/Dashboard.vue");
/* harmony import */ var _Leave_vue__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../Leave.vue */ "./src/Leave.vue");







var routes = [{
  path: "/",
  name: "base",
  component: _Base_vue__WEBPACK_IMPORTED_MODULE_3__["default"]
}, {
  path: "/hr",
  name: "hr",
  component: _HR_vue__WEBPACK_IMPORTED_MODULE_4__["default"]
}, {
  path: "/dashboard",
  name: "dashboard",
  component: _Dashboard_vue__WEBPACK_IMPORTED_MODULE_5__["default"],
  meta: {
    requiresAuth: true
  }
}, {
  path: "/leave",
  name: "leave",
  component: _Leave_vue__WEBPACK_IMPORTED_MODULE_6__["default"],
  meta: {
    requiresAuth: true
  }
}];
var router = Object(vue_router__WEBPACK_IMPORTED_MODULE_1__["createRouter"])({
  history: Object(vue_router__WEBPACK_IMPORTED_MODULE_1__["createWebHistory"])(),
  routes: routes
});
router.beforeEach(function (to, from, next) {
  if ((to.name == 'leave' || to.name == 'dashboard') && vue_cookie_next__WEBPACK_IMPORTED_MODULE_2__["VueCookieNext"].getCookie('token') == 'guest') {
    //if user not logged in, redirect to login
    next({
      name: 'hr'
    });
  } else if (to.name == 'hr' && vue_cookie_next__WEBPACK_IMPORTED_MODULE_2__["VueCookieNext"].getCookie('token') != 'guest') {
    //if user logged in, skip past login to dashboard
    next({
      name: 'dashboard'
    });
  } else {
    next();
  }
});
/* harmony default export */ __webpack_exports__["default"] = (router);//# sourceURL=[module]
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvcm91dGVyL3JvdXRlci5qcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3NyYy9yb3V0ZXIvcm91dGVyLmpzPzk4ODMiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgY3JlYXRlV2ViSGlzdG9yeSwgY3JlYXRlUm91dGVyIH0gZnJvbSBcInZ1ZS1yb3V0ZXJcIjtcbmltcG9ydCB7IFZ1ZUNvb2tpZU5leHQgfSBmcm9tICd2dWUtY29va2llLW5leHQnXG5pbXBvcnQgQmFzZSBmcm9tICcuLi9CYXNlLnZ1ZSdcbmltcG9ydCBIUiBmcm9tICcuLi9IUi52dWUnXG5pbXBvcnQgRGFzaGJvYXJkIGZyb20gJy4uL0Rhc2hib2FyZC52dWUnXG5pbXBvcnQgTGVhdmUgZnJvbSAnLi4vTGVhdmUudnVlJ1xuXG5jb25zdCByb3V0ZXMgPSBbXG4gIHtcbiAgICBwYXRoOiBcIi9cIixcbiAgICBuYW1lOiBcImJhc2VcIixcbiAgICBjb21wb25lbnQ6IEJhc2UsXG4gIH0sXG4gIHtcbiAgICBwYXRoOiBcIi9oclwiLFxuICAgIG5hbWU6IFwiaHJcIixcbiAgICBjb21wb25lbnQ6IEhSLFxuICB9LFxuICB7XG4gICAgcGF0aDogXCIvZGFzaGJvYXJkXCIsXG4gICAgbmFtZTogXCJkYXNoYm9hcmRcIixcbiAgICBjb21wb25lbnQ6IERhc2hib2FyZCxcbiAgICBtZXRhOiB7XG4gICAgICByZXF1aXJlc0F1dGg6IHRydWVcbiAgICB9XG4gIH0sXG4gIHtcbiAgICBwYXRoOiBcIi9sZWF2ZVwiLFxuICAgIG5hbWU6IFwibGVhdmVcIixcbiAgICBjb21wb25lbnQ6IExlYXZlLFxuICAgIG1ldGE6IHtcbiAgICAgIHJlcXVpcmVzQXV0aDogdHJ1ZVxuICAgIH1cbiAgfVxuXTtcblxuY29uc3Qgcm91dGVyID0gY3JlYXRlUm91dGVyKHtcbiAgaGlzdG9yeTogY3JlYXRlV2ViSGlzdG9yeSgpLFxuICByb3V0ZXMsXG59KTtcblxucm91dGVyLmJlZm9yZUVhY2goKHRvLCBmcm9tLCBuZXh0KSA9PiB7XG4gIGlmKCh0by5uYW1lID09ICdsZWF2ZScgfHwgdG8ubmFtZSA9PSAnZGFzaGJvYXJkJykgJiYgVnVlQ29va2llTmV4dC5nZXRDb29raWUoJ3Rva2VuJykgPT0gJ2d1ZXN0JykgeyAvL2lmIHVzZXIgbm90IGxvZ2dlZCBpbiwgcmVkaXJlY3QgdG8gbG9naW5cbiAgICBuZXh0KHsgbmFtZTogJ2hyJyB9KVxuICB9XG4gIGVsc2UgaWYodG8ubmFtZSA9PSAnaHInICYmIFZ1ZUNvb2tpZU5leHQuZ2V0Q29va2llKCd0b2tlbicpICE9ICdndWVzdCcpIHsgLy9pZiB1c2VyIGxvZ2dlZCBpbiwgc2tpcCBwYXN0IGxvZ2luIHRvIGRhc2hib2FyZFxuICAgIG5leHQoeyBuYW1lOiAnZGFzaGJvYXJkJyB9KVxuICB9XG4gIGVsc2Uge1xuICAgIG5leHQoKVxuICB9XG59KVxuXG5leHBvcnQgZGVmYXVsdCByb3V0ZXI7Il0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFIQTtBQU1BO0FBQ0E7QUFDQTtBQUhBO0FBTUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBSkE7QUFTQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFKQTtBQVVBO0FBQ0E7QUFDQTtBQUZBO0FBS0E7QUFDQTtBQUFBO0FBQ0E7QUFBQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQUE7QUFBQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUEiLCJzb3VyY2VSb290IjoiIn0=
//# sourceURL=webpack-internal:///./src/router/router.js

