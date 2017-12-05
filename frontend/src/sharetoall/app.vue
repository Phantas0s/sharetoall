<template>
    <div id="app">
        <main>
            <router-view></router-view>
        </main>
    </div>
</template>

<script>
'use strict';

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
