$(document).ready(function(){
    
    showMenu("orders");

    $(".side-nav a").click(function(){
       let menu = $(this).attr("op");
       showMenu(menu);
       return false;
    })

    $(".nav-list .admin-list a").click(function(){
        let menu = $(this).attr("op");
        showMenu(menu);
        return false;
     })
    

});

function showMenu(menu)
{
    $(".content .side-content .menu").slideUp();
    $(".content .side-content ."+menu).slideDown();
    $(".side-nav .side-box a").removeClass("btn-primary");
    $(".side-nav .side-box a").addClass("btn-default");
    $(".side-nav .side-box ."+menu).removeClass("btn-default");
    $(".side-nav .side-box ."+menu).addClass("btn-primary");
}