<template>
    <div>
        <div class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white rounded" @click="logout">
            <feather-icon icon="LogOutIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Logout</span>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        logout() {
            this.$store.commit('LOGOUT');
            this.$router.push('/s3ndpvt2020');
        },
    }
}
</script>

<style scoped>
.rounded
{
    transition: all 0.3s;
}
</style>