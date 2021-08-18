<?php 
  $obj_date = new DateTime();
  $timestamp = $obj_date->format('Y-m-d H:i:s');
  $bln_now = (int)$obj_date->format('m');
  $thn_now = (int)$obj_date->format('Y');
  $thn_awal = $thn_now - 20;
  $thn_akhir = $thn_now + 20;
  $bulan = $this->input->get('bulan');
  $tahun = $this->input->get('tahun');
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
    <div class="alert alert-warning fade show" role="alert">
      <div class="alert-icon"><i class="flaticon-warning"></i></div>
      <div class="alert-text"><strong>Perhatian,</strong> Untuk Dapat Mencetak Laporan anda wajib melakukan <strong>Kunci Laporan</strong> terlebih dahulu.</div>
      <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true"><i class="la la-close"></i></span>
        </button>
      </div>
    </div>
    <div class="kt-portlet kt-portlet--mobile">
      <form class="kt-form" action="<?=base_url('lap_keuangan')?>" method="get">
        <div class="kt-portlet__body">
          <div class="kt-section kt-section--first">
            <div class="form-group row">
              <div class="col-4">
                <label class="col-form-label">Tahun</label>
                <div>
                  <select class="form-control select2" id="tahun" name="tahun" style="width: 100%;">
                    <option value="">Silahkan Pilih Tahun</option>
                    <?php 
                      for ($i=$thn_awal; $i <= $thn_akhir; $i++) { 
                        if($i == $tahun) {
                          echo '<option value="'.$i.'" selected>'.$i.'</option>';
                        }else if($i == $thn_now) {
                          echo '<option value="'.$i.'" selected>'.$i.'</option>';
                        }else{
                          echo '<option value="'.$i.'">'.$i.'</option>';
                        }
                        
                      }
                    ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-4">
                <label class="col-form-label">Bulan</label>
                <div>
                  <select class="form-control select2" id="bulan" name="bulan" style="width: 100%;">
                    <option value="">Silahkan Pilih Bulan</option>
                    <?php 
                      for ($i=1; $i <= 12; $i++) { 
                        if($i == $bulan) {
                          echo '<option value="'.$i.'" selected>'.bulan_indo($i).'</option>';
                        }else if($i == $bln_now) {
                          echo '<option value="'.$i.'" selected>'.bulan_indo($i).'</option>';
                        }else{
                          echo '<option value="'.$i.'">'.bulan_indo($i).'</option>';
                        }
                        
                      }
                    ?>
                  </select>
                </div> 
              </div>
              
              <div class="col-3" style="padding-top: 13px;">
                <label class="col-form-label"></label>
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
              <h5 style="text-align: center;"><strong>Laporan Keuangan</strong></h5>
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