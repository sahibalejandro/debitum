<template>
    <div>
        <div class="card mb-3" :class="{ 'border-warning': paymentIsOverdue }">
            <div class="card-header" :class="paymentIsOverdue ? 'border-warning bg-warning' : ''">
                {{ payment.name }}
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h4 class="card-title">{{ payment.amount | currency }}</h4>
                    <h6 class="card-subtitle text-muted">{{ payment.due_date | calendar }}</h6>
                    <small v-if="payment.repeat_period" class="text-muted">Every {{ payment.repeat_period }} {{ payment.repeat_designator }}</small>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-link btn-sm" type="button" @click.prevent="confirmPayment"
                    :disabled="form.isPending">Pay</button>

                <button class="btn btn-link btn-sm" type="button" @click.prevent="edit"
                    :disabled="form.isPending">Edit</button>

                <button class="btn btn-link btn-sm" type="button" @click.prevent="confirmDeletion"
                    :disabled="form.isPending">Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['payment'],

    data() {
        return {
            form: new Form()
        }
    },

    computed: {
        paymentIsOverdue() {
            return moment().isAfter(this.payment.due_date);
        }
    },

    methods: {
        confirmPayment() {
            if (confirm(`Please confirm you want to pay ${this.payment.name}`)) {
                this.pay();
            }
        },

        async pay() {
            let response = await this.form.post('/paid', {id: this.payment.id});

            this.$emit('paid', {
                payment: this.payment,
                nextPayment: response.next_payment
            });
        },

        edit() {
            Turbolinks.visit(`/payments/${this.payment.id}`);
        },

        confirmDeletion() {
            if (confirm(`Delete ${this.payment.name}`)) {
                this.destroy();
            }
        },

        async destroy() {
            await this.form.delete('/payments/' + this.payment.id);
            this.$emit('deleted', this.payment.id);
        }
    }
}
</script>
