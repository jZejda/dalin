<template>
    <!-- Content -->
    <div class="flex-1 flex flex-row">
        <div id="content-left-sidebar" class="h-screen border-l border-r border-black" style="width: 400px; background-color: #242528;">
            <!-- TODO prepare to Vue search
           <div class="h-12 px-4 py-1 border-b border-white items-center self-center">

               <input class="appearance-none block w-full bg-white text-grey-darker border border-grey-light rounded py-2 px-2 leading-tight focus:outline-none focus:bg-white focus:border-grey"
                   id="grid-city" type="text" placeholder="Hledej">

            </div>
            -->

            <!-- Item iterate -->
            <div v-for="(ccat, index) in ccats" :key="index" v-on:click="ShowCatDetail(ccat.id, index)">
                <div v-if="ccat.id === activeid" class="no-underline block group bg-yellow-600 hover:bg-yellow-600 p-4">
                    <p class="font-bold text-lg mb-1 text-black hover:text-black">
                    {{ccat.title}}
                </p>
                <p class="text-xs text-grey-darkest mb-2">/kategorie/{{ccat.slug}}</p>
                </div>
                <div v-else class="no-underline block group hover:bg-yellow-600 hover:text-black p-4 border-b border-gray-700 text-white">
                    <p class="font-bold text-lg mb-1">
                    {{ccat.title}}
                </p>
                <p class="text-xs mb-2">/kategorie/{{ccat.slug}}</p>
                </div>
            </div>

        </div>

        <div class="w-full">
            <div class="px-6 py-1 items-center h-12 bg-gray-200 border-b">

                <div class="flex justify-between">

                    <div class="flex justify-start">
                        <h1 class="adm-h1">Kategorie</h1>
                    </div>
                    <div class="flex justify-start">
                        <button @click="toggleModalCreate" title="Přidej kategorii" class="btn-ico btn-ico-blue">
                            <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 ">
                
                
                <div>
                    <div v-if="activeid">
                        <p class="form-label">Nadpis</p>
                        <h2>{{ ccatActiveDetail.title }}</h2>
                        <p class="form-label pt-2">Popis:</p>
                        <h3>{{ ccatActiveDetail.description }}</h3>

                        <p class="form-label pt-2">Odkaz</p>
                        <h3>/kategorie/{{ ccatActiveDetail.slug }}</h3>


                        <button @click="initUpdate(activeindex)" class="btn btn-blue mt-4">Upravit kategorii</button>
                    </div>

                    <div v-else>
                        <h2>Pro editaci zvol kategorii vlevo.</h2>
                    </div>


                    <!--
                    <span @click="toggleModalCreate" class="absolute pin-t pin-r pt-4 px-4">
                                <svg class="h-12 w-12 text-grey hover:text-grey-darkest" role="button" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <title>vytvo5</title>
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                </svg>
                            </span>
                    -->

                    <!-- Modal Create Task -->
                    <div v-if="modalCreate" @click.self="toggleModalCreate" class="modal-remove bg-smoke">
                        <div class="fixed shadow-inner max-w-md md:relative bottom-0 inset-x-0 align-top m-auto justify-end md:justify-center bg-white md:rounded w-full md:h-auto md:shadow flex flex-col">

                            <div class="bg-yellow-600 md:rounded-t mb-3">
                                <h2 class="text-2xl text-center font-hairline md:leading-loose text-grey-darkest md:mt-4 mb-2">Přidej novou kategorii.</h2>
                            </div>

                            <div v-if="errors.length > 0">
                                <ul>
                                    <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                                </ul>
                            </div>

                            <div class="m-6">
                                <label for="title" class="form-label">Nadpis</label>
                                <input type="text" name="title" id="title" placeholder="název kategorie" class="form-input-full" v-model="ccat.title">

                                <label for="description" class="form-label">Popis</label>
                                <input type="textarea" name="description" id="description" placeholder="popis kategorie" class="form-input-full"
                                       v-model="ccat.description">
                            </div>


                            <div class="inline-flex justify-center mb-8">
                                <button @click="createContentCat" class="btn-2x btn-blue">
                                    Uložit
                                </button>
                                <button @click="toggleModalCreate" class="ml-2 btn-2x btn-blue-outline">
                                    Zavřít bez uložení
                                </button>
                            </div>
                            <span @click="toggleModalCreate" class="absolute top-0 right-0 pt-4 px-4">
                                <svg class="h-12 w-12 text-grey hover:text-grey-darkest" role="button" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <title>Close</title>
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <!-- End of Modal Create Task -->
                    <!-- Modal Update Task -->
                    <div v-if="modalEdit" @click.self="toggleModalEdit" class="modal-remove bg-smoke">
                        <div class="fixed shadow-inner max-w-md md:relative bottom-0 inset-x-0 align-top m-auto justify-end md:justify-center bg-white md:rounded w-full md:h-auto md:shadow flex flex-col">

                            <div class="bg-yellow-600 md:rounded-t mb-3">
                                <h2 class="text-2xl text-center font-hairline md:leading-loose text-grey-darkest md:mt-4 mb-2">Uprav kategorii.</h2>
                            </div>

                            <div v-if="errors.length > 0">
                                <ul>
                                    <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                                </ul>
                            </div>

                            <div class="m-6">
                                <label for="title" class="form-label">Nadpis</label>
                                <input type="text" name="title" placeholder="název kategorie" class="form-input-full"
                                       v-model="ccat_update.title">

                                <label for="description" class="form-label">Popis</label>
                                <input type="textarea" name="description" id="description" placeholder="Nazev" class="form-input-full"
                                    v-model="ccat_update.description">

                                <label for="slug" class="form-label">cesta</label>
                                <input type="text" name="slug" id="description" placeholder="cesta" class="form-input-full"
                                       v-model="ccat_update.slug">
                            </div>

                            <div class="inline-flex justify-center mb-8">
                                <button @click="updateContentCat" class="btn-2x btn-blue">
                                    Upravit Kategorii
                                </button>
                                <button @click="toggleModalEdit" class="ml-2 btn-2x btn-blue-outline">
                                    Zavřít
                                </button>
                            </div>
                            <span @click="toggleModalEdit" class="absolute top-0 right-0 pt-4 px-4">
                                <svg class="h-12 w-12 text-grey hover:text-grey-darkest" role="button" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <title>Close</title>
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <!-- End of Modal Update Task -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                ccat: {
                    title: '',
                    description: '',
                    slug: ''
                },
                ccatActiveDetail: {},
                ccat_update:{},
                ccats: [],
                activeid: '',
                activeindex: '',
                modalCreate: false,
                modalEdit: false,
                errors: []
            }
        },
        mounted() {
            this.readContentCats();
        },
        methods: {
            toggleModalCreate() {
                this.modalCreate = !this.modalCreate
            },
            toggleModalEdit() {
                this.modalEdit = !this.modalEdit
            },
            readContentCats() {
                axios.get('/admin/contentcategories')
                    .then(response => {
                        this.ccats = response.data.ccats;
                    });

            },
            createContentCat() {
                axios.post('/admin/contentcategories', {
                        title: this.ccat.title,
                        description: this.ccat.description,
                        slug: this.ccat.slug,
                    })
                    .then(response => {
                        this.reset();
                        this.ccats.push(response.data.ccat);
                        this.modalCreate = false;
                    })
                    .catch(error => {
                        this.errors = [];
                        if (error.response.data.errors.title) {
                            this.errors.push(error.response.data.errors.title[0]);
                        }

                        if (error.response.data.errors.description) {
                            this.errors.push(error.response.data.errors.description[0]);
                        }
                    });
            },
            initUpdate(index)
            {
                this.errors = [];
                this.modalEdit = true;
                this.ccat_update = this.ccats[index];
                
                //this.ccat_update = '';
                //this.ccat_update = {
                //    'title': this.ccats[index].title, 
                //    'description': this.ccats[index].description,
                //    'slug': this.ccats[index].slug,
                //    'id': this.ccats[index].id,
                //} 
            },
            updateContentCat()
            {
                axios.patch('/admin/contentcategories/' + this.ccat_update.id, {
                    title: this.ccat_update.title,
                    description: this.ccat_update.description,
                    slug: this.ccat_update.slug,
                    id: this.ccat_update.id,
                })
                    .then(response => {
 
                        this.modalEdit = false;
 
                    })
                    .catch(error => {
                        this.errors = [];
                        if (error.response.data.errors.title) {
                            this.errors.push(error.response.data.errors.title[0]);
                        }
 
                        if (error.response.data.errors.description) {
                            this.errors.push(error.response.data.errors.description[0]);
                        }

                        if (error.response.data.errors.slug) {
                            this.errors.push(error.response.data.errors.slug[0]);
                        }
                    });
            },
            reset () {
                this.ccat.title = '';
                this.ccat.description = ''
            },
            ShowCatDetail(id, index) {
                this.activeid = id;
                this.activeindex = index;
                this.ccatActiveDetail = {};
                axios.get('/admin/contentcategories/showdetail/'+id)
                    .then(response => {
                        this.ccatActiveDetail = response.data.ccatDetail;
                    });

                //console.log('jede' + id);
            }
        }
    } 
</script>
