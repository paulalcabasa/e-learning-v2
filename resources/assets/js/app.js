require('./bootstrap');

window.Vue = require('vue');
window.Vuetify = require('vuetify');
window.Vuex = require('vuex');
window.VueScrollTo = require('vue-scrollto');

import 'material-design-icons-iconfont/dist/material-design-icons.css';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuex);
Vue.use(Vuetify);
Vue.use(VueScrollTo);