<template>
    <div class="mt-3">
        <h2 class="text-center mb-3">Login</h2>

        <div v-if="authError" class="alert alert-danger" v-text="authError"></div>

        <div data-turbolinks="false">
            <div class="text-center mt-5">
                <a href="/login/redirect/google">
                    <img src="/i/btn_google_signin_dark_normal_web@2x.png" alt="Sign in with Google">
                </a>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    props: ['auth-error']
}
</script>
