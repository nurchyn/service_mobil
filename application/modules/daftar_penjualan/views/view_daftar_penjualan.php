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
          <!-- <span class="kt-portlet__head-icon">
            <i class="kt-font-brand flaticon2-line-chart"></i>
          </span>
          <h3 class="kt-portlet__head-title">
            <?= $title; ?>
          </h3> -->
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
              
                <div class="col-12 btn-group btn-group">
                  <button type="button" class="btn btn-primary btn-md" onclick="filter_tanggal()">Cari</button>
                </div>
            
            </div>
          </div>
        </div>
      </div>
      <div class="kt-portlet__body">

        <!--begin: Datatable -->
        <table class="table table-striped- table-bordered table-hover table-checkable" id="tabel_list_penjualan">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th>User</th>
              <th>Harga</th>
              <th>Bayar</th>
              <th>Kembalian</th>
              <th>Status</th>
              <th style="width: 5%;">Aksi</th>
            </tr>
          </thead>
        </table>

        <!--end: Datatable -->
      </div>
    </div>
  </div>
  
</div>



