let slug_trans;
let activeModal;

const cekDanSetValue = (txt_div_modal) => {
    let retval = $.ajax({
        type: "post",
        url: base_url+"trans_lain/get_old_data",
        data: {
            menu:txt_div_modal,
        },
        dataType: "json",
        success: (objData) => {setModalFieldValue(objData)},
    });

    return;
}

const setModalFieldValue = (objData) => {
    console.log(objData);
    if(objData.menu == 'pembelian') {
        reloadTabelFormPembelian(objData.data);
    }else if(objData.menu == 'penggajian'){
        reloadTabelFormPenggajian(objData.data);
    }else if(objData.menu == 'investasi'){
        reloadTabelFormInvestasi(objData.data);
    }else if(objData.menu == 'operasional'){
        reloadTabelFormOperasional(objData.data);
    }else if(objData.menu == 'pengeluaran-lain-lain'){
        reloadTabelFormPengeluaranLain(objData.data);
    }else if(objData.menu == 'penerimaan-lain-lain'){
        reloadTabelFormPenerimaanLain(objData.data);
    }
}


////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {

    //force integer input in textfield
    $('input.numberinput').bind('keypress', function (e) {
        return (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) ? false : true;
    });

    $(document).on('click', '.div_menu', function(){
        var nama_menu = $(this).data('id');
        slug_trans = $(this).data('slug');
        activeModal =  nama_menu+'-modal';
        cekDanSetValue(activeModal);
        $("a.btn_direct_data").attr("href", base_url+"daftar_transaksi_lain?tipe="+slug_trans);
        $('#'+nama_menu+'-modal').modal('show');
    });
    
    //////////////////////////////////////////////////////////////
});

function formatMoney(number) {
    var value = number.toLocaleString(
        'id-ID', 
        { minimumFractionDigits: 2 }
    );
    return value;
}

function save(id_form)
{
    let str1 = '#';
    let id_element = str1.concat(id_form);
    var form = $(id_element)[0];
    //console.log(form);
    var data = new FormData(form);
    data.append('slug_trans', slug_trans);
    $("#btnSave").prop("disabled", true);
    $('#btnSave').text('Menyimpan Data'); //change button text
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: base_url+'trans_lain/simpan_'+id_form,
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false, 
        cache: false,
        timeout: 600000,
        success: function (data) {
            if(data.status) {
                swal.fire({
                    title: "Sukses!!", 
                    text: data.pesan, 
                    type: "success"
                }).then(function() {
                    $("#btnSave").prop("disabled", false);
                    $('#btnSave').text('Simpan');      
                    if(id_form == 'form_pembelian') {
                        reloadTabelFormPembelian();
                    }else if(id_form == 'form_penggajian'){
                        reloadTabelFormPenggajian();
                    }else if(id_form == 'form_investasi'){
                        reloadTabelFormInvestasi();
                    }else if(id_form == 'form_operasional'){
                        reloadTabelFormOperasional();
                    }else if(id_form == 'form_out_lain'){
                        reloadTabelFormPengeluaranLain();
                    }else if(id_form == 'form_in_lain'){
                        reloadTabelFormPenerimaanLain();
                    }else{
                        $('#'+activeModal).modal('hide');
                    }
                });
                // swal.fire("Sukses!!", data.pesan, "success");     
            }else {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    if (data.is_select2[i] == false) {
                        $('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]).addClass('invalid-feedback');
                    }else{
                        //ikut style global
                        $('[name="'+data.inputerror[i]+'"]').next().next().text(data.error_string[i]).addClass('invalid-feedback-select');
                    }
                }

                $("#btnSave").prop("disabled", false);
                $('#btnSave').text('Simpan');
            }
        },
        error: function (e) {
            console.log("ERROR : ", e);
            $("#btnSave").prop("disabled", false);
            $('#btnSave').text('Simpan');
        }
    });
}

function reset_form(jqIdForm) {
    $(':input','#'+jqIdForm)
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .prop('checked', false)
        .prop('selected', false);
}

function get_uri_segment(segment) {
    var pathArray = window.location.pathname.split( '/' );
    return pathArray[segment];
}
