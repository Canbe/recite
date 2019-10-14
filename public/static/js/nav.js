$(document).ready(function(){

    $(".tr-word .td-audio").click(function(){
        
        let au = $(this).find("audio")[0];

        au.play();

    });


    $("#live-search").hide();


    $("#search-input").bind("input propertychange", function() {

        let text = $(this).val();

        
        $("#live-search>li").remove();


        $.ajax({
            url:"/recite/public/index.php/index/outbook/fuzzyquery",
            type:"POST",
            data:{en:text},
            dataType:"json",
            error:function(e){
                console.log("error");
                
            },
            success:function(data){
                for(index in data){
                    
                    let li = $("<li></li>");

                    $(li).text(data[index]['en']);

                    $(li).click(function(){

                        window.location.href = "/recite/public/index.php/index/outbook/en.html?en="+$(this).text();

                    })

                    $("#live-search").append(li);
                    
                }

                $("#live-search").show();
            }
        })

        

    });

    let ho = 0;

    $("#live-search").hover(function(){
        ho = 1;
    },
    function(){
        ho = 2;
    });

    $("#search-input").focusout(function(){
        if(ho==1)
        {

        }
        else
        {
            $("#live-search").hide();
        }
        
    })

    




});

