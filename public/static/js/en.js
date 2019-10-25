$(document).ready(function(){

    $("#WordAddition-input-en").change(function(){

        let en = $(this).val();
        
        $.ajax({
            url:"/recite/public/index.php/index/enbook/isExist",
            type:"POST",
            data:{en:en},
            dataType:"json",
            error:function(e){
                console.log("error");
                
            },
            success:function(data){
                console.log(data);
                if(data["status"]==200)
                {
                    alert("English word already exist!");
                }
            }
        })


    });

    function ChangeScore(id,int)
    {
        $.ajax({
            url:"/recite/public/index.php/index/enbook/changeScore",
            type:"POST",
            data:{id:id,score:int},
            dataType:"json",
            error:function(e){
                console.log("error");
                
            },
            success:function(data){
                console.log(data);
            }
        })
    }

    $(".AddScore").click(function(){

        ChangeScore($(this).attr("wordid"),5);
        alert("成功加5分")
        location.reload();
    });

    $(".SubtractScore").click(function(){

        ChangeScore($(this).attr("wordid"),-5);
        alert("成功减5分")
        location.reload();
    });

    $(".collected").click(function(){
        let wordid = $(this).attr("wordid");

        let listid = $(this).attr("listid");

        addColected(wordid,listid);

        $("#panel-AddToCollect").modal('hide');
    });

    function addColected(wordid,listid)
    {
        $.ajax({
            url:"/recite/public/index.php/index/account/addColected",
            type:"POST",
            data:{wordid:wordid,listid:listid},
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