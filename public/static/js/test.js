$(document).ready(function(){

    $(".content .input-test").change(function(){
        let val_test = $(this).val();
        
        let val_correct = $(this).attr("words");
        

        $(this).parents(".word-item").find(".answer").removeClass("btn-hidden");
        $(this).parents(".word-item").find(".hint").addClass("btn-hidden");

        Open(this,val_test!=""&&val_test==val_correct);
    })

    function Open(inp,bool)
    {
        let tr = $(inp).parents(".word-item")

        let bt_mistake = $(tr).find(".mistake")
        let bt_correct = $(tr).find(".correct")
        let val_id = $(inp).attr("wordid");

        if(bool){
            $(bt_correct).removeClass("btn-hidden")
            ChangeScore(val_id,2);
            $("#correct_input").text(parseInt($("#correct_input").text())+1);
            
        }
        else
        {
            $(bt_mistake).removeClass("btn-hidden")
            ChangeScore(val_id,-1);
            $("#woring_input").text(parseInt($("#woring_input").text())+1);
        }

        $(inp).attr("disabled","disabled")
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