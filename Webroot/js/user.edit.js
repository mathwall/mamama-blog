// Ce code sert a rafraichir l'avatar des qu'on selectionne une nouvelle image d'upload
$(function(){
    $('#avatar').change(function(){
        console.log("LOL");

        var input = this;
        var url = $(this).val();
        var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
        if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg"))
        {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#avatar-pic').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
});
