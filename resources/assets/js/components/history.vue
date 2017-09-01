<template>
    <div class="mt-3">

        <div class="mb-3">
            <a href="/payments" class="btn btn-link">Payments</a>
        </div>

        <div v-for="payment in payments" class="card mb-3">
            <div class="card-header">
                {{ payment.name }}
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h4 class="card-title">{{ payment.amount | currency }}</h4>
                    <small class="text-muted">Paid at {{ payment.paid_at | formatDate }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['payments'],

    filters: {
        formatDate(date) {
            return moment(date).format('MMMM DD, YYYY');
        }
    }
}
</script>
