<template>
    <div class="mt-3">
        <div class="mb-3">
            <a href="/paid" class="btn btn-link">History</a>
            <a href="/payments/create" class="btn btn-link">Add</a>
        </div>
        <payment v-for="payment in sortedPayments"
                 :key="payment.id"
                 :payment="payment"
                 @deleted="removePayment"
                 @paid="paidPayment">
        </payment>
        <div v-if="payments.length === 0" class="text-center mt-3">
            <div class="display-4 mt-5 mb-5">🎉</div>
            <p>
                Great! You have nothing to pay.
            </p>
            <p>
                <small class="text-muted">Try to mantain this message ;)</small>
            </p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        dataPayments: {
            type: Array,
            default() {
                return [];
            }
        },

        orderByDueDate: {
            type: Boolean,
            default: true
        }
    },

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
            if (! this.orderByDueDate) {
                return this.payments;
            }

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
