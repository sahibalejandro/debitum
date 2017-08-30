<template>
    <div class="mt-3">
        <a href="/payments/create">Add new payment</a>
        <payment v-for="payment in sortedPayments"
            :key="payment.id"
            :payment="payment"
            @deleted="removePayment"
            @paid="paidPayment">
        </payment>
    </div>
</template>

<script>
export default {
    props: ['data-payments'],

    data() {
        return {
            payments: this.dataPayments
        }
    },

    computed: {
        /**
         * Returns payments sorted by due date.
         *
         * @return {Array}
         */
        sortedPayments() {
            return this.payments.sort((a, b) => {
                if (a.due_date < b.due_date) return -1;
                if (a.due_date === b.due_date) return 0;
                if (a.due_date > b.due_date) return 1;
            });
        }
    },

    methods: {
        removePayment(id) {
            let index = this.payments.findIndex(p => p.id === id);
            this.payments.splice(index, 1);
        },

        paidPayment(payAction) {
            this.removePayment(payAction.payment.id);

            if (payAction.nextPayment) {
                this.payments.push(payAction.nextPayment);
            }
        }
    }
}
</script>
