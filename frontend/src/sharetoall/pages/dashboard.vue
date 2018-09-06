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
                                            'list-item disabled': !isNetworkRegistered(network),
                                            'list-item connected': isNetworkRegistered(network),
                                        }
                                    ]"
                                    @click=""
                                    >
                                    <v-list-tile-action @click="toggleNetwork">
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
                                    </v-list-tile-action>
                                    <v-list-tile-content @click="toggleNetwork">
                                        <v-list-tile-title>
                                            {{network.networkSlug}}
                                        </v-list-tile-title>
                                        <v-list-tile-sub-title>
                                            {{ networkStatus(network) }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                    <v-list-tile-avatar v-if="isNetworkRegistered(network)">
                                        <v-menu bottom nudge-right>
                                            <v-btn flat slot="activator" icon color="accent">
                                                <v-icon small>settings</v-icon>
                                            </v-btn>
                                            <v-list dense>
                                                <v-list-tile
                                                    v-for="(networkOption, i) in networkOptions"
                                                    :key="i"
                                                    @click="disconnectNetwork"
                                                >
                                                    <v-list-tile-title :data-slug="network.networkSlug">
                                                        {{ networkOption.title }}
                                                    </v-list-tile-title>
                                                </v-list-tile>
                                            </v-list>
                                        </v-menu>
                                    </v-list-tile-avatar>
                                </v-list-tile>
                            </v-list>
                        </v-navigation-drawer>
                    </v-card>
                </v-flex>
                <v-toolbar dense app clipped-left fixed>
                    <h1><a href="/" class="black--text" title="home"><img class="hidden-xs-only" src="/assets/img/logo.png">Share<span class="accent-color">to</span>all<small class="beta hidden-xs-only">beta</small></a></h1>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn flat color="secondary" id="button-logout" clipped-right @click.native="logout()">Logout</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-flex lg4 class="ma-3 mt-5">
                    <v-card class="pa-3" app>
                        <v-form id="form-message">
                            <v-textarea
                                id="message"
                                color="secondary"
                                name="message"
                                label="Message"
                                value=""
                                v-model="message"
                                :rules="[(v) => v.length <= 280 || 'Max 280 characters']"
                                :counter="280"
                            ></v-textarea>
                            <div class="pt-2 text-xs-right">
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
                            </div>
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
        this.refreshNetworks()
    },
    data() {
        return {
            'networks': '',
            'userId': this.$session.getUser().userId,
            'username': this.$session.getFullName(),
            'selectClass' :'primary',
            'message': '',

            'messageLoading': false,
            'networkLoading': false,
            'twitterLoading': false,
            'linkedinLoading': false,
            'networkOptions': [
                { title: 'Disconnect', function: 'disconnectNetwork' },
            ],
        };
    },
    methods: {
        getSocialIcon(slug){
            return 'pe-so-' + slug;
        },
        getLoading(slug){
            return this[slug + 'Loading'];
        },
        logout() {
            this.$session.logout();
        },
        refreshNetworks(networkSlug) {
            this.$network.findUserNetwork(this.userId).then(response => {
                this.networks = response;
                if(networkSlug != undefined) {
                    this[networkSlug + 'Loading'] = false;
                }
            });
        },
        isNetworkRegistered(network) {
            return network.userId == this.userId;
        },
        networkStatus(network) {

            if (!this.isNetworkRegistered(network)) {
                return 'Click to connect';
            }

            if (network.userAccount == '' || network.userAccount == undefined) {
                return 'Connected';
            }

            return network.userAccount;
        },
        toggleNetwork(event) {
            const el = event.target;
            const listItem = el.closest('.list-item');
            const button = listItem.querySelector('button');

            button.classList.toggle(this.selectClass);
            button.classList.toggle('selected');

            if(!listItem.classList.contains('connected') && button.classList.contains(this.selectClass)) {
                const networkSlug = button.dataset.slug;
                this[networkSlug + 'Loading'] = true;

                this.$api.get(`connect/${networkSlug}`).then(response => {
                    this[networkSlug + 'Loading'] = true;
                    window.location = response.data;
                }, () => {
                    this.networkLoading = false;
                    button.classList.toggle(this.selectClass);
                    button.classList.toggle('selected');
                });
            }
        },
        disconnectNetwork(event) {
            const el = event.target;

            const networkSlug = el.dataset.slug;
            this[networkSlug + 'Loading'] = true;

            this.$network.deleteUserNetwork(this.userId, networkSlug).then(() => {
                // TODO only refresh one network
                this.refreshNetworks(networkSlug);
            }, (error) => {
                this.$alert.error('ERROR '+error);
                this[networkSlug + 'Loading'] = false;
            });
        },
        sendMessage(event) {
            event.preventDefault();
            this.messageLoading = true;

            const networks = document.getElementById('networks');
            const connectedNetworks = networks.querySelectorAll('.selected');

            const networkSlugs = Array.from(connectedNetworks, network => network.dataset.slug);
            const networkSlugLg = networkSlugs.length;

            if (networkSlugLg <= 0) {
                this.$alert.error('You need to select at least one social media.');
                this.messageLoading = false;
                return false;
            }

            for (var i = 0; i < networkSlugLg; i++) {
                this.$api.post('message', {networkSlug: networkSlugs[i], message: this.message}).then(response => {
                    this.message = '';
                    this.messageLoading = false;
                    this.$alert.success('Your message has been shared on '+response.data.network+'!');
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
            const binding = {};
            if (this.$vuetify.breakpoint.xs) binding.column = true;
            return binding;
        },
    },
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
