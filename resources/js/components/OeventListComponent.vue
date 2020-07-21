<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">

        <div class="w-full">

            <div class="px-6 py-1 items-center bg-gray-200 border-b">

                <!-- Filter barr two columns -->
                <div class="flex">
                    <div class="w-1/2">

                        <div class="flex">
                            <div class="w-1/3 pr-2">
                                <select v-model="sport" class="form-input-full">
                                    <option value="0">Filtruj podle sportu</option>
                                    <option v-for="(sport, index) in sports" :value="index">{{ sport }}</option>
                                </select>
                            </div>
                            <div class="w-1/3">
                                <select v-model="cat" class="form-input-full">
                                    <option value="0">Filtruj podle kategorie</option>
                                    <option v-for="(cat, index) in category" :value="index">{{ cat }}</option>
                                </select>
                            </div>
                            <div class="w-1/3">

                            </div>
                        </div>

                    </div>
                    <div class="w-1/2"></div>
                </div>
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
                        <tr v-for="oevent in filteredList" :key="oevent.id">

                            <td v-if="oevent.event_category === 1" class="bg-white rounded-l border-l-8 border-blue-500"></td>
                            <td v-else-if="oevent.event_category === 2" class="bg-white rounded-l border-l-8 border-red-500"></td>
                            <td v-else-if="oevent.event_category === 3" class="bg-white rounded-l border-l-8 border-blue-500"></td>
                            <td v-else-if="oevent.event_category === 4" class="bg-white rounded-l border-l-8 border-blue-500"></td>
                            <td v-else class="bg-white rounded-l border-l-8 border-gray-500"></td>

                            <td class="px-4 py-2 bg-white">{{ oevent.from_date | moment("DD.MM.YYYY") }}</td>
                            <td class="px-4 py-2 bg-white">
                                <span v-if="oevent.is_canceled === 1" class="line-through text-gray-600">{{ oevent.title }}</span>
                                <span v-else>{{ oevent.title }}</span>
                            </td>
                            <td class="px-4 py-2 bg-white">{{ oevent.place }}</td>
                            <td class="px-4 py-2 bg-white rounded-r">
                                <span v-for="(club, index) in oevent.clubs">{{ club }} </span>
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
        props: ['events_in_year', 'is_canceled_show', 'list_limit', 'events_from'],
        data() {
            return {
                oevents: [],
                regions: [],
                sports: [],
                category: [],

                sport: 0,
                region: 0,
                cat: 0,
                flt_isCancel: 0,
                flt_list: 10,
                isToggled: false,
            }
        },
        mounted() {
            this.readOeventList();
        },
        methods: {
            readOeventList(){
                axios.get('/admin/oevents/json/listallinyear/'+this.events_in_year+'/'+this.events_from)
                    .then(response => {
                        this.oevents = response.data.oevents;
                        this.regions = response.data.regions;
                        this.sports = response.data.sports;
                        this.category = response.data.category;
                    });
            },
        },
        computed: {
            filteredList: function() {

                let sportFilterArray = [];
                let catFilterArray = [];
                let listFilterArray = [];

                // filter by sport
                if (this.sport == 0) {
                    sportFilterArray = this.oevents;
                } else {
                    sportFilterArray = this.oevents.filter(obj => obj.sport_id == this.sport);
                }

                // filter by event category
                if (this.cat == 0) {
                    catFilterArray = sportFilterArray;
                } else {
                    catFilterArray = sportFilterArray.filter(obj => obj.event_category == this.cat);
                }

                // filter by num list
                if (this.list_limit == 0) {
                    listFilterArray = catFilterArray;
                } else {
                    listFilterArray = catFilterArray.slice(0, this.list_limit);
                }


                return listFilterArray;

                //return this.oevents.filter(obj => obj.is_canceled === 1); //funguje
                //return this.oevents.filter(obj => obj.clubs === 'ABM'); //funguje

                //return this.oevents.filter(obj => obj.regions.includes(this.region) == 1);
                                //return this.oevents;
            },
            testMoment: function(){

                return moment('2010-10-20').isBefore('2010-10-21')
            }
        },
        filters: {
            stringToArray: function (value){
                //if (!value) return '';
                return JSON.parse(value)

            }
        }
    };


</script>

<style>
    table {
        border-collapse:separate;
        border-spacing: 0 4px;
    }
</style>



