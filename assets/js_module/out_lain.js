const hitungTotalOut = () => {
    let harga = $('#harga_out').inputmask('unmaskedvalue');
    let qty = $('#qty_out').inputmask('unmaskedvalue');
    
    harga = harga.replace(",", ".");
    hargaFix = parseFloat(harga).toFixed(2);
    
    let total = hargaFix * qty;
    let totalFix = Number(total).toFixed(2);
    $('#hargatot_out').val(formatMoney(Number(totalFix)));

    // set raw value
    $('#harga_out_raw').val(hargaFix);
    $('#hargatot_out_raw').val(totalFix);
}

const reloadTabelFormPengeluaranLain = (objData=null) => {
    $('#CssLoader').removeClass('hidden');
    $.ajax({
        type: "post",
        url: base_url+"trans_lain/load_form_tabel_pengeluaran_lain",
        data:{data:objData, activeModal:activeModal},
        dataType: "json",
        success: function (response) {
           $('#CssLoader').addClass('hidden');
           $('#tabel_modal_pengeluaran_lain tbody').html(response.html);
        }
    });
}

const hapus_pengeluaran_lain = (id) => {
    swalConfirmDelete.fire({
        title: 'Hapus Data Pengeluaran ?',
        text: "Data Akan dihapus ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus Data !',
        cancelButtonText: 'Tidak, Batalkan!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
            $.ajax({
                url : base_url + 'trans_lain/delete_data_pengeluaran_lain',
                type: "POST",
                dataType: "JSON",
                data : {id:id},
                success: function(data)
                {
                    swalConfirm.fire('Berhasil Hapus Data!', data.pesan, 'success');
                    reloadTabelFormPengeluaranLain();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    Swal.fire('Terjadi Kesalahan');
                }
            });
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalConfirm.fire(
            'Dibatalkan',
            'Aksi Dibatalakan',
            'error'
          )
        }
    });
}


$(document).ready(function() {
    
    $("#item_out").select2({
        // tags: true,
        //multiple: false,
        tokenSeparators: [',', ' '],
        minimumInputLength: 0,
        minimumResultsForSearch: 5,
        ajax: {
            url: base_url+'master_item_trans/get_select_pengeluaran_lain',
            dataType: "json",
            type: "GET",
            data: function (params) {

                var queryParameters = {
                    term: params.term
                }
                return queryParameters;
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.text,
                            id: item.id,
                            harga: item.harga
                        }
                    })
                };
            }
        }
    });

    $('#item_out').on('select2:selecting', function(e) {
        let data = e.params.args.data;
        let hargaFix = Number(data.harga).toFixed(2);
        $('#harga_out').val(formatMoney(Number(hargaFix)));
        $('#harga_out_raw').val(hargaFix);
        // let tgl_lhr = data.tanggal_lahir;
        // $('#tanggal_lahir').val(tgl_lhr.split("-").reverse().join("/"));
    }); 
    
});