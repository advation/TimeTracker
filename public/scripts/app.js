(function() {

	//New Announcement App
	var app = angular.module('announcement',[]);

	app.filter('unsafe', function($sce) 
	{
		return function(val) 
		{
			return $sce.trustAsHtml(val);
    	};
	});


	app.controller('PreviewController', function($scope) {
		
		this.ptitle = "Example Title";
		this.pcontent = "Vivamus laoreet, justo at bibendum auctor, ante libero pulvinar neque, in fermentum turpis nunc ut augue. Aliquam iaculis tortor orci, in malesuada erat accumsan ac. Vivamus commodo enim ac ligula ornare, et faucibus est sagittis. Pellentesque tellus dui, condimentum sed turpis vel, vulputate ullamcorper libero. Praesent eget nibh ante. Nunc massa urna, tempus vel ante eu, maximus vestibulum orci. Donec et malesuada ipsum. Aliquam porta nunc eu turpis blandit vulputate. Aliquam in ante at ipsum elementum vulputate. Etiam congue id ipsum nec molestie. Maecenas vitae scelerisque risus. Cras nibh mauris, varius vitae euismod eget, eleifend porta urna. Duis pretium posuere quam, vitae sollicitudin est vulputate finibus.";
		
		this.titleUpdate = function() {
			this.ptitle = $scope.title;	
		};

		this.contentUpdate = function() {

			var buf = $scope.content;
			buf = buf.replace(/\r?\n/g, '<br />');
			this.pcontent = buf;	
		};

	});


})();