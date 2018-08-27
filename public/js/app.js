/**
 * Created by Dread Pirate Roberts on 06-Aug-17.
 */
$('#menuToggle').on('click', function() {
    $('body').toggleClass('open');
});

Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
};

