<!DOCTYPE html>
<html>

<head>
  <style>
    table,
    th,
    td {
      border: 1px solid black;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 5px;
    }
  </style>
</head>

<body>

  <h3>Denormalisasi</h3>
  <table style="width:100%">
    <tr>
      <th colspan="6">bobot</th>
      <th colspan="2">input ke hidden</th>
      <th colspan="2">hidden</th>
      <th colspan="2">aktivasi</th>
      <th>bobot hidden ke output</th>
      <th>output</th>
    </tr>
    <tr>
      <td><?= $denormalisasi['v11']; ?></td>
      <td><?= $denormalisasi['v12']; ?></td>
      <td><?= $denormalisasi['v21']; ?></td>
      <td><?= $denormalisasi['v22']; ?></td>
      <td><?= $denormalisasi['bias_1']; ?></td>
      <td><?= $denormalisasi['bias_2']; ?></td>

      <td><?= $denormalisasi['aktivasi_z1']; ?></td>
      <td><?= $denormalisasi['aktivasi_z2']; ?></td>
      <td><?= $denormalisasi['hidden_z1']; ?></td>
      <td><?= $denormalisasi['hidden_z2']; ?></td>
      <td><?= $denormalisasi['w1']; ?></td>
      <td><?= $denormalisasi['w2']; ?></td>

      <td><?= $denormalisasi['b']; ?></td>
      <td><?= $denormalisasi['y']; ?></td>
    </tr>


  </table>
  <h3>Hasil : <?= $denormalisasi['hasil_denom']; ?></h3>
  <hr>
  <table style="width:100%">
    <tr>
      <th>Epoh ke-</th>
      <th colspan="4">Data</th>
      <th colspan="6">Bobot input ke hidden</th>
      <th colspan="2">Hidden</th>
      <th colspan="2">Aktivasi</th>
      <th colspan="3">Bobot hidden ke output</th>
      <th colspan="2">Output</th>
      <th>Faktor error Y</th>
      <th colspan="3">Perubahan bobot w</th>
      <th colspan="3">Faktor error Z</th>
      <th colspan="6">Perubahan bobot v</th>
      <th colspan="3"></th>
    </tr>
    <tr>
      <th>ke-</th>
      <th>x1</th>
      <th>x2</th>
      <th>t</th>
      <th>v11</th>
      <th>v12</th>
      <th>v21</th>
      <th>v22</th>
      <th>bias 1</th>
      <th>bias 2</th>
      <th>Z1</th>
      <th>Z2</th>
      <th>Z1</th>
      <th>Z2</th>
      <th>w1</th>
      <th>w2</th>
      <th>b</th>
      <th>Y</th>
      <th>aktivasi</th>
      <th>δ</th>
      <th>∆w1</th>
      <th>∆w2</th>
      <th>∆w bias</th>
      <th>δ_ net1</th>
      <th>δ_ net2</th>
      <th>δ1</th>
      <th>δ2</th>
      <th>∆v11</th>
      <th>∆v12</th>
      <th>∆v21</th>
      <th>∆v22</th>
      <th>∆vb1</th>
      <th>∆vb2</th>
      <th>error</th>
      <th>error^2</th>
      <th>MSE</th>
    </tr>
    <?php for ($i = 0; $i < count($perhitungan_det, true); $i++) { ?>
      <?php for ($z = 0; $z < count(json_decode($perhitungan_det[$i]['arr_bobot_v11'], true)); $z++) { ?>

        <tr <?php if ($perhitungan_det[$i]['is_stop'] == '1') { ?> style="color:red;" <?php } ?>>
          <td><?= $perhitungan_det[$i]['epoch_ke']; ?></td>
          <td><?= json_decode($perhitungan['arr_input_x1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan['arr_input_x2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan['arr_input_t'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_v11'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_v12'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_v21'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_v22'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_bias1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_bias2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_hidden_z1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_hidden_z2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_aktivasi_z1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_aktivasi_z2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_w1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_w2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_bobot_b'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_y'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_aktivasi'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_faktor_error_y'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_w1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_w2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_w_bias'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_faktor_error_z_net1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_faktor_error_z_net2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_faktor_error_z1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_faktor_error_z2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_v11'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_v12'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_v21'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_v22'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_vb1'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_perubahan_bobot_vb2'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_error'], true)[$z]; ?></td>
          <td><?= json_decode($perhitungan_det[$i]['arr_output_error2'], true)[$z]; ?></td>
          <td><?= $perhitungan_det[$i]['mse']; ?></td>
        </tr>
      <?php  } ?>

    <?php } ?>

  </table>
  <br>



</body>

</html>