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
        </div>
        <div class="kt-portlet__head-toolbar">
          <div class="kt-portlet__head-wrapper">
            <div class="kt-portlet__head-actions row">
             
            </div>
          </div>
        </div>
      </div>
      <div class="kt-portlet__body">

        <!--begin: Datatable -->
        
        <form id="form-onderdil" name="form-onderdil">
          <div class="form-group col-sm-3">
            <input type="hidden" value="<?php echo $id;?>" id="id_kendaraan" name="id_kendaraan">
            <label for="lbl_username" class="form-control-label">Piih Onderdil:</label>
            <select class="form-control required" name="id_barang" id="id_barang">
                <option value="">-- Pilih Onderdil -- </option>
                <?php
                  foreach ($onderdil as $value) { ?>
                    <option value="<?= $value->id?>"><?= $value->nama_barang ?></option>
                 <?php  }
                ?>
            </select>
            <span class="help-block"></span>
          </div>
         
          <div class="form-group col-sm-3">
            <label for="lbl_username" class="form-control-label">Qty:</label>
               <input type="number" class="form-control" name="qty" >
              <span class="help-block"></span>
          </div>
          <div class="form-group col-sm-3">
             <button type="button" class="btn btn_1" id="btnSave" onclick="save_onderdil()">Simpan</button>
             <a href="<?php echo base_url('kendaraan/view_kendaraan');?>" class="btn btn-secondary">Kembali</a>
          </div>
        </form>
        <table class="table table-striped" id="tabel_onderdil">
            <thead>
                <tr>
                    <th>Onderdil</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Sub Total</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody id="tbody">
            </tbody>
        </table>

        <!--end: Datatable -->
      </div>
    </div>
  </div>
  
</div>



