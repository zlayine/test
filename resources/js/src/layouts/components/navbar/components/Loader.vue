<template>
    <div>
        <vs-row v-if="uploadPercent != null">
            <span class="mb-2">Uploading File {{ uploadPercent }}%</span>
            <vs-progress :percent="uploadPercent" color="primary"></vs-progress>
        </vs-row>
    </div>
</template>

<script>
export default {
    computed: {
        uploadPercent() {
            return this.$store.getters.uploadPercent;
        },
    }
}
</script>