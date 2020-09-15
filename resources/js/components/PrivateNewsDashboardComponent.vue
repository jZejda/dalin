<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">
        <ul>
            <li v-for="privateNew in privateNews" :key="privateNew.id">
                <button @click="privateNew.show = !privateNew.show">{{privateNew.title}}</button>
                <div v-if="privateNew.show">
                    {{ privateNew.content}}
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    //vyzkousej subkomponentu
    //https://vuejs.org/v2/guide/single-file-components.html
    //https://jsfiddle.net/w10qx0dv/

    import moment from 'moment';

    export default {
        props: ['list_limit'],
        data() {
            return {
                test: false,
                privateNews: [],
            }
        },
        mounted() {
            this.readPrivateNewsList();
        },
        methods: {
            readPrivateNewsList(){
                axios.get('/admin/dashboard/json/privatenews')
                    .then(response => {
                        this.privateNews = response.data.private_news;
                    });
            },
        },
    };


</script>

<style>
    table {
        border-collapse:separate;
        border-spacing: 0 4px;
    }
</style>



