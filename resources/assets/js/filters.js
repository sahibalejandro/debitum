Vue.filter('calendar', date => {
    return moment(date).calendar(null, {
        sameDay: '[Today]',
        nextDay: '[Tomorrow]',
        nextWeek: '[Next] dddd',
        lastDay: '[Yesterday]',
        lastWeek: '[Last] dddd',
        sameElse: 'MMMM DD, YYYY'
    });
});

Vue.filter('currency', amount => {
    return accounting.formatMoney(amount / 100);
});
