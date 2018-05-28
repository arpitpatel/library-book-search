$('#slider').slider({
	range: true,
	min: 0,
	max: 3000,
	values: [ 1, 3000 ],
	slide: function(event, ui) {
		
		$('.ui-slider-handle:eq(0) .price-range-min').html('' + ui.values[ 0 ]);
		$('.ui-slider-handle:eq(1) .price-range-max').html('' + ui.values[ 1 ]);
		$('.price-range-both').html('<i>' + ui.values[ 0 ] + ' - </i>' + ui.values[ 1 ] );
		
		//
		
    if ( ui.values[0] == ui.values[1] ) {
      $('.price-range-both i').css('display', 'none');
    } else {
      $('.price-range-both i').css('display', 'inline');
    }
        
		
	},
	//stop: function( event, ui ) { alert(ui.values[ 0 ]);  }
});

$('.ui-slider-range').append('<span class="price-range-both value"><i>' + $('#slider').slider('values', 0 ) + ' - </i>' + $('#slider').slider('values', 1 ) + '</span>');

$('.ui-slider-handle:eq(0)').append('<span  class="price-range-min value">' + $('#slider').slider('values', 0 ) + '</span>');

$('.ui-slider-handle:eq(1)').append('<span class="price-range-max value">' + $('#slider').slider('values', 1 ) + '</span>');