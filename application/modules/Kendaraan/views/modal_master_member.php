
<div class="modal fade modal_add_form" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="modal_user_form">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
      <form id="form-user" name="form-user">
          <div class="form-group">
            <input type="hidden" class="form-control" id="id" name="id">
            <label for="lbl_username" class="form-control-label">Nama Customer:</label>
            <input type="text" class="form-control" id="nama" name="nama_customer" autocomplete="off">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <input type="hidden" class="form-control" id="id" name="id">
            <label for="lbl_username" class="form-control-label">Nama Kendaraan:</label>
            <input type="text" class="form-control" id="nama" name="nama_kendaraan" autocomplete="off">
            <span class="help-block"></span>
          </div>
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="lbl_username" class="form-control-label">No Polisi:</label>
              <input type="text" class="form-control" id="kode_member" name="nopol" autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="form-group col-sm-6">
              <label for="lbl_username" class="form-control-label">Merek Mobil:</label>
              <select class="form-control required" name="merek" id="merek">
                <?php
                  foreach ($merek as $row) {
                ?>
                    <option value="<?= $row['id'];?>"><?= $row['nama_merek'];?></option>
                <?php  
                  }
                ?>
              
              </select>
              <span class="help-block"></span>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-sm-6">
              <label for="lbl_username" class="form-control-label">Telp:</label>
              <input type="text" class="form-control" id="hp" name="hp" autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="form-group col-sm-6">
              <label for="lbl_username" class="form-control-label">Warna:</label>
              <input type="text" class="form-control" id="warna" name="warna" autocomplete="off">
              <span class="help-block"></span>
            </div>
          </div>
          <div class="form-group">
            <label for="lbl_username" class="form-control-label">Keluhan:</label>
            <textarea type="text" class="form-control" id="keluhan" name="keluhan" autocomplete="off"></textarea>
            <span class="help-block"></span>
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn_outline" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn_1" id="btnSave" onclick="update_kendaraan()">Simpan</button>
      </div>
    </div>
  </div>
</div>
