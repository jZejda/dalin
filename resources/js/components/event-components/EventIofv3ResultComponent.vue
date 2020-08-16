<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">

        <div class="w-full">

            <div class="px-6 py-1 items-center bg-gray-200 border-b">

            </div>

            <div class="px-6 py-1 items-center bg-gray-100">

                <table class="table-fixed w-full">

                    <thead>
                    <tr>
                        <th class="px-1 py-2" style="width: 5px"></th>
                        <th class="w-2/12 px-4 py-2">Datum</th>
                        <th class="w-4/12 px-4 py-2">Název</th>
                        <th class="w-4/12 px-4 py-2">Místo</th>
                        <th class="w-2/12 px-4 py-2">Klub</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class=""></td>
                            <td class="px-4 py-2 bg-white">dd</td>
                            <td class="px-4 py-2 bg-white">dddd</td>
                            <td class="px-4 py-2 bg-white">dddd</td>
                            <td class="px-4 py-2 bg-white rounded-r">
                            </td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </div>
    </div>
</template>

<script>

    import moment from 'moment';

    export default {
        props: [],
        data() {
            return {
                data: [],
                test:''
            }
        },
        mounted() {
            this.readEventIofv3Result();
        },
        methods: {
            readEventIofv3Result(){
                axios.get('/admin/oevents/json/listallinyear/')
                    .then(response => {
                        this.data = response.data.rawdata;
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



