<template>
    <div>
        <div class="card mb-3">
            <div class="card-header">
                <div class="circle mr-1" :class="`circle--${payment.level}`"></div>
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

    methods: {
        confirmPayment() {
            let amount = accounting.formatMoney(this.payment.amount / 100);

            if (confirm(`Pay ${amount} for ${this.payment.name}?`)) {
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
            if (confirm(`Delete payment ${this.payment.name}?`)) {
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
