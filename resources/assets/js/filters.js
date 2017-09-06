Vue.filter('calendar', date => {
    return moment(date).calendar(null, { 
        nextWeek: '[Next] dddd',
        lastWeek: '[Last] dddd',
        sameElse: 'MMMM DD, YYYY'
    });
});

Vue.filter('currency', amount => {
    return accounting.formatMoney(amount / 100);
});
