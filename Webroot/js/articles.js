var comments = $("div.comments");

$("document").ready(function(){

    comments.hide();

});

$("button.comments").click(function(){
    
    if(comments.is(":hidden")){
        comments.show();
    } else {
        comments.hide();
    }
});