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
    data: {
        loginModal: false,
        registerModal: false,
        confirmModal: false,
        resetModal: false,
        confirmResetModal: false,
        newPasswordModal: true,
        newPasswordConfirmModal: false,

        loginEmail: '',
        loginPass: '',
        resetPasswordEmail: '',

        userEmail: '',
        userPassword: '',
        userPasswordConfirm: '',
        newPassword: '',
        newPasswordConfirm: '',
        userNewsletter: false,

        session_token: '',

        loginPassVisible: false,
        userPasswordVisible: false,
        userPasswordConfirmVisible: false,
        newPasswordVisible: false,
        newPasswordConfirmVisible: false,
    },
    created() {
        this.email = this.$session.getEmail();
        this.session_token = this.$session.getToken();
    },
    methods: {
        logout: function (e) {
            this.$session.deleteToken();
            this.$refs.form.submit();
        },

        login: function (e) {
            e.preventDefault();

            Api.post('session', {loginEmail: this.loginEmail, loginPass: this.loginPass}).then(response => {
                this.$refs.login_session_token.value = response.data.token;

                this.$session.setToken(response.data.token);
                this.$session.setUser(response.data.user);

                this.$refs.loginForm.submit();
            }, error => {});
        },

        register: function(e) {
            e.preventDefault();

            let form = {
                userEmail: this.userEmail,
                userPassword: this.userPassword,
                userPasswordConfirm: this.userPasswordConfirm,
                userNewsletter: this.userNewsletter,
            };

            Api.post('register', {form: form}).then(response => {
                this.registerModal = false;
                this.confirmModal = true;
            }, error => {});
        },

        resetPassword: function(e) {
            e.preventDefault();

            Api.post('auth', {resetPasswordEmail: this.resetPasswordEmail}).then(response => {
                this.resetModal = false;
                this.confirmResetModal = true;
            }, error => {});
        },

        changePassword: function(e) {
            e.preventDefault();

            let form = {
                newPassword: this.newPassword,
                newPasswordConfirm: this.newPasswordConfirm,
            };
            let resetToken = document.getElementById('resetTokenField').value;

            Api.post(`auth/${resetToken}/reset`, {form: form}).then(response => {
                this.newPasswordModal = false;
                this.newPasswordConfirmModal = true;
                this.$session.deleteToken();
            }, error => {
            });
        },
    },
});
