import Api from 'common/api';
import Collection from 'common/collection';

class Network {

    findAll() {
        return Api.get('network').then(
            response => Promise.resolve(new Collection(response.data))
        );
    }

    findUserNetwork(userId) {
        return Api.get('network/' + userId).then(
            response => Promise.resolve(new Collection(response.data))
        );
    }
}

export default Network;
