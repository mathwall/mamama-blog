// Ce code sert a supprimer en ajax les articles/users/categories/etc...

$(function(){
    $("[id^=delete_category_]").on("click", function(e) {
        e.preventDefault();
        console.log("LOL");

        var id = this.getAttribute('id').replace(/delete_category_/, "");

        var request = $.ajax({
            url: "/categories/delete/",
            method: "DELETE",
            data: { id : id },
        });

        request.done(function( msg ) {
            $("[id^=tr_category_" + id +"]").hide("slow", function() {
                $(this).remove();
            })
        });

        request.fail(function( jqXHR, textStatus ) {
            alert( "Error: " + jqXHR.responseJSON.message );
        });
    });
});
