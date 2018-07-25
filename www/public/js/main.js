$(document).ready(function($) {

    $('#phone').on('keyup', function() {
        let phoneNum = $( this ).val()
        let phone = phoneNum.replace(/-/g, "");
        let k = phoneNum.substring(1, 2) === '-';
        let keyNumber = !isNaN(Number(phone));
        if (!keyNumber || k) {
            $( this ).val('');
        }
    })

    $('#phone').on('blur', function () {
        let phoneNum = $( this ).val()
        let k = phoneNum.substring(0, 1) !== '-';
        if (phoneNum.length < 10){
            if (!k){
                $( this ).val('-');
            }else{
                $ ( this ).val('');
            }

        }
    })

    $('#phone').bind("keyup change",function(e) {

        var val = this.value.replace(/\D/g, '');
        var value = this.value;
        var newVal = '';

        var thTEL =  ['02','03','04','05','07'].includes(val.substr(0, 2));
        var thBKK =  ['02'].includes(val.substr(0, 2));
        var thMobile =  ['06','08','09'].includes(val.substr(0, 2)) && !thTEL;

        if ( thTEL ){

            $('#phone').attr("maxlength", 11 ).removeClass('is-invalid');
            $('#phone_error').text('');

            if( !thBKK ){

                while ( val.length > 3 ) {
                    newVal += val.substr(0, 3) + '-';
                    val = val.substr(3);
                }
                newVal += val;
                this.value = newVal;
                $( this ).val(newVal);

            } else {

                let i = 2;
                while ( val.length > i ) {
                    newVal += val.substr(0, i) + '-';
                    val = val.substr(i);
                    i++
                }
                newVal += val;
                this.value = newVal;
                $( this ).val(newVal);

            }
        }

        else if( thMobile ){

                $('#phone').attr("maxlength", 12 ).removeClass('is-invalid');
                $('#phone_error').text('');

                let i = 0;
                while ( val.length > 3 && i < 2 ) {
                    newVal += val.substr(0, 3) + '-';
                    val = val.substr(3);
                    i++
                }
                newVal += val;
                this.value = newVal;
                $( this ).val(newVal);

        } else if( !thMobile && !thTEL && val.length >= 2 ){

            $('#phone').addClass('is-invalid');
            $('#phone_error').text('ไม่มีหมายเลขนี้ในประเทศไทย')
            $( this ).val('');

        }

    });

} );



