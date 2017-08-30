import './bootstrap';
import './filters';
import './directives';

import Turbolinks from 'turbolinks';
import TurbolinksAdapter from 'vue-turbolinks';
Vue.use(TurbolinksAdapter);

Vue.component('payments', require('./components/payments.vue'));
Vue.component('payment', require('./components/payment.vue'));
Vue.component('payment-form', require('./components/payment-form.vue'));
Vue.component('repeat', require('./components/repeat.vue'));

document.addEventListener('turbolinks:load', () => {
    new Vue({
        el: '#app'
    });
});

Turbolinks.start();
