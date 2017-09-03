import Api from 'common/api';

class Network {
    getNetworkList(userId) {
        Api.get('network/' + userId).then(
            () => {
                //return network list for user
            }
        );
    }
}

export default Network;
