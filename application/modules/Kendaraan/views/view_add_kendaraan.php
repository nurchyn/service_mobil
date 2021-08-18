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
              Data Kendaraan
            <!-- <div class="kt-portlet__head-actions row">
              <div><?= $this->template_view->getAddButton(true, 'add_menu'); ?></div>
            </div> -->
          </div>
        </div>
      </div>
      <div class="kt-portlet__body">

        <!--begin: Datatable -->
        <form id="form-user" name="form-user">
          <div class="row col-sm-12">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="lbl_username" class="form-control-label">Customer:</label>
                <select class="form-control select2" name="customer" id="customer" >
                  <option value="">-- PIlih Customer --</option>
                  <?php
                    foreach ($customer as $row) {
                  ?>
                      <option value="<?= $row['id'];?>"><?= $row['nama_customer'];?></option>
                  <?php  
                    }
                  ?>
                
                </select>
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <input type="hidden" class="form-control" id="id" name="id">
                <label for="lbl_username" class="form-control-label">Nama Customer:</label>
                <input type="text" class="form-control" id="nama_customer" autocomplete="off" readonly="">
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <input type="hidden" class="form-control" id="id" name="id">
                <label for="lbl_username" class="form-control-label">Alamat Customer:</label>
                <input type="text" class="form-control" id="alamat_customer" autocomplete="off" readonly="">
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <input type="hidden" class="form-control" id="id" name="id">
                <label for="lbl_username" class="form-control-label">Telp:</label>
                <input type="text" class="form-control" id="telp" autocomplete="off" readonly="">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="lbl_username" class="form-control-label">Customer:</label>
                <select class="form-control select2" name="kendaraan" id="kendaraan">
                  <option value="">-- PIlih Kendaraan --</option>
                  <?php
                    foreach ($kendaraan as $row) {
                  ?>
                      <option value="<?= $row['id'];?>"><?= $row['nama_kendaraan'].' ( '.$row['nopol'].' )';?></option>
                  <?php  
                    }
                  ?>
                
                </select>
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <input type="hidden" class="form-control" id="id" name="id">
                <label for="lbl_username" class="form-control-label">Nama Kendaraan:</label>
                <input type="text" class="form-control" id="nama_kendaraan" autocomplete="off" readonly>
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <label for="lbl_username" class="form-control-label">No Polisi:</label>
                <input type="text" class="form-control" id="nopol" autocomplete="off" readonly>
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <label for="lbl_username" class="form-control-label">Merek Mobil:</label>
                <input type="text" class="form-control" id="merek" autocomplete="off" readonly>
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <label for="lbl_username" class="form-control-label">Warna:</label>
                <input type="text" class="form-control" id="warna"  autocomplete="off" readonly>
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <label for="lbl_username" class="form-control-label">Keluhan:</label>
                <textarea type="text" class="form-control" id="keluhan" name="keluhan" autocomplete="off"></textarea>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
         
         
          <!-- <div class="form-group">
            <label>Foto Profil</label>
            <div></div>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="foto" name="foto" accept=".jpg,.jpeg,.png">
              <label class="custom-file-label" id="label_foto" for="customFile">Pilih gambar yang akan diupload</label>
            </div>
          </div>
          <div class="form-group" id="div_preview_foto" style="display: none;">
            <label for="lbl_password_lama" class="form-control-label">Preview Foto:</label>
            <div></div>
            <img id="preview_img" src="#" alt="Preview Foto" height="200" width="200"/>
            <span class="help-block"></span>
          </div> -->
         
         
        </form>
        <div class="col-sm-3">
          <button type="button" class="btn btn_1" id="btnSave" onclick="save()">Simpan</button>
        </div>
        <!--end: Datatable -->
      </div>
    </div>
  </div>
  
</div>



