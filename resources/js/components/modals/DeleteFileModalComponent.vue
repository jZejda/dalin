<template>
    <div>
        <button @click="toggleModalDelete">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
        </button>

        <!-- Modal Update Task -->
        <transition name="fade">
            <div v-if="modalDelete" @click.self="toggleModalDelete" class="modal-remove bg-smoke">
                <div
                    class="fixed shadow-inner max-w-md md:relative pin-b pin-x align-top m-auto justify-end md:justify-center bg-white md:rounded w-full md:h-auto md:shadow flex flex-col">

                    <div class="bg-yellow-600 md:rounded-t mb-6">
                        <h2 class="text-2xl text-center font-hairline md:leading-loose text-white md:mt-4 mb-2">Chystáš se smazat soubor.</h2>
                    </div>


                    <p class="text-xl leading-normal mb-8 text-center">
                        {{ del_name }}
                    </p>

                    <div class="inline-flex justify-center mb-8">
                        <a :href="'/admin/media/' + this.doublecollon_name + '/delete'" class="btn-2x btn-blue" role="button">
                            SMAZAT
                        </a>
                        <button @click="toggleModalDelete"
                            class="ml-2 btn-2x btn-blue-outline">
                            STORNO
                        </button>
                    </div>
                    <span @click="toggleModalDelete" class="absolute top-0 right-0 pt-5 px-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x text-white" role="button"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </span>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>

export default {
    props: ['del_name', 'doublecollon_name'],
    data() {
        return {
            name: 'Jmeno',
            modalDelete: false,
        }
    },
    methods: {
        toggleModalDelete() {
            this.modalDelete = !this.modalDelete
        }
    }
}

</script>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
</style>
