import Vue from 'vue';
import '../styles/sass/pages/web/_login.scss';
import Api from 'common/api';
import VueMaterial from 'vue-material';
import Session from 'common/session';
// import SharetoallComponents from 'component/sharetoall-components';
import Alert from 'common/alert';
import Event from 'pubsub-js';

Vue.prototype.$alert = Alert;
Vue.prototype.$event = Event;
Vue.prototype.$session = new Session(window.localStorage);
Vue.prototype.$api = Api;

Vue.use(VueMaterial);

/* eslint-disable no-unused-vars */
const app = new Vue({
    el: '#login',
    data: {
        email: '',
        password: '',
        session_token: '',
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

            Api.post('session', {email: this.email, password: this.password}).then(response => {
                this.$refs.session_token.value = response.data.token;

                this.$session.setToken(response.data.token);
                this.$session.setUser(response.data.user);

                this.$refs.form.submit();
            }, error => {});
        },
    },
});
