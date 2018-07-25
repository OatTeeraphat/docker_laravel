$(function() {
    // Multiple images preview in browser
    var arr = [];
    var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {
            var filesAmount = input.files.length;
            let l = arr.length +filesAmount;
            console.log(l);
            if (l <=4){
                for (i = 0; i < filesAmount; i++) {

                    if (!/\.(jpe?g|png|gif)$/i.test(input.files[i].name)){

                        $(".gallery").addClass('is-invalid');
                        $("#gallery_error").text(input.files[i].name+' ไม่ใช่รูปภาพ');
                        return false;

                    } else {
                        arr.push(input.file);

                        var reader = new FileReader();

                        var fd = new FormData();
                        fd.append("images[]", input.files[i]);

                        reader.onload = function(event) {
                            $($.parseHTML('<img>'))
                                .attr('src', event.target.result)
                                .css('height',100)
                                .prependTo(placeToInsertImagePreview);
                        }
                        reader.readAsDataURL(input.files[i]);
                        $(".gallery").removeClass('is-invalid');
                        $("#gallery_error").text('');
                        console.log(arr)

                    }
                }
            } else {
                $(".gallery").addClass('is-invalid');
                $("#gallery_error").text('สามารถเพิ่มรูปได้สูงสุด 4 ภาพ');
            }
        }
    };

    $('#gallery-photo-add').on('change', function() {
        imagesPreview(this, 'div.gallery');

    });



});