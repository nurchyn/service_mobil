<html>

<head>
  <title><?php echo $title; ?></title>
  <style type="text/css">
    #outtable {
      padding: 10px;
      border: 1px solid #e3e3e3;
      width: 600px;
      border-radius: 5px;
    }

    .short {
      width: 50px;
    }

    .normal {
      width: 150px;
    }

    .tbl-outer {
      color: #070707;
    }

    .text-center {
      text-align: center;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .tebal {
      font-weight: bold;
    }

    .outer-left {
      border: 0px solid white;
      border-color: white;
      margin: 0px;
      background: white;
    }

    .head-left {
      padding-bottom: 0px;
      border: 0px solid white;
      border-color: white;
      margin: 0px;
      background: white;
    }

    .tbl-footer {
      width: 100%;
      color: #070707;
      border-top: 0px solid white;
      border-color: white;
      padding-top: 15px;
    }

    .head-right {
      padding-bottom: 0px;
      border: 0px solid white;
      border-color: white;
      margin: 0px;
    }

    .tbl-header {
      padding-top: 1px;
      width: 100%;
      color: #070707;
      border-color: #070707;
      border-top: 2px solid #070707;
    }

    #tbl_content {
      padding-top: 10px;
      margin-left: -15px;
      border-collapse: collapse;
    }

    .tbl-footer td {
      border-top: 0px;
      padding: 0px;
    }

    .tbl-footer tr {
      background: white;
    }

    .foot-center {
      padding-left: 70px;
    }

    .inner-head-left {
      padding-top: 20px;
      border: 0px solid white;
      border-color: white;
      margin: 0px;
      background: white;
    }

    .tbl-content-footer {
      width: 100%;
      color: #070707;
      padding-top: 0px;
    }

    table {
      border-collapse: collapse;
      font-family: arial;
      color: black;
      font-size: 12px;
    }

    thead th {
      text-align: center;
      font-style: bold;
    }

    .clear {
      clear: both;
    }

    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 80px;}

  </style>
</head>

<body>
  <div class="container">
    <table class="tbl-outer">
      <tr>
        
        <td align="left" class="outer-left">
          <img src="<?=base_url('assets/images/').$profil->gambar;?>" height="75" width="75">
        </td>

        <td align="right" class="outer-left" style="padding-top: 30px; padding-left:10px;">
          <p style="text-align: left; font-size: 14px" class="outer-left">
            <strong><?= $profil->nama; ?></strong>
          </p>
          <p style="text-align: left; font-size: 12px" class="outer-left"><?= $profil->alamat.' '.$profil->kelurahan.' '.$profil->kecamatan; ?></p>
          <p style="text-align: left; font-size: 12px" class="outer-left"><?= $profil->kota.', '.$profil->provinsi.' '.$profil->kode_pos; ?></p>
        </td>
        
      </tr>
    </table>

    <table class="tbl-header">
      <tr>
        <td align="center" class="head-center">
          <p style="text-align: center; font-size: 16px; padding-top:10px;" class="head-left"><strong> <?= $title; ?> </strong></p>
          <p style="text-align: center; font-size: 16px; padding-top:10px;" class="head-left"><strong> <?= $periode; ?> </strong></p>
        </td>
      </tr>
    </table>
    
    <table id="tbl_content" class="table table-bordered table-hover" cellspacing="0" width="100%" border="2">
      <thead>
        <tr>
          <th style="width: 5%; text-align: center;">No</th>
          <th style="text-align: center;">Tanggal</th>
          <th style="text-align: center;">Kode</th>
          <th style="text-align: center;">Jenis</th>
          <th style="text-align: center;">Penerimaan</th>
          <th style="text-align: center;">Pengeluaran</th>
          <th style="text-align: center;">Saldo Akhir</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $obj_date = new DateTime();
        $obj_date2 = new DateTime($tahun.'-'.$bulan.'-01');

        if($data['saldo'] != null) {
          if($data['saldo'] == 'kosong') {
            // hasil hitungan
            $saldonya = 0;
          }else{
            // hasil hitungan
            $saldonya = (float)$data['saldo'];
          }

          echo '<tr>
                <td class="text-center">'.$no++.'</td>
                <td>'.$obj_date2->format('d/m/Y').'</td>
                <td>-</td>
                <td>Saldo Awal</td>
                <td align="right">-</td>
                <td align="right">-</td>
                <td align="right">'.number_format($saldonya, 0 ,',','.').'</td>
              </tr>';
        }

        $tot_penerimaan = 0;
        $tot_pengeluaran = 0;
        foreach ($data['data'] as $key => $val) { 
          $penerimaan = 0;
				  $pengeluaran = 0;

          if($val->cashflow == 'in') {
            $saldonya += $val->total_harga;
            $penerimaan = $val->total_harga;
            $tot_penerimaan += $val->total_harga;
          }else{
            $saldonya -= $val->total_harga;
            $pengeluaran = $val->total_harga;
            $tot_pengeluaran += $val->total_harga;
          }

          echo '<tr>
                  <td class="text-center">'.($no++).'</td>
                  <td>'.$obj_date->createFromFormat('Y-m-d', $val->tgl_trans)->format('d/m/Y').'</td>
                  <td>'.$val->kode_jenis.'</td>
                  <td>'.$val->nama_jenis.'</td>
                  <td align="right">'.number_format($penerimaan, 0 ,',','.').'</td>
                  <td align="right">'.number_format($pengeluaran, 0 ,',','.').'</td>
                  <td align="right">'.number_format($saldonya, 0 ,',','.').'</td>
          </tr>';
        } 
        
        echo '<tr>
          <td colspan="4" align="center"><strong>Grand Total</strong></td>
          <td align="right"><strong>'.number_format($tot_penerimaan, 0 ,',','.').'</strong></td>
          <td align="right"><strong>'.number_format($tot_pengeluaran, 0 ,',','.').'</strong></td>
          <td align="right"><strong>'.number_format($saldonya, 0 ,',','.').'</strong></td>
        </tr>';
        ?>
      </tbody>
    </table>

    <p style="font-size: 12px;font-style: italic;">Tanggal Cetak Laporan : <?= $obj_date->format('d/m/Y H:i:s'); ?></p>
  </div>
</body>

</html>