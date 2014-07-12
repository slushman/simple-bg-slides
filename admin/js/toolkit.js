jQuery(document).ready(function($){
	// color picker	
    $('.slushman_color_picker').wpColorPicker();

    // date picker
    $('.slushman_date_picker').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm/dd/yy',
		showButtonPanel: true
	});

	// datetime picker
	$('.slushman_datetime_picker'||'.slushman_datetime_local_picker').datetimepicker({
		addSliderAccess: true,
		sliderAccessArgs: {
			touchonly: false
		},
		timeFormat: "hh:mm tt"
	});

	// datetime-local picker
	$('.slushman_datetime_local_picker').datetimepicker({
		addSliderAccess: true,
		sliderAccessArgs: {
			touchonly: false
		},
		timeFormat: "hh:mm tt"
	});

    // month picker
    $('.slushman_month_picker').MonthPicker({
		ShowIcon: false
	});

    // range slider
	$('.slushman_range_slider').slider();

	// time picker
	$('.slushman_time_picker').timepicker({
		addSliderAccess: true,
		sliderAccessArgs: {
			touchonly: false
		},
		timeFormat: "hh:mm tt"
	});

});

// week picker
// http://roelhartman.blogspot.com/2012/01/create-calendar-item-returning-week.html
/*jQuery(document).ready(function($){
	$('.slushman_week_picker').datepicker({
		showWeek: true,
		onSelect: function(dateText, inst) {
			//var week = $.datepicker.iso8601Week( new Date( dateText ) );
			//$(this).val( week + ', ' + date.selectedYear );
			inst.input.val("Week " + $.datepicker.iso8601Week(new Date(dateText)));
			//$( '.slushman_week_picker' ).val( $.datepicker.formatDate( 'yy-', new Date( dat ) ) + ( week<10 ? '0' : '' ) + week );
		} // End of onSelect function
	});
	$('.slushman_week_picker').ready(function() {
		$('<a href="http://cnn.com"></a>').insertAfter('<td class="ui-datepicker-week-col">');
	});
});*/