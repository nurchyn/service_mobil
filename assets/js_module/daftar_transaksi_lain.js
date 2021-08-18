var save_method;
var table;

$(document).ready(function() {
    filter_tabel();

    //force integer input in textfield
    $('input.numberinput').bind('keypress', function (e) {
        return (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) ? false : true;
    });
});

const formatRupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumSignificantDigits: 1
});

const formatMoney = (number) => {
    var value = number.toLocaleString(
        'id-ID', 
        { minimumFractionDigits: 2 }
    );
    return value;
}

const formatTanggal = (tgl) => {
  let formatnya = 'DD-MM-YYYY HH:mm:ss';
  let objDate = new Date(tgl);
  return moment(objDate).format(formatnya);
};

const formatTanggalCustom = (tgl, format) => {
    let formatnya = format;
    let objDate = new Date(tgl);
    return moment(objDate).format(formatnya);
};

const save = (id_form) => {
    let str1 = '#';
    let id_element = str1.concat(id_form);
    var form = $(id_element)[0];
    var data = new FormData(form);
    
    $("#btnSave").prop("disabled", true);
    $('#btnSave').text('Menyimpan Data'); //change button text
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: base_url+'daftar_transaksi_lain/simpan_'+id_form,
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
                    $(".modal").modal('hide');
                    filter_tabel();
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

const filter_tabel = () => {
    //datatables
    let tglAwal = $('#tgl_filter_mulai').val();
    let tglAkhir = $('#tgl_filter_akhir').val();
    let jenis = $('#jenis').val();

	table = $('#tabel_list_transaksi').DataTable({
        destroy: true,
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
		ajax: {
			url  : base_url + "daftar_transaksi_lain/list_transaksi",
			type : "POST",
            data : {tglAwal:tglAwal, tglAkhir:tglAkhir, jenis:jenis},
		},
        order: [[ 0, "desc" ]],

		//set column definition initialisation properties
		columnDefs: [
			{
				targets: [-1], //last column
				orderable: false, //set not orderable
			},
            // {
            //     targets: [4,5,6],
            //     className: 'dt-body-right'
            // }
		],
    });
};

const hitungTotalBeli = () => {
    let harga = $('#harga_beli').inputmask('unmaskedvalue');
    let qty = $('#qty_beli').inputmask('unmaskedvalue');
    
    harga = harga.replace(",", ".");
    hargaFix = parseFloat(harga).toFixed(2);
    
    let total = hargaFix * qty;
    let totalFix = Number(total).toFixed(2);
    $('#hargatot_beli').val(formatMoney(Number(totalFix)));

    // set raw value
    $('#harga_beli_raw').val(hargaFix);
    $('#hargatot_beli_raw').val(totalFix);
}

const hitungTotalGaji = () => {
    let harga = $('#harga_gaji').inputmask('unmaskedvalue');
    harga = harga.replace(",", ".");
    hargaFix = parseFloat(harga).toFixed(2);
    $('#harga_gaji_raw').val(hargaFix);
}

const hitungTotalInvestasi = () => {
    let harga = $('#harga_inves').inputmask('unmaskedvalue');
    harga = harga.replace(",", ".");
    hargaFix = parseFloat(harga).toFixed(2);
    $('#harga_inves_raw').val(hargaFix);
}

const hitungTotalOperasional = () => {
    let harga = $('#harga_op').inputmask('unmaskedvalue');
    harga = harga.replace(",", ".");
    hargaFix = parseFloat(harga).toFixed(2);
    $('#harga_op_raw').val(hargaFix);
}

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

const hitungTotalIn = () => {
    let harga = $('#harga_in').inputmask('unmaskedvalue');
    let qty = $('#qty_in').inputmask('unmaskedvalue');
    
    harga = harga.replace(",", ".");
    hargaFix = parseFloat(harga).toFixed(2);
    
    let total = hargaFix * qty;
    let totalFix = Number(total).toFixed(2);
    $('#hargatot_in').val(formatMoney(Number(totalFix)));

    // set raw value
    $('#harga_in_raw').val(hargaFix);
    $('#hargatot_in_raw').val(totalFix);
}

const loadModalPembelian = (dataTrans) => {
    (() => {
        $("#item_beli").select2({
            tokenSeparators: [',', ' '],
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            ajax: {
                url: base_url+'master_item_trans/get_select_pembelian',
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

        $("#sup_beli").select2({
            // tags: true,
            //multiple: false,
            tokenSeparators: [',', ' '],
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            ajax: {
                url: base_url+'master_supplier/get_select_supplier',
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
                            }
                        })
                    };
                }
            }
        });
    })();

    $('#item_beli').on('select2:selecting', function(e) {
        let data = e.params.args.data;
        let hargaFix = Number(data.harga).toFixed(2);
        $('#harga_beli').val(formatMoney(Number(hargaFix)));
        $('#harga_beli_raw').val(hargaFix);
    });

    $('#tgl_beli').val(formatTanggalCustom(dataTrans.tgl_trans, 'DD/MM/YYYY'));
     
    $("#item_beli").append(() => {
        return $("<option selected='selected'></option>").val(dataTrans.id_item_trans).text(dataTrans.nama_item);
    }).trigger('change');    

    $("#sup_beli").append(() => {
        return $("<option selected='selected'></option>").val(dataTrans.id_supplier).text(dataTrans.nama_supplier);
    }).trigger('change');
    
    $('#id_trans_beli').val(dataTrans.id);
    $('#id_jenis_beli').val(dataTrans.id_jenis_trans);
    $('#qty_beli').val(Number(dataTrans.qty));
    $('#harga_beli').val(formatMoney(Number(Number(dataTrans.harga_satuan).toFixed(2))));
    hitungTotalBeli();
    $('#div-pembelian-modal').modal('show');
}

const loadModalPenggajian = (dataTrans) => {
    (() => {
        $("#item_gaji").select2({
            // tags: true,
            //multiple: false,
            tokenSeparators: [',', ' '],
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            ajax: {
                url: base_url+'master_item_trans/get_select_penggajian',
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

    })();

    $('#item_gaji').on('select2:selecting', function(e) {
        let data = e.params.args.data;
        let hargaFix = Number(data.harga).toFixed(2);
        $('#harga_gaji').val(formatMoney(Number(hargaFix)));
        $('#harga_gaji_raw').val(hargaFix);
    });

    $('#id_trans_gaji').val(dataTrans.id);
    $('#id_jenis_gaji').val(dataTrans.id_jenis_trans);

    // $('#tgl_beli').val(formatTanggalCustom(dataTrans.tgl_trans, 'DD/MM/YYYY'));
    $("#bulan_gaji").val(dataTrans.bulan_trans).trigger('change');
    $("#tahun_gaji").val(dataTrans.tahun_trans).trigger('change');
    
    $("#item_gaji").append(() => {
        return $("<option selected='selected'></option>").val(dataTrans.id_item_trans).text(dataTrans.nama_item);
    }).trigger('change');    

    $('#harga_gaji').val(formatMoney(Number(Number(dataTrans.harga_satuan).toFixed(2))));
    hitungTotalGaji();
    $('#div-penggajian-modal').modal('show');
}

const loadModalInvestasi = (dataTrans) => {
    (() => {
        $("#item_inves").select2({
            // tags: true,
            //multiple: false,
            tokenSeparators: [',', ' '],
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            ajax: {
                url: base_url+'master_item_trans/get_select_investasi',
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
    })();

    $('#item_inves').on('select2:selecting', function(e) {
        let data = e.params.args.data;
        let hargaFix = Number(data.harga).toFixed(2);
        $('#harga_inves').val(formatMoney(Number(hargaFix)));
        $('#harga_inves_raw').val(hargaFix);
    });

    $('#tgl_inves').val(formatTanggalCustom(dataTrans.tgl_trans, 'DD/MM/YYYY'));
     
    $("#item_inves").append(() => {
        return $("<option selected='selected'></option>").val(dataTrans.id_item_trans).text(dataTrans.nama_item);
    }).trigger('change');    

    $('#id_trans_inves').val(dataTrans.id);
    $('#id_jenis_inves').val(dataTrans.id_jenis_trans);
    $('#harga_inves').val(formatMoney(Number(Number(dataTrans.harga_satuan).toFixed(2))));
    hitungTotalInvestasi();
    $('#div-investasi-modal').modal('show');
}

const loadModalOperasional = (dataTrans) => {
    (() => {
        $("#item_op").select2({
            tokenSeparators: [',', ' '],
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            ajax: {
                url: base_url+'master_item_trans/get_select_operasional',
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
    })();

    $('#item_op').on('select2:selecting', function(e) {
        let data = e.params.args.data;
        let hargaFix = Number(data.harga).toFixed(2);
        $('#harga_op').val(formatMoney(Number(hargaFix)));
        $('#harga_op_raw').val(hargaFix);
    });

    $('#tgl_op').val(formatTanggalCustom(dataTrans.tgl_trans, 'DD/MM/YYYY'));
     
    $("#item_op").append(() => {
        return $("<option selected='selected'></option>").val(dataTrans.id_item_trans).text(dataTrans.nama_item);
    }).trigger('change');    

    $('#id_trans_op').val(dataTrans.id);
    $('#id_jenis_op').val(dataTrans.id_jenis_trans);
    $('#harga_op').val(formatMoney(Number(Number(dataTrans.harga_satuan).toFixed(2))));
    hitungTotalOperasional();
    $('#div-operasional-modal').modal('show');
}

const loadModalPengeluaranLain = (dataTrans) => {
    (() => {
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
    })();

    $('#item_out').on('select2:selecting', function(e) {
        let data = e.params.args.data;
        let hargaFix = Number(data.harga).toFixed(2);
        $('#harga_out').val(formatMoney(Number(hargaFix)));
        $('#harga_out_raw').val(hargaFix);
    }); 

    $('#tgl_out').val(formatTanggalCustom(dataTrans.tgl_trans, 'DD/MM/YYYY'));
     
    $("#item_out").append(() => {
        return $("<option selected='selected'></option>").val(dataTrans.id_item_trans).text(dataTrans.nama_item);
    }).trigger('change');    

    $('#id_trans_out').val(dataTrans.id);
    $('#id_jenis_out').val(dataTrans.id_jenis_trans);
    $('#qty_out').val(Number(dataTrans.qty));
    $('#harga_out').val(formatMoney(Number(Number(dataTrans.harga_satuan).toFixed(2))));
    hitungTotalOut();
    $('#div-pengeluaran-lain-lain-modal').modal('show');
}

const loadModalPenerimaanLain = (dataTrans) => {
    (() => {
        $("#item_in").select2({
            // tags: true,
            //multiple: false,
            tokenSeparators: [',', ' '],
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            ajax: {
                url: base_url+'master_item_trans/get_select_penerimaan_lain',
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
    })();

    $('#item_in').on('select2:selecting', function(e) {
        let data = e.params.args.data;
        let hargaFix = Number(data.harga).toFixed(2);
        $('#harga_in').val(formatMoney(Number(hargaFix)));
        $('#harga_in_raw').val(hargaFix);
    }); 

    $('#tgl_in').val(formatTanggalCustom(dataTrans.tgl_trans, 'DD/MM/YYYY'));
     
    $("#item_in").append(() => {
        return $("<option selected='selected'></option>").val(dataTrans.id_item_trans).text(dataTrans.nama_item);
    }).trigger('change');    

    $('#id_trans_in').val(dataTrans.id);
    $('#id_jenis_in').val(dataTrans.id_jenis_trans);
    $('#qty_in').val(Number(dataTrans.qty));
    $('#harga_in').val(formatMoney(Number(Number(dataTrans.harga_satuan).toFixed(2))));
    hitungTotalIn();
    $('#div-penerimaan-lain-lain-modal').modal('show');
}

const editTransLain = (id) =>
{
    save_method = 'update';
    $.ajax({
        url : base_url + 'daftar_transaksi_lain/edit_data',
        type: "POST",
        dataType: "JSON",
        data : {id:id},
        success: function(response)
        {
            if(response.status) {
                if(response.jenis_trans == 'pembelian') {
                    loadModalPembelian(response.data);
                }else if(response.jenis_trans == 'penggajian'){
                    loadModalPenggajian(response.data);
                }else if(response.jenis_trans == 'investasi'){
                    loadModalInvestasi(response.data);
                }else if(response.jenis_trans == 'operasional'){
                    loadModalOperasional(response.data);
                }else if(response.jenis_trans == 'out_lain'){
                    loadModalPengeluaranLain(response.data);
                }else if(response.jenis_trans == 'in_lain'){
                    loadModalPenerimaanLain(response.data);
                }
            }else{
                swalConfirm.fire(
                    'Error',
                    response.pesan,
                    'error'
                );
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

const detailTransLain = (id) => {
    $.ajax({
        url : base_url + 'daftar_transaksi_lain/get_detail_transaksi',
        type: "GET",
        dataType: "JSON",
        data : {id:id},
        success: function(response)
        {
            if(response.status) {
                $('#modal_detail_transaksi').modal('show');
                // $('#spn-invoice').text(response.data[0].kode);
                $('#spn-tanggal').text(formatTanggalCustom(response.data[0].tgl_trans, 'DD-MM-YYYY'));
                $('#spn-user').text(response.data[0].nama_user);
                $('#spn-total').text(formatRupiah.format(response.data[0].harga_total));
                $('#div_tabel_detail').html(response.html);
                $('#div_button_detail').html(response.html2);
                $('#div_supplier_modal').html(response.html3);
            }else{
                alert('Terjadi Kesalahan');
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

const deleteTransLain = (id) => {
    swalConfirmDelete.fire({
        title: 'Peringatan !',
        text: "Data Akan dihapus permanen ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus Data !',
        cancelButtonText: 'Tidak, Batalkan!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
            $.ajax({
                url : base_url + 'daftar_transaksi_lain/delete_item_trans_lain',
                type: "POST",
                dataType: "JSON",
                data : {id:id},
                success: function(data)
                {
                    if(data.status) {
                        swalConfirm.fire('Berhasil Hapus data transaksi!', data.pesan, 'success');
                        filter_tabel();
                    }else{
                        swalConfirm.fire(
                            'Error',
                            data.pesan,
                            'error'
                        );
                    }
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
  
///////////////
