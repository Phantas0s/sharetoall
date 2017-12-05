'use strict';

import Vue from 'vue';
import Router from 'vue-router';
import '../styles/sass/sharetoall.scss';
import App from 'sharetoall/app.vue';
import routes from 'sharetoall/routes';
import Api from 'common/api';
import Network from 'common/network';

// import PantaComponents from 'component/panta-components';
import Alert from 'common/alert';
import Session from 'common/session';
import Event from 'pubsub-js';

import Vuetify from 'vuetify';

const session = new Session(window.localStorage);

if(!session.isUser()) {
    window.location = '/auth/login';
    //To stop the execution
    throw 'Requires authentication';
}

Vue.use(Router);
Vue.use(Vuetify,{
    theme: {
        primary: '#3A992E',
        secondary: '#b0bec5',
        accent: '#8c9eff',
        error: '#b71c1c',
    },
});

Vue.prototype.$event = Event;
Vue.prototype.$alert = Alert;
Vue.prototype.$session = session;
Vue.prototype.$api = Api;
Vue.prototype.$network = new Network();

const router = new Router({
    routes,
    mode: 'hash',
    saveScrollPosition: true,
});

/* eslint-disable no-unused-vars */
const app = new Vue({
    el: '#app',
    router,
    render: h => h(App),
});
