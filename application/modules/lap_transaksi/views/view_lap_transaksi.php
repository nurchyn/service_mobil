<?php
  $id_jenis = $this->input->get('jenis');
?>

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

  <!-- begin:: Content Head -->
  <div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
      <div class="kt-subheader__main">
        <h3 class="kt-subheader__title">
          <?= $this->template_view->nama('judul'); ?>
        </h3>
      </div>
    </div>
  </div>
  <!-- end:: Content Head -->

  <!-- begin:: Content -->
  <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    
    <div class="kt-portlet kt-portlet--mobile">
      <form class="kt-form" action="<?=base_url('lap_transaksi')?>" method="get">
        <div class="kt-portlet__body">
          <div class="kt-section kt-section--first">
            <div class="form-group row">
              <div class="col-3">
                <label class="col-form-label">Mulai</label>
                <div>
                  <?php if($this->input->get('mulai')) { ?>
                    <input type="text" class="form-control kt_datepicker" id="mulai" name="mulai" placeholder="Tanggal Awal" value="<?= $this->input->get('mulai'); ?>"/>
                  <?php }else{ ?>
                    <input type="text" class="form-control kt_datepicker" id="mulai" name="mulai" placeholder="Tanggal Awal" value="<?= DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->modify('-10 day')->format('d/m/Y'); ?>"/>
                  <?php } ?>
                </div>
              </div>
              <div class="col-3">
                <label class="col-form-label">Hingga</label>
                <div>
                <?php if($this->input->get('akhir')) { ?>
                  <input type="text" class="form-control kt_datepicker" id="akhir" name="akhir" placeholder="Tanggal Akhir" value="<?= $this->input->get('akhir'); ?>"/>
                <?php }else{ ?>
                  <input type="text" class="form-control kt_datepicker" id="akhir" name="akhir" placeholder="Tanggal Akhir" value="<?= DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'); ?>"/>
                <?php } ?> 
                </div>
              </div>
              
              <div class="col-6">
                <label class="col-form-label">Jenis Transasi</label>
                <div>
                  <select name="jenis" id="jenis" class="form-control select2">
                      <?php 
                        foreach ($jenis_trans as $key => $value) {
                          if($id_jenis == $value->id) {
                            echo "<option value='$value->id' selected>$value->nama_jenis [$value->kode_jenis]</option>";
                          }else{
                            echo "<option value='$value->id'>$value->nama_jenis [$value->kode_jenis]</option>";
                          }
                        }
                      ?>
                    </select>
                </div>
              </div>
              
            </div>
            
            <div class="form-group-row">
              <div class="kt-form__actions">
                <button type="submit" class="btn btn-primary">Submit</button>
                <?php if($html != '') { ?>
                  <button type="button" class="btn btn-warning" onclick="cetakLaporan()">Cetak</button>
                  <button type="button" class="btn btn-success" onclick="importExcel()">Download Excel</button>
                <?php } ?>
              </div>
            </div>
            
          </div>
        </div>
      </form>
    </div>
    <?php if($html != '') { ?>
      <div class="kt-portlet">
        <div class="kt-portlet__body">
          <div class="table-responsive">
            <div class="col-xs-12">
              <h4 style="text-align: center;"><strong> <?=$profil->nama; ?></strong></h4>
              <h5 style="text-align: center;"><strong>Laporan Transaksi <?= $jenis_trans_txt; ?></strong></h5>
            </div>
            <hr>
            <div class="col-xs-12" style="text-align: center;">
              <span>Periode : <?php echo $periode ?></span>
            </div>
            <?= $html; ?>
          </div>
        </div>
      </div>
    <?php } ?>
    
  </div>
  
</div>