Vue.filter('calendar', date => {
    return moment(date).calendar();
});

Vue.filter('currency', amount => {
    return accounting.formatMoney(amount / 100);
});
