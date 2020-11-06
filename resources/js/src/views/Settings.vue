<template>
     <div id="settings" class="data-list-container">
		<vx-card title="Settings">
            <div class="vx-row">
                <div class="vx-col sm:w-1/4 w-full mb-2">
                    <vs-input class="w-full mt-0" label-placeholder="Debug Email" v-model="settings.email" />
                </div>
                <div class="vx-col sm:w-1/4 w-full">
                    <vs-button @click="saveSettings('email')" >Save Email</vs-button>
                </div>
            </div>
		</vx-card>
    </div>
</template>

<script>
export default {
    data(){
        return {
            settings: {
                email: '',
            }
        }
    },
    created(){
        this.$vs.loading();
        this.$store.dispatch('fetchSettings')
        .then(res => {
            this.settings.email = this.email;
            this.$vs.loading.close();
        })
    },
    methods: {
        saveSettings(option){
           if(option == 'email'){
			   if(this.settings.email)
                    this.$store.dispatch('saveSetEmail', this.settings.email)
                    .then(res => {
                        this.showDialog(res.data.message, 0, 'Settings');
                    });
           }
	   },
    },
    computed: {
        email(){
            return this.$store.getters.email;
        },
    }
}
</script>