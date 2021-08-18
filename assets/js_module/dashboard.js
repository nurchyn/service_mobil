
  function changeModel() {
    if($("#model").val() == '1'){
      $(".div_tanggal").hide();
      $(".div_bulan").show();
      $(".div_tahun").hide();
    }
    else if($("#model").val() == '2') {
      $(".div_tanggal").show();
      $(".div_bulan").hide();
      $(".div_tahun").hide();
    }
    else if($("#model").val() == '3') {
      $(".div_tanggal").show();
      $(".div_bulan").hide();
      $(".div_tahun").hide();
    }
    else if ($("#model").val() == '4') {
      $(".div_tahun").show();
      $(".div_tanggal").hide();
      $(".div_bulan").hide();
    }
    else {
      $(".div_tanggal").hide();
      $(".div_bulan").hide();
      $(".div_tahun").hide();
    }
  }

  function submit_form() {
      alert('kesini'); exit;
    console.log(form);
    var eksekusi = false;
    var pesan = '';
    if($("#user").val() != null) {
      if($("#model").val() == '1'){
        if($("#bulan").val() != '') {
          if($("#tahun").val() != '') {
            eksekusi = true;
          }
          else {
            pesan = "Silahkan memilih Tahun terlebih dahulu";
          }
        }
        else {
          pesan = "Silahkan memilih Bulan terlebih dahulu";
        }
      }
      else if($("#model").val() == '2') {
        if($("#tanggal").val() != '') {
          eksekusi = true;
        }
        else {
          pesan = "Silahkan memilih Tanggal terlebih dahulu";
        }
      }
      else if($("#model").val() == '3') {
        if($("#tanggal").val() != '') {
          eksekusi = true;
        }
        else {
          pesan = "Silahkan memilih Tanggal terlebih dahulu";
        }
      }
      else if ($("#model").val() == '4') {
        if ($("#tahun2").val() != '') {
          eksekusi = true;
        }
        else{
            pesan = "Silahkan memilih Tahun terlebih dahulu";
        }
      }
      else {
        pesan = "Silahkan memilih Model Monitoring terlebih dahulu";
      }
    }
    else {
      pesan = "Silahkan memilih Minimal 1 User terlebih dahulu";
    }

    if(eksekusi) {
      $("#line-chart").empty();
      ajaxCall(form, function(result){
        response = JSON.parse(result);
        if (response.status) {
          if(response.model == 1 || response.model == 2 || response.model == 3) {
            console.log(response.model);
            new Chart(document.getElementById("line-chart"), {
              type: 'line',
              data: {
                labels: response.label,
                datasets: response.datasets
              },
              options: {
                title: {
                  display: true,
                  text: response.judul
                }
              }
            });
          }else{
            new Chart(document.getElementById("line-chart"), {
              type: 'bar',
              data: {
                labels: response.label,
                datasets: response.datasets
              },
              options: {
                title: {
                  display: true,
                  text: response.judul
                }
              }
            });
          }
        }
        else {
          sweetAlert({
              title: 'Perhatian!',
              text: response.pesan,
              type: 'warning',
              confirmButtonColor: '#3085d6',
              confirmButtonText: 'OK'
          }).then(function (isConfirm) {
          })
        }
      }, "monitoring/ambil_monitoring");
    }
    else {
      sweetAlert({
          title: 'Perhatian!',
          text: pesan,
          type: 'warning',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
      }).then(function (isConfirm) {
      })
    }
  }

//   function search()
//   {
//     url = base_url + 'home/monitoring';
//     var form = $('#form-pegawai')[0];
//     var data = new FormData(form);
//     $.ajax({
//         type: "POST",
//         enctype: 'multipart/form-data',
//         url: url,
//         data: data,
//         dataType: "JSON",
//         processData: false, // false, it prevent jQuery form transforming the data into a query string
//         contentType: false, 
//         cache: false,
//         timeout: 600000,
//         success: function (data) {
//             if(data.status) {
            
//             }else {
//                 for (var i = 0; i < data.inputerror.length; i++) 
//                 {
//                     if (data.inputerror[i] != 'jabatans') {
//                         $('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
//                         $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]).addClass('invalid-feedback'); //select span help-block class set text error string
//                     }else{
//                         $($('#jabatans').data('select2').$container).addClass('has-error');
//                     }
//                 }

//                 $("#btnSave").prop("disabled", false);
//                 $('#btnSave').text('Simpan');
//             }
//         },
//         error: function (e) {
//             console.log("ERROR : ", e);
//             $("#btnSave").prop("disabled", false);
//             $('#btnSave').text('Simpan');

//             reset_modal_form();
//             $(".modal").modal('hide');
//         }
//     });
//   }
$(document).ready(function () {
    var nama = $("[name=jenis_penjualan]").val();
    var tahun = $("[name=tahun]").val();
    monitoring(nama, tahun);
    $('#form-pegawai').submit(function () {
        url = base_url + 'home/monitoring';
        var form = $('#form-pegawai')[0];
        var data = new FormData(form);
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
                    success: function (response) {
                        if(response.status) {
                            console.log('berhasil');
                            new Chart(document.getElementById("line-chart"), {
                                type: 'bar',
                                data: {
                                  labels: response.label,
                                  datasets: response.datasets
                                },
                                options: {
                                  title: {
                                    display: true,
                                    text: response.judul
                                  }
                                }
                            });
                        }else {
                            for (var i = 0; i < data.inputerror.length; i++) 
                            {
                                if (data.inputerror[i] != 'jabatans') {
                                    $('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
                                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]).addClass('invalid-feedback'); //select span help-block class set text error string
                                }else{
                                    $($('#jabatans').data('select2').$container).addClass('has-error');
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
            
                        reset_modal_form();
                        $(".modal").modal('hide');
                    }
                });
        // sendContactForm();
        return false;
       });

       $(document).on('click', '.div_menu', function(){
        var nama_menu = $(this).data('id');
        console.log(nama_menu)
  
        if(nama_menu == 'reguler') {
          window.location.href = base_url+'kendaraan';
        }else{
          window.location.href = base_url+'kendaraan/view_kendaraan';
        }
        
                
         
    
      });
});

function monitoring(nama, tahun){
  url = base_url + 'home/monitoring';
  $.ajax({
    type: "POST",
    enctype: 'multipart/form-data',
    url: url,
    data: {jenis_penjualan:nama, tahun:tahun},
    dataType: "JSON",
    // processData: false, // false, it prevent jQuery form transforming the data into a query string
    // contentType: false, 
    // cache: false,
    timeout: 600000,
    success: function (response) {
        if(response.status) {
            console.log('berhasil');
            new Chart(document.getElementById("line-chart"), {
                type: 'bar',
                data: {
                  labels: response.label,
                  datasets: response.datasets
                },
                options: {
                  title: {
                    display: true,
                    text: response.judul
                  }
                }
            });
        }else {
            for (var i = 0; i < data.inputerror.length; i++) 
            {
                if (data.inputerror[i] != 'jabatans') {
                    $('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]).addClass('invalid-feedback'); //select span help-block class set text error string
                }else{
                    $($('#jabatans').data('select2').$container).addClass('has-error');
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

        reset_modal_form();
        $(".modal").modal('hide');
    }
  });
}
