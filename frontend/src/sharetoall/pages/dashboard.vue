<template>
    <v-app light>
        <v-layout app>
            <v-flex xs2 offset-xs3>
                <v-card>
                    <v-navigation-drawer width="100%" permanent clipped app light>
                        <v-toolbar flat>
                            <v-list>
                                <v-list-tile>
                                    <v-list-tile-title class="title">
                                        Social networks
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-toolbar>
                        <v-divider></v-divider>
                        <v-list id="networks">
                            <v-list-tile
                                v-for="(network, key) in networks"
                                tag="div"
                                :key="key"
                                :class="[
                                    {
                                        'disabled': !networkHasToken(network),
                                        'connected': networkHasToken(network),
                                    }
                                ]"
                                @click="toggleNetwork">
                                <v-list-tile-avatar>
                                    <v-btn fab small
                                        :data-slug="network.networkSlug"
                                        :class="[ isNetworkRegistered(network) ? selectClass + ' selected' : '' ]">
                                        <i :class="getSocialIcon(network.networkSlug)"></i>
                                    </v-btn> </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        {{network.networkSlug}}
                                    </v-list-tile-title>
                                    <v-list-tile-sub-title v-if="isNetworkRegistered(network)">
                                        Connected
                                    </v-list-tile-sub-title>
                                    <v-list-tile-sub-title v-else>
                                        Click to connect
                                    </v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-navigation-drawer>
                </v-card>
            </v-flex>
            <v-toolbar dense app clipped-left fixed>
                <h1>Sharetoall</h1>
                <v-spacer></v-spacer>
                <v-btn clipped-right @click.native="logout()">Logout</v-btn>
                <v-btn @click.native="showNotification()">Show notification</v-btn>
            </v-toolbar>
            <v-flex xs4 class="ma-3 mt-5">
                <v-card class="pa-3" app>
                    <v-form id="form-message">
                        <v-text-field
                            id="message"
                            name="message"
                            label="Message"
                            value=""
                            :rules="[(v) => v.length <= 280 || 'Max 280 characters']"
                            :counter="280"

                            multi-line
                        ></v-text-field>
                        <v-btn @click.native="sendMessage">
                            Share
                            <v-icon right>send</v-icon>
                        </v-btn>
                    </v-form>

                </v-card>
            </v-card>
            </v-flex>
        </v-layout>
    </v-app>
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
            'networks': '',
            'userId': this.$session.getUser().userId,
            'username': this.$session.getFullName(),
            'selectClass' :'primary'
        };
    },
    methods: {
        showNotification() {
            this.$alert.warning('hello this is a warning');
        },
        networkHasToken(network) {
            return network.userNetworkTokenKey != null;
        },
        getSocialIcon(slug){
            return "pe-so-" + slug;
        },
        logout() {
            this.$session.logout();
        },
        isNetworkRegistered(network) {
            return network.userId == this.userId;
        },
        toggleNetwork(event) {

            const el = event.target;
            const listItem = el.closest("li");
            const button = listItem.querySelector('button');
            const listTile = el.closest(".list__tile");

            button.classList.toggle(this.selectClass);
            button.classList.toggle('selected');

            if(!listItem.classList.contains('connected') && button.classList.contains(this.selectClass)) {
                const networkSlug = listTile.dataset.slug;

                this.$api.get(`connect/${networkSlug}`).then(response => {
                    window.location = response.data;
                }, error => {
                    button.classList.toggle(this.selectClass);
                    button.classList.toggle('selected');
                });
            }
        },
        sendMessage(event) {
            event.preventDefault();

            const networks = document.getElementById('networks');
            const connectedNetworks = networks.querySelectorAll('.selected');
            const message = document.getElementById('message').value;

            const networkSlugs = Array.from(connectedNetworks, network => network.dataset.slug);

            this.$api.post(`message`, {networkSlugs: networkSlugs, message: message}).then(response => {
                this.$alert.success('The message have been sent!');
            }, error => {
                this.$alert.error('Error: ' + error);
            });
        },
    }
};
</script>

<style scoped>

label{
    display:block;
}

.text-small {
    font-size: 0.9em;
}

.text-alert {
    color: red;
}

.no-padding {
    padding: 0;
}
.disabled div {
    opacity: 0.7;
}

/* #networks .list__tile__sub-title{
    color: green;
} */

#networks .disabled .list__tile__sub-title{
    color: red;
}
.selected {
    background-color: green;
}
</style>
