import Session from 'common/session';
import '../styles/sass/confirm.scss';

const session = new Session(window.localStorage);
session.setToken(window.sessionToken);
session.setUser(JSON.parse(window.sessionUser));
window.location = window.redirect;
