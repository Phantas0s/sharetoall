<template>
    <div id="app">
        <nav>
            <!-- <router-link to="/dashboard">Dashboard</router-link> -->
        </nav>
        <main>
            <router-view></router-view>
        </main>
    </div>
</template>

<script>
    export default {
        name: 'app',
        computed: {},
    };
</script>

<style lang="css">
    main {
        margin: 10px 20px;
    }
</style>
