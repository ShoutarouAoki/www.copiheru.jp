jQuery(function($){
  //slide
  $(function () {
    var destaque = $("#slide-container").destaque({
      slideMovement: 100,
      slideSpeed: 1000,
      elementSpeed: 1100,
      autoSlideDelay: 3000,
      itemSelector: ".item",
      itemBackgroundSelector: ".background",
      itemForegroundElementSelector: ".foreground .element",
      onPageUpdate: function(destaque, pageData) {
      setTimeout(function(){
        $(".pagination .bullet").removeClass("active");
        $(".pagination .bullet[rel='"+ pageData.currentSlide +"']").addClass("active");
      },700);
    }
  }); 


  $(".pagination .bullet").click(function(e) {
    e.preventDefault();
    destaque.goTo(parseInt($(this).attr("rel"), 10));
    });
  });


});