// Ce code sert a supprimer en ajax les articles/users/categories/etc...

$(function(){
    $("[id^=delete_article_]").on("click", function(e) {
        e.preventDefault();
        var id = this.getAttribute('id').replace(/delete_article_/, "");

        var request = $.ajax({
            url: "/articles/delete/",
            method: "DELETE",
            data: { id : id },
        });

        request.done(function( msg ) {
            $("[id^=tr_article_" + id +"]").hide("slow", function() {
                $(this).remove();
            })
        });

        request.fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });
    });
});
