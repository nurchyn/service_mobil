
<div class="modal fade modal_add_form" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="modal_mekanik_form">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_title3"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <form id="form-mekanik" name="form-mekanik">
          <div class="form-group">
            <input type="hidden" class="form-control" id="id" name="id">
            <label for="lbl_username" class="form-control-label">tgl_selesai:</label>
            <input type="text" class="form-control kt_datepicker" id="tgl_selesai" name="tgl_selesai" autocomplete="off" readonly>
            <span class="help-block"></span>
          </div>
         
          <div class="form-group">
            <label for="lbl_username" class="form-control-label">Nama Mekanik:</label>
            <select class="form-control required" name="id_mekanik" id="id_mekanik">
                <option value="">-- Pilih Mekanik -- </option>
                <?php
                  foreach ($mekanik as $value) { ?>
                    <option value="<?= $value->id?>"><?= $value->nama_mekanik ?></option>
                 <?php  }
                ?>
              </select>
              <span class="help-block"></span>
          </div>
         
         
         
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn_outline" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn_1" id="btnSave" onclick="save_mekanik()">Simpan</button>
      </div>
    </div>
  </div>
</div>
