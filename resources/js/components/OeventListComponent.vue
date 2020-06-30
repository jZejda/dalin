<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">

        <div class="w-full">
            <div class="px-6 py-1 items-center h-12 bg-gray-200">

                <div class="flex justify-between">

                    <div class="flex justify-start">
                        <h1 class="adm-h1">Akce</h1>
                    </div>
                    <div class="flex justify-start">
                    </div>
                </div>
            </div>

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

            <div class="px-6 py-1 items-center">

                <table class="table-fixed w-full">

                    <thead>
                    <tr>
                        <th class="w-1/4 px-4 py-2">Datum</th>
                        <th class="w-1/4 px-4 py-2">Název</th>
                        <th class="w-1/4 px-4 py-2">Místo</th>
                        <th class="w-1/4 px-4 py-2">Klub</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="oevent in filteredList" :key="oevent.id" class="border-b">
                            <td class="px-4 py-2">{{ oevent.from_date | moment("DD.MM.YYYY") }}</td>
                            <td class="px-4 py-2">{{ oevent.title }}</td>
                            <td class="px-4 py-2">{{ oevent.place }}</td>
                            <td class="px-4 py-2">
                                <span v-for="(club, index) in oevent.clubs">{{ club }}, </span>
                            </td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </div>
    </div>
</template>

<script>
    export default {
        props: ['events_in_year'],
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
                isToggled: false,
            }
        },
        mounted() {
            this.readOeventList();
        },
        methods: {
            readOeventList(){
                axios.get('/devel/oevents/json/listallinyear/'+this.events_in_year)
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

                return catFilterArray;

                //return this.oevents.filter(obj => obj.is_canceled === 1); //funguje
                //return this.oevents.filter(obj => obj.clubs === 'ABM'); //funguje

                //return this.oevents.filter(obj => obj.regions.includes(this.region) == 1);
                                //return this.oevents;
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


