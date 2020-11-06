<template>
    <div class="h-screen flex w-full bg-img vx-row no-gutter items-center justify-center">
        <div class="vx-col sm:w-1/2 md:w-1/2 lg:w-3/4 xl:w-3/5 sm:m-0 m-4">
            <vx-card>
                <div slot="no-body" class="full-page-bg-color">
                    <div class="vx-row no-gutter">
                        <div class="vx-col hidden sm:hidden md:hidden lg:block lg:w-1/2 mx-auto self-center">
                            <img :src="url + '/images/register.jpg'" alt="register" class="mx-auto">
                        </div>
                        <div class="vx-col sm:w-full md:w-full lg:w-1/2 mx-auto self-center  d-theme-dark-bg">
                            <div class="px-8 pt-8 register-tabs-container">
                                <div class="vx-card__title mb-4">
                                    <h4 class="mb-4">Create Account</h4>
                                    <p>Fill the below form to create a new account.</p>
                                </div>
                               <div class="clearfix">
                                    <vs-input
                                    name="email"
                                    type="email"
                                    label-placeholder="Email"
                                    placeholder="Email"
                                    v-model="email"
                                    class="w-full mt-6" />

                                    <vs-input
                                    ref="password"
                                    type="password"
                                    name="password"
                                    label-placeholder="Password"
                                    placeholder="Password"
                                    v-model="password"
                                    class="w-full mt-6" />

                                    <vs-button class="float-right mt-6 mb-3" @click="register" :disabled="!validateForm">Register</vs-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </vx-card>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            email: '',
            password: '',
            url: window.Laravel.url,
        }
    },
    computed: {
        validateForm() {
            return this.email != '' && this.password != '';
        }
    },
    methods: {
        register() {
            let data = {email: this.email, password: this.password};
            this.$store.dispatch('register', data)
            .then((res) => {
                this.$router.push({path: '/s3ndpvt2020'});
            });
        }
    },
    created(){
        this.$vs.loading();
        this.$store.dispatch('checkUser')
        .then(res => {
        this.$vs.loading.close();
        if (res[0].length > 0)
            this.$router.push({path: '/s3ndpvt2020'});
        })
    },
}
</script>
