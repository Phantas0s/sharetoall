<template>
    <div class="dashboard">
        <h1>{{ msg }}</h1>
        <h2>Essential Links</h2>
        <ul>
            <li><a href="https://vuejs.org" target="_blank">Core Docs</a></li>
            <li><a href="https://forum.vuejs.org" target="_blank">Forum</a></li>
            <li><a href="https://gitter.im/vuejs/vue" target="_blank">Gitter Chat</a></li>
            <li><a href="https://twitter.com/vuejs" target="_blank">Twitter</a></li>
            <br>
            <li><a href="http://vuejs-templates.github.io/webpack/" target="_blank">Docs for This Template</a></li>
        </ul>
        <h2>Ecosystem</h2>
        <ul>
            <li><a href="http://router.vuejs.org/" target="_blank">vue-router</a></li>
            <li><a href="http://vuex.vuejs.org/" target="_blank">vuex</a></li>
            <li><a href="http://vue-loader.vuejs.org/" target="_blank">vue-loader</a></li>
            <li><a href="https://github.com/vuejs/awesome-vue" target="_blank">awesome-vue</a></li>
        </ul>

        <md-button @click.native="open()" class="md-primary md-raised">Open Snackbar</md-button>
        <md-button @click.native="showNotification()" class="md-primary md-raised">Show notification</md-button>
        <md-button @click.native="logout()" class="md-primary md-raised">Logout {{ username }}</md-button>

        <md-snackbar md-position="bottom center" ref="snackbar" md-duration="4000">
            <span>Connection timeout. Showing limited messages.</span>
            <md-button class="md-accent" md-theme="light-blue" @click.native="$refs.snackbar.close()">Retry</md-button>
        </md-snackbar>
    </div>
</template>

<script>
    export default {
        name: 'dashboard',
        data() {
            return {
                msg: 'Welcome to Sharetoall',
                username: this.$session.getFullName(),
            };
        },
        methods: {
            open() {
                this.$refs.snackbar.open();
            },

            showNotification() {
                this.$alert.success('Yeah wuwuwu');
            },

            logout() {
                this.$session.logout();
            },
        }
    };
</script>

<style scoped>
    h1, h2 {
        font-weight: normal;
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        display: inline-block;
        margin: 0 10px;
    }
</style>
