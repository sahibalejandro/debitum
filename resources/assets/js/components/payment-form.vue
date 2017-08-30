<template>
    <form @submit.prevent="save">
        <!-- Name -->
        <div class="form-group">
            <label>Name</label>
            <input v-model="payment.name" class="form-control" type="text" v-focus>
        </div>

        <!-- Amount -->
        <div class="form-group">
            <label>Amount</label>
            <div class="input-group">
                <div class="input-group-addon">$</div>
                <input v-model="amount" class="form-control" type="number" min="1" step="0.01">
            </div>
        </div>

        <!-- Due date -->
        <div class="form-group">
            <label>Due date</label>
            <input v-model="payment.due_date" class="form-control" type="date">
        </div>

        <!-- Repeat -->
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" v-model="repeat"> Repeat
                </label>
            </div>
            <div class="form-row align-items-center" v-show="repeat">
                <div class="col-auto">Every</div>
                <div class="col">
                    <input v-model="payment.repeat_period" class="form-control" type="number" step="1" min="1" max="100">
                </div>
                <div class="col">
                    <select v-model="payment.repeat_designator" class="form-control">
                        <option value="weeks">weeks</option>
                        <option value="months">months</option>
                        <option value="years">years</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <button class="btn btn-primary w-100"
            type="submit"
            v-text="saveButtonText"
            :disabled="form.isPending">
        </button>
    </form>
</template>

<script>
export default {
    props: {
        'data-payment': {
            default() {
                return {
                    name: null,
                    amount: null,
                    due_date: null,
                    repeat_period: null,
                    repeat_designator: null
                }
            }
        }
    },

    data() {
        return {
            form: new Form(),
            payment: this.dataPayment,
            repeat: this.dataPayment.repeat_period !== null
        }
    },

    watch: {
        repeat(repeat) {
            if (! repeat) {
                this.payment.repeat_period = null;
                this.payment.repeat_designator = null;
            } else {
                this.payment.repeat_period = 1;
                this.payment.repeat_designator = 'weeks';
            }
        }
    },

    computed: {
        amount: {
            set(amount) {
                this.payment.amount = amount * 100;
            },

            get() {
                if (this.payment.amount === null) {
                    return null;
                }

                return this.payment.amount / 100;
            }
        },

        saveButtonText() {
            return this.form.isPending ? 'Saving...' : 'Save';
        }
    },

    methods: {
        async save() {
            await this.form.save('/payments', this.payment);
            Turbolinks.visit('/payments');
        }
    }
}
</script>
