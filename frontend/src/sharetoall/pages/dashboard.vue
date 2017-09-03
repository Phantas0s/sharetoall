<template>
    <div class="dashboard">
        <h1>{{ msg }} {{username}}</h1>
        <ul id="networks">
            <li v-for="network in networks">
                <button :class=network.class @click="toggleNetwork">{{ network.text }}</button>
            </li>
        </ul>
        <form>
            <label for="message">Message</label>
            <input id="message" name="message" />
            <button>Send</button>
        </form>
        <md-button @click.native="showNotification()" class="md-primary md-raised">Show notification</md-button>
        <md-button @click.native="logout()" class="md-primary md-raised">Logout {{ username }}</md-button>
    </div>
</template>

<script>
    export default {
        name: 'dashboard',
        data() {
            return {
                msg: 'Welcome to Sharetoall',
                username: this.$session.getFullName(),
                //For later: get list of network + networks the user already subscribed to
                //networks: $network.getNetworkList(this.$session.getUserId());
                networks: [
                    { text: 'Twitter', class: 'twitter' },
                    { text: 'Linkedin', class: 'linkedin' },
                ]
            };
        },
        methods: {
            showNotification() {
                this.$alert.success('Yeah wuwuwu');
                this.$alert.warning('Yeah wuwuwu');
            },

            logout() {
                this.$session.logout();
            },
            toggleNetwork(event) {
                this.$api.post('connect', {network: this.network}).then(response => {
                    event.target.classList.toggle('active');
                }, error => {event.target.classList.toggle('active');
});
            }
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

    #networks {
        width: 10%;
        background: white;
    }

    #networks > li {
        width: 100%;
        display: block;
        float:clear;
    }

    button {
        background: grey;
    }
    .active {
        background: green;
    }
</style>
