const cetakLaporan = () => {
    swalConfirm.fire({
        title: 'Yakin Cetak Data ?',
        text: "Data Laporan Akan Dicetak ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Cetak !',
        cancelButtonText: 'Tidak, Batalkan!',
        reverseButtons: false
      }).then((result) => {
        if (result.value) {
            let bulan = $('#bulan').val();
            let tahun = $('#tahun').val();
            $.ajax({
                url : base_url + 'lap_keuangan/cetak_laporan',
                type: "POST",
                dataType: "JSON",
                data : {bulan:bulan, tahun:tahun},
                success: function(data)
                {
                    swalConfirm.fire('Berhasil Hapus Data!', data.pesan, 'success');
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

const importExcel = () => {
    swalConfirm.fire({
        title: 'Yakin Download Excel ?',
        text: "Data Laporan Akan Di Download ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya !',
        cancelButtonText: 'Tidak !',
        reverseButtons: false
      }).then((result) => {
        if (result.value) {
            let bulan = $('#bulan').val();
            let tahun = $('#tahun').val();
            window.open( base_url + 'lap_keuangan/import_excel?bulan='+bulan+'&tahun='+tahun, '_blank');
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
        
});