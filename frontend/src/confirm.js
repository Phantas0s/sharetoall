import Session from 'common/session';

console.log('lolo');

const session = new Session(window.localStorage);
session.setToken(window.sessionToken);
session.setUser(JSON.parse(window.sessionUser));
window.location = window.redirect;
