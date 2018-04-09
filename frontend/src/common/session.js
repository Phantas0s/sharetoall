import Api from 'common/api';

class Session {
    /**
     * @param {Storage} storage
     */
    constructor(storage) {
        this.storage = storage;
        this.session_token = this.storage.getItem('session_token');
        // WARNING if the ttl change here, it needs to be changed on the server side (service/Session.php)
        this.ttl = 604800;

        const userJson = this.storage.getItem('user');
        this.user = userJson !== 'undefined' ? JSON.parse(userJson) : null;

        const expiration = this.storage.getItem('expiration');
        this.expiration = expiration !== 'undefined' ? expiration : null;
    }

    setToken(token) {
        this.session_token = token;
        this.storage.setItem('session_token', token);
        Api.defaults.headers.common['X-Session-Token'] = token;
    }

    getToken() {
        return this.session_token;
    }

    deleteToken() {
        this.session_token = null;
        this.storage.removeItem('session_token');
        this.expiration = null;
        this.storage.removeItem('expiration');
        Api.defaults.headers.common['X-Session-Token'] = '';
        this.deleteUser();
    }

    setUser(user) {
        this.user = user;
        this.storage.setItem('user', JSON.stringify(user));
    }

    setExpiration() {
        this.expiration = parseInt(new Date().getTime() / 1000) + parseInt(this.ttl); // seconds
        this.storage.setItem('expiration', JSON.stringify(this.expiration));
    }

    isExpired() {
        const expiration = parseInt(this.storage.getItem('expiration'));
        const currentTime = parseInt(new Date().getTime() / 1000); //seconds

        if (currentTime > expiration) {
            return false;
        }

        return true;
    }

    isValid() {
        if (!this.isExpired() || !this.isUser()) {
            return false;
        }

        return true;
    }

    getUser() {
        return this.user;
    }

    getEmail() {
        if (this.isUser()) {
            return this.user.email;
        }

        return '';
    }

    getFullName() {
        if (this.isUser()) {
            return this.user.userFirstname + ' ' + this.user.userLastname;
        }

        return '';
    }

    isUser() {
        return this.user !== null;
    }

    deleteUser() {
        this.user = null;
        this.storage.removeItem('user');
    }

    logout() {
        const token = this.getToken();

        this.deleteToken();

        Api.delete('session/' + token).then(
            () => {
                window.location = '/';
            }
        );
    }
}

export default Session;
