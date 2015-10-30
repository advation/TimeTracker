$(document).ready(function(){

	$('.postSlide').slick({
		slidesToShow: 2,
		slidesToScroll: 1,
		slide: 'div',
		arrows: true,
		adaptiveHeight: false,
		focusOnSelect: true,
		autoplay: true,
		autoplaySpeed: 7000,
		pauseOnHover: true,
		responsive: [
			{
				breakpoint: 752,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: true,
					arrows: false
				}
			}]
	});

	$('.event-slider').slick({
		infinite: false,
		slidesToShow: 6,
		slidesToScroll: 1,
		slide: 'div',
		dots: false,
		centerMode: false,
		focusOnSelect: false,
		vertical: true,
		arrows: true,
		swipe: false,
		prevArrow:".eventPrev",
		nextArrow:".eventNext"
	});

	var amountScrolled = 50;

	$(window).scroll(function()
	{
		if($(window).scrollTop() > amountScrolled)
		{
			$('a.upnav').fadeIn('slow');
		}
		else
		{
			$('a.upnav').fadeOut('slow');
		}
	});
});