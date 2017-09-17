<template>
    <div class="dashboard">
        <h1>{{ msg }} {{username}}</h1>
        <ul id="networks">
            <li v-for="network in networks">
                <button
                    v-bind:data-network-slug="network.networkSlug"
                    v-bind:class="[
                        {'connected': networkHasToken(network),
                        'active': isNetworkRegistered(network)}
                    ]"
                    @click="toggleNetwork">
                        {{ network.networkName }}
                </button>
            </li>
        </ul>
        <form>
            <label for="message">Message</label>
            <textarea id="message" name="message"></textarea>
            <button>Send</button>
        </form>
        <md-button @click.native="showNotification()" class="md-primary md-raised">Show notification</md-button>
        <md-button @click.native="logout()" class="md-primary md-raised">Logout {{ username }}</md-button>
        <md-button @click.native="test()" class="md-primary md-raised">test</md-button>
    </div>
</template>

<script>
    export default {
        name: 'dashboard',
        created () {
            this.$network.findUserNetwork(this.userId).then(response => {
                this.networks = response;
            });
        },
        data() {
            return {
                msg: 'Welcome to Sharetoall',
                userId: this.$session.getUser().userId,
                username: this.$session.getFullName(),
                //For later: get list of network + networks the user already subscribed to
                //networks: $network.getNetworkList(this.$session.getUserId());
                networks: '',
            };
        },
        methods: {
            showNotification() {
                this.$alert.warning('hello this is a warning');
            },
            networkHasToken(network) {
                return network.userNetworkToken != null;
            },
            logout() {
                this.$session.logout();
            },
            isNetworkRegistered(network) {
                return network.userId == this.userId;
            },
            toggleNetwork(event) {
                const el = event.target;
                el.classList.toggle('active');

                if(!event.target.classList.contains('connected') && event.target.classList.contains('active')) {
                    const networkSlug = el.dataset.networkSlug;

                    this.$api.post('connect', {networkSlug: networkSlug}).then(response => {
                        console.log('success');
                    }, error => {event.target.classList.toggle('active');
                    });
                }
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
