import Api from 'common/api';

class Session {
    /**
     * @param {Storage} storage
     */
    constructor(storage) {
        this.storage = storage;
        this.session_token = this.storage.getItem('session_token');

        const userJson = this.storage.getItem('user');
        this.user = userJson !== 'undefined' ? JSON.parse(userJson) : null;
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
        Api.defaults.headers.common['X-Session-Token'] = '';
        this.deleteUser();
    }

    setUser(user) {
        this.user = user;
        this.storage.setItem('user', JSON.stringify(user));
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
                window.location = '/auth/login';
            }
        );
    }
}

export default Session;
