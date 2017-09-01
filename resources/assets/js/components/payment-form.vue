<template>
    <form @submit.prevent="save">

        <div class="mt-3 mb-3">
            <a href="/payments" class="btn btn-link">Payments</a>
        </div>

        <!-- Name -->
        <div class="form-group mt-3">
            <label>Name</label>
            <input v-model="payment.name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" type="text" v-focus>
            <small class="text-danger" v-text="form.errors.get('name')"></small>
        </div>

        <!-- Amount -->
        <div class="form-group">
            <label>Amount</label>
            <div class="input-group">
                <div class="input-group-addon">$</div>
                <input v-model="amount" class="form-control" :class="{ 'is-invalid': form.errors.has('amount') }" type="number" min="1" step="0.01">
            </div>
            <small class="text-danger" v-text="form.errors.get('amount')"></small>
        </div>

        <!-- Due date -->
        <div class="form-group">
            <label>Due date</label>
            <input v-model="payment.due_date" class="form-control" :class="{ 'is-invalid': form.errors.has('due_date') }" type="date">
            <small class="text-danger" v-text="form.errors.get('due_date')"></small>
        </div>

        <!-- Repeat -->
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" v-model="repeat"> Repeat
                </label>
            </div>
            <div class="form-row align-items-center" v-if="repeat">
                <div class="col-auto">Every</div>
                <div class="col">
                    <input v-model="payment.repeat_period" class="form-control" :class="{ 'is-invalid': form.errors.has('repeat_period') }" type="number" step="1" min="1" max="100">
                </div>
                <div class="col">
                    <select v-model="payment.repeat_designator" class="form-control" :class="{ 'is-invalid': form.errors.has('repeat_designator') }">
                        <option value="weeks">weeks</option>
                        <option value="months">months</option>
                        <option value="years">years</option>
                    </select>
                </div>
            </div>
            <small class="text-danger" v-text="form.errors.get('repeat_period')"></small>
            <small class="text-danger" v-text="form.errors.get('repeat_designator')"></small>
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
                    due_date: moment().add(1, 'days').format('YYYY-MM-DD'),
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
            Turbolinks.visit('/payments', {action: 'replace'});
        }
    }
}
</script>
