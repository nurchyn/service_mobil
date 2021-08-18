var save_method;
var table;

$(document).ready(function() {

    //force integer input in textfield
    $('input.numberinput').bind('keypress', function (e) {
        return (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) ? false : true;
    });

    getTable();

    console.log(table);
    
   

    $(".modal").on("hidden.bs.modal", function(){
        reset_modal_form();
        reset_modal_form_import();
    });
});	

function add_menu()
{
    reset_modal_form();
    save_method = 'add';
	$('#modal_user_form').modal('show');
	$('#modal_title').text('Tambah Data Supplier'); 
}

function save_onderdil()
{
    var url;
    var txtAksi;
    save_method = 'add';

    url = base_url + 'kendaraan/add_onderdil';
    txtAksi = 'Add Onderdil';
   
    
    var form = $('#form-onderdil')[0];
    var data = new FormData(form);
    
    // $("#btnSave").prop("disabled", true);
    // $('#btnSave').text('Menyimpan Data'); //change button text
    swalConfirmDelete.fire({
        title: 'Perhatian !!',
        text: "Apakah anda yakin menambah data ini ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: url,
                data: data,
                dataType: "JSON",
                processData: false, // false, it prevent jQuery form transforming the data into a query string
                contentType: false, 
                cache: false,
                timeout: 600000,
                success: function (data) {
                    if(data.status) {
                        swal.fire("Sukses!!", "Aksi "+txtAksi+" Berhasil", "success");
                        $("#form-onderdil")[0].reset();
                        getTable();
                        // table.ajax.reload();
                    }else {
                        console.log('kesini');
                        swal.fire("Gagal!!", "Aksi "+data.pesan+"", "error");
                        $("#form-onderdil")[0].reset();
                        //table.ajax.reload();
                        getTable();
                    }
                },
                error: function (e) {
                    console.log("ERROR : ", e);
                    $("#btnSave").prop("disabled", false);
                    $('#btnSave').text('Simpan');
        
                    reset_modal_form();
                    $(".modal").modal('hide');
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
      })
}

function hapus_onderdil(id)
{
  // alert('kesini'); exit;
  swalConfirm.fire({
    title: 'Apakah Anda Yakin ?',
    text: "ingin menghapus daftar barang ini ?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya !',
    cancelButtonText: 'Tidak !',
    reverseButtons: true
  }).then((result) => {
    if (result.value) {
        $.ajax({
            url : base_url + 'kendaraan/hapus_onderdil',
            type: "POST",
            dataType: "JSON",
            data : {id : id},
            success: function(data)
            {
                swalConfirm.fire('Berhasil !', data.pesan, 'success');
                getTable();
                
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

function getTable(){
    var id = $('#id_kendaraan').val();
	//datatables
	table =  $.ajax({
        type: 'POST',
        url: base_url + 'kendaraan/list_onderdil',
        data: {id:id},
        success:function(response){
            $('#tbody').html(response);
        }
    });
}

function tes(id)
{
  var tes = '#qty_order_'+id;
  var qty = $(tes).val();
  $.ajax({
    url : base_url + 'kendaraan/change_qty',
    type: "POST",
    dataType: "JSON",
    data : {id : id, qty : qty},
    success: function(data)
    {
        // swalConfirm.fire('Berhasil !', data.pesan, 'success');
        getTable();
        
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
        Swal.fire('Terjadi Kesalahan');
    }
});
  
}



