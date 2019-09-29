$(document).ready(function(){

    $(".tr-word .td-audio").click(function(){
        
        let au = $(this).find("audio")[0];

        au.play();

    });

    let num = $(".content").attr("num");

    $(".row-word").hide();

    $(".row-word").eq(0).show();

    $(".btn-show").click(function(){
        let row = $(this).parents(".row-word");

        let op  = $(row).attr("op");

        if(op=="1")
        {
            OpenHidden(row);
        }
        else
        {
            ChangeWord(row)
        }
    })

    $(".btn-forget").click(function(){
        let row = $(this).parents(".row-word");

        let op  = $(row).attr("op");

        let wordid = $(this).attr("wordid");


        memorize_record(wordid,-1);

        if(op=="1")
        {
            OpenHidden(row);
        }
        else
        {
            ChangeWord(row)
        }
        $(this).hide()
    });

    $(".btn-easy").click(function(){
        let row = $(this).parents(".row-word");
        let wordid = $(this).attr("wordid");
        memorize_record(wordid,1);
        ChangeWord(row)
    });

    function OpenHidden(row)
    {
        $(row).attr("op","2");

        $(row).find(".btn-hidden").removeClass("btn-hidden");
    }

    function ChangeWord(row)
    {
        let next_row = $(row).next();
        
        

        if(next_row[0])
        {
            $(".list-current-span").text(parseInt($(".list-current-span").text())+1);
            $(next_row).show();
            $(row).hide();
        }
        else
        {
            $(location).attr("href","/recite/public/index.php/index/account/memorize?num="+num)
        }
    }

    function memorize_record(wordid,score)
    {
        $.ajax({
            url:"/recite/public/index.php/index/account/memorize_record",
            type:"POST",
            data:{wordid:wordid,score:score},
            dataType:"json",
            error:function(e){
                console.log("error");
                
            },
            success:function(data){
                console.log(data);
            }
        })
    }


});