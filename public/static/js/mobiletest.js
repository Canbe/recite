$(document).ready(function(){

    $(".content .input-test").change(function(){
        let val_test = $(this).val();
        
        let val_correct = $(this).attr("words");
        

        $(this).parents(".tr-word").find(".answer").removeClass("btn-hidden");

        Open(this,val_test!=""&&val_test==val_correct);
        NextInput(this);
    })
    //当按下enter键时跳转到下一个input
    $('.content .input-test').keydown(function(e){
        if(e.keyCode==13){
            NextInput(this);
        }
    });

    function Open(inp,bool)
    {
        let tr = $(inp).parents(".tr-word")

        let val_id = $(inp).attr("wordid");
        

        if(bool){
            ChangeScore(val_id,2);
            $("#correct_input").text(parseInt($("#correct_input").text())+1);
            
        }
        else
        {
            $(inp).addClass("btn-woring");
            ChangeScore(val_id,-1);
        }

        $(inp).attr("disabled","disabled")
    }

    function NextInput(inp)
    {
        let tr = $(inp).parents(".tr-word").next();
            
            if(tr[0])
            {
                $(tr).find(".input-test").focus();
            }
    }

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
    
});