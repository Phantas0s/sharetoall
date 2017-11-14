<template>
    <div class="dashboard">
        <h1>{{ msg }} {{username}}</h1>
        <ul id="networks">
            <li v-for="network in networks">
                <button
                    class="md-icon-button"
                    v-bind:data-slug="network.networkSlug"
                    v-bind:class="[
                        {'connected': networkHasToken(network),
                        'active': isNetworkRegistered(network)}
                    ]"
                    @click="toggleNetwork">
                        {{ network.networkName }}
                </button>
            </li>
        </ul>
        <form id="form-message">
            <label for="message">Message(maximum 140 characters)</label>
            <textarea id="message" class="form-control" v-on:keyup="countdown" v-model="message" placeholder="" name="message"></textarea>
            <p class='text-right text-small' v-bind:class="{'text-danger': hasError }">{{remainingCount}}</p>
            <button @click="sendMessage">Send</button>
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
                networks: '',

                maxCount: 140,
                remainingCount: 140,
                message: '',
                messageError: false
            };
        },
        methods: {
            showNotification() {
                this.$alert.warning('hello this is a warning');
            },
            networkHasToken(network) {
                return network.userNetworkTokenKey != null;
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
                    const networkSlug = el.dataset.slug;

                    this.$api.get(`connect/${networkSlug}`).then(response => {
                        window.location = response.data;
                    }, error => {
                        event.target.classList.toggle('active');
                    });
                }
            },
            sendMessage(event) {
                event.preventDefault();

                const networks = document.getElementById('networks');
                const connectedNetworks = networks.getElementsByClassName('connected active');
                const message = document.getElementById('message').value;

                const networkSlugs = Array.from(connectedNetworks, network => network.dataset.slug);
                this.$api.post(`message`, {networkSlugs: networkSlugs, message: message}).then(response => {
                    this.$alert.success('The message have been sent!');
                }, error => {
                    this.$alert.error('Error: ' + error);
                });
            },
            countdown(even) {
                this.remainingCount = this.maxCount - this.message.length;
                this.messageError = this.remainingCount < 0;
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
        display: flex;
    }

    #networks > li {
    }

    button {
        background: grey;
    }
    .active {
        background: green;
    }
    .dashboard {
        margin: 0 auto;
        width: 50%;
    }
    #message {
        width: 50%;
    }
    label{
        display:block;
    }
    .text-small {
        font-size: 0.9em;
    }
    .text-alert {
        color: red;
    }
</style>
