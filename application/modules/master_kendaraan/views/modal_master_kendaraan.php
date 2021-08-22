
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
            <span class="help-block"></span>
          </div>
          <div class="row">
            <div class="form-group col-sm-12">
              <label for="lbl_username" class="form-control-label">Nama Kendaraan:</label>
              <input type="text" class="form-control" id="nama" name="nama" autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="form-group col-sm-12">
              <label for="lbl_username" class="form-control-label">Merek:</label>
              <select class="form-control" name="merek" id="merek">
                <option value="">-- Pilih Merek -- </option>
                <?php
                  foreach ($merek as $value) { ?>
                    <option value="<?= $value->id?>"><?= $value->nama_merek ?></option>
                 <?php  }
                ?>
              </select>
              <span class="help-block"></span>
            </div>
            <div class="form-group col-sm-12">
              <label for="lbl_username" class="form-control-label">Warna:</label>
              <input type="email" class="form-control" id="warna" name="warna" autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="form-group col-sm-12">
              <label for="lbl_username" class="form-control-label">Nopol:</label>
              <input type="email" class="form-control" id="nopol" name="nopol" autocomplete="off">
              <span class="help-block"></span>
            </div>
          </div>
         
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn_outline" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn_1" id="btnSave" onclick="save()">Simpan</button>
      </div>
    </div>
  </div>
</div>
