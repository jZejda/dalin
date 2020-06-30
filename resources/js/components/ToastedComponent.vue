<template>
    <div>
    </div>
</template>

<script>

import Toasted from 'vue-toasted';

Vue.use(Toasted,{
    position: 'top-right',
    duration: 6000,
    type: 'success'
})

export default {
    props: ['message', 'type'],
    mounted () {
        console.log('jsem namontovany');

        if ( this.type == 'success' ){
            this.$toasted.show(this.message)
        
        } else if (this.type == 'error') {
            this.$toasted.show(this.message, {
            type : 'error',
            duration: null,
            action : {
                text : 'Rozumím',
                onClick : (e, toastObject) => {
                    toastObject.goAway(0);
                }
            },
        })
        } else if (this.type == 'info') {
            this.$toasted.show(this.message, {
                type: 'info'
            })
        } else {
            this.$toasted.show('OOPS něco se asi nepovedlo.')
        }
        
       
    }
}
</script>