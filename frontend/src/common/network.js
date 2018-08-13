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

    deleteUserNetwork(userId, networkSlug) {
        return Api.delete('/network/' + userId + '/' + networkSlug).then(
            response => Promise.resolve(new Collection(response.data))
        );
    }
}

export default Network;
