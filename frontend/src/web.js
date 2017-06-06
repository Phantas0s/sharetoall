import Vue from 'vue';
import Api from 'common/api';
import VueMaterial from 'vue-material';
import Session from 'common/session';
import Alert from 'common/alert';
import Event from 'pubsub-js';

Vue.prototype.$alert = Alert;
Vue.prototype.$event = Event;
Vue.prototype.$session = new Session(window.localStorage);
Vue.prototype.$api = Api;

Vue.use(VueMaterial);

/* eslint-disable no-unused-vars */
const app = new Vue({
    el: '#app',
    data: {
        form: {},
        password: '',
        session_token: '',
    },
    created() {
        this.email = this.$session.getEmail();
        this.session_token = this.$session.getToken();
    },
    methods: {
        open() {
            this.$refs.snackbar.open();
        },

        showNotification() {
            this.$alert.success('Yeah yeah yeahhhhh');
        },

        logout() {
            this.$session.logout();
        },
        register: function (e) {
            e.preventDefault();

            Api.post('registration', this.form).then(response => {
                window.location = '/register/confirm';
            }, error => {});
        },
        domain: function (e) {
            e.preventDefault();

            Api.post('domain', this.form).then(response => {
                window.location = response.data.url;
            }, error => {});
        },
    },
});
