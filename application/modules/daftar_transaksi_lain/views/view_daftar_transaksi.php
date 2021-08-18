<?php
  $this->load->model('m_global');
  $slug = $this->input->get('tipe');
  $q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');
  if($q_jenis) {
    $id_jenis = $q_jenis->id;
  }else{
    $id_jenis = 2;
  }
?>

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

  <!-- begin:: Content Head -->
  <div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
      <div class="kt-subheader__main">
        <h3 class="kt-subheader__title">
          <?= $this->template_view->nama('judul').' - '.$title; ?>
        </h3>
      </div>
    </div>
  </div>
  <!-- end:: Content Head -->

  <!-- begin:: Content -->
  <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    
    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
          <div class="row" style="">
            <div class="col-md-4 row">
              <label class="col-form-label col-lg-3">Mulai</label>
              <div class="col-lg-9">
                <input type="text" class="form-control kt_datepicker" id="tgl_filter_mulai" readonly placeholder="Tanggal Awal" value="<?= DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->modify('-10 day')->format('d/m/Y'); ?>"/>
              </div>
            </div>
            <div class="col-md-4 row">
              <label class="col-form-label col-lg-3">Hingga</label>
              <div class="col-lg-9">
                <input type="text" class="form-control kt_datepicker" id="tgl_filter_akhir" readonly placeholder="Tanggal Akhir" value="<?= DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'); ?>"/>
              </div>
            </div>
            <div class="col-md-4 row">
              <div class="col-lg-12">
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

          <div class="row">
            <div class="col-md-12 row">
                
                <div class="col-12 btn-group btn-group">
                  <button type="button" class="btn btn-primary btn-md" onclick="filter_tabel()">Cari</button>
                </div>
            
            </div>
          </div>
        </div>
      </div>
      <div class="kt-portlet__body">

        <!--begin: Datatable -->
        <table class="table table-striped- table-bordered table-hover table-checkable" id="tabel_list_transaksi">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th>User</th>
              <th>Harga</th>
              <th>Bulan</th>
              <th>Tahun</th>
              <th style="width: 5%;">Aksi</th>
            </tr>
          </thead>
        </table>

        <!--end: Datatable -->
      </div>
    </div>
  </div>
  
</div>



