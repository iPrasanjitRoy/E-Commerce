if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}

$(function() {
    "use strict";

    // main sidebar toggle js
    $('.menu-toggle').on('click', function () {
        $('.sidebar').toggleClass('open');
        $('.open').removeClass('sidebar-mini');
    });

    // layout a sidebar mini version
    $('.sidebar-mini-btn').on('click', function () {
        $('.sidebar').toggleClass('sidebar-mini');
        $('.sidebar-mini').removeClass('open');
    });



    // cSidebar overflow daynamic height
    
    overFlowDynamic();

    $(window).resize(function(){
        overFlowDynamic();
    });

    function overFlowDynamic(){ 
        var sideheight=$(".sidebar.sidebar-mini").height() + 48;
        
        if(sideheight <= 760) {  
            $(".sidebar.sidebar-mini").css( "overflow", "scroll");  
        }
        else{
            $(".sidebar.sidebar-mini").css( "overflow", "visible"); 
        }
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    
  
});




 