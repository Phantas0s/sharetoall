import Dashboard from 'sharetoall/pages/dashboard.vue';

export default [
    { path: '/', redirect: '/dashboard' },
    { path: '/dashboard', component: Dashboard },
];
