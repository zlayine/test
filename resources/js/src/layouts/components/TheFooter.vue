
<template functional>
    <footer class="the-footer flex-wrap justify-between" :class="classes">
        <span>COPYRIGHT &copy; {{ new Date().getFullYear() }}, All rights Reserved</span>
    </footer>
</template>

<script>
export default {
    name: "the-footer",
    props: {
        classes: {
            type: String,
        },
    }
}
</script>
