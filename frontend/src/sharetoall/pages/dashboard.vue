<template>
    <v-app light>
        <v-container app>
            <v-layout v-bind="binding" app>
                <v-flex lg4 xl2 offset-md1 offset-lg3>
                    <v-card>
                        <v-navigation-drawer width="100%" permanent clipped app light>
                            <v-toolbar flat>
                                <v-list>
                                    <v-list-tile>
                                        <v-list-tile-title class="title">
                                            Social media
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
                                            :class="[ isNetworkRegistered(network) ? selectClass + ' selected' : '' ]"
                                            :loading="getLoading(network.networkSlug)"
                                        >
                                            <i :class="getSocialIcon(network.networkSlug)"></i>
                                            <span slot="loader" class="network-loader">
                                                <v-icon light>cached</v-icon>
                                            </span>
                                        </v-btn>
                                    </v-list-tile-avatar>
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
                    <h1><a href="/" class="black--text" title="home"><img class="hidden-xs-only" src="/assets/img/logo.png">Sharetoall <small class="hidden-xs-only">beta</small></a></h1>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn flat color="secondary" id="button-logout" clipped-right @click.native="logout()">Logout</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-flex lg4 class="ma-3 mt-5">
                    <v-card class="pa-3" app>
                        <v-form id="form-message">
                            <v-text-field
                                id="message"
                                color="secondary"
                                name="message"
                                label="Message"
                                value=""
                                :rules="[(v) => v.length <= 280 || 'Max 280 characters']"
                                :counter="280"

                                multi-line
                            ></v-text-field>
                            <v-btn
                                id="share"
                                @click.native="sendMessage"
                                :loading="messageLoading"
                                :disabled="messageLoading"
                            >
                                Share
                                <v-icon right>send</v-icon>
                                <span slot="loader">Sharing...</span>
                            </v-btn>
                        </v-form>
                    </v-card>
                </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </v-app>
</template>

<script>
'use strict';

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
            'selectClass' :'primary',

            'messageLoading': false,
            'networkLoading': false,
            'twitterLoading': false,
            'linkedinLoading': false
        };
    },
    methods: {
        networkHasToken(network) {
            return network.userNetworkTokenKey != null;
        },
        getSocialIcon(slug){
            return "pe-so-" + slug;
        },
        getLoading(slug){
            return this[slug + 'Loading'];
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
                const networkSlug = button.dataset.slug;
                this[networkSlug + 'Loading'] = true;

                this.$api.get(`connect/${networkSlug}`).then(response => {
                    this[networkSlug + 'Loading'] = true;
                    window.location = response.data;
                }, error => {
                    this.networkLoading = false;
                    button.classList.toggle(this.selectClass);
                    button.classList.toggle('selected');
                });
            }
        },
        sendMessage(event) {
            event.preventDefault();
            this.messageLoading = true;

            const networks = document.getElementById('networks');
            const connectedNetworks = networks.querySelectorAll('.selected');
            const message = document.getElementById('message').value;

            const networkSlugs = Array.from(connectedNetworks, network => network.dataset.slug);

            for (var i = 0; i < networkSlugs.length; i++) {
                this.$api.post(`message`, {networkSlug: networkSlugs[i], message: message}).then(response => {
                    this.messageLoading = false;
                    this.$alert.success('Your message have been shared on '+response.data.network+'!');
                }, error => {
                    this.messageLoading = false;

                    if (error.response.status == 404) {
                        this.$alert.error(error.response.data.message);
                        // disconnect from the network
                    } else {
                        this.$alert.error('Error trying to send a message.');
                    }
                });
            }
        },
    },
    computed: {
        binding () {
          const binding = {}

          if (this.$vuetify.breakpoint.xs) binding.column = true

          return binding
        }
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

#networks .disabled .list__tile__sub-title{
    color: red;
}

main {
    margin: 0;
    margin-top: 10px;
}

.network-loader {
  animation: loader 1s infinite;
  display: flex;
}

@-moz-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}

@-webkit-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}

@-o-keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes loader {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}

small {
    font-size: 0.6em;
    color: #B1A296;
}
</style>
