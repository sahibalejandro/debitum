<template>
    <div class="mt-3">
        <h2 class="text-center mb-3">Login</h2>

        <div v-if="authError" class="alert alert-danger" v-text="authError"></div>

        <div data-turbolinks="false">
            <a href="/login/redirect/google">Login with Google</a>
        </div>

    </div>
</template>

<script>
export default {
    props: ['auth-error']
}
</script>
