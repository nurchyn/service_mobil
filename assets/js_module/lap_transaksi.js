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
            let mulai = $('#mulai').val();
            let akhir = $('#akhir').val();
            let jenis = $('#jenis').val();
            $.ajax({
                url : base_url + 'lap_transaksi/cetak_laporan',
                type: "POST",
                dataType: "JSON",
                data : {mulai:mulai, akhir:akhir, jenis:jenis},
                success: function(data)
                {
                  return;
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
            window.open( base_url + 'lap_transaksi/import_excel?bulan='+bulan+'&tahun='+tahun, '_blank');
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