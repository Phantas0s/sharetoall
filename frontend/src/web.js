'use strict';

import Vue from 'vue';
import Api from 'common/api';
import Session from 'common/session';
import Alert from 'common/alert';
import Event from 'pubsub-js';
import Vuetify from 'vuetify';
import '../styles/sass/web.scss';

Vue.prototype.$alert = Alert;
Vue.prototype.$event = Event;
Vue.prototype.$session = new Session(window.localStorage);
Vue.prototype.$api = Api;

Vue.use(Vuetify,{
    theme: {
        primary: '#3A992E',
        secondary: '#b0bec5',
        accent: '#8c9eff',
        error: '#b71c1c',
    },
});

/* eslint-disable no-unused-vars */
const app = new Vue({
    el: '#app',
});
