
<div class="modal fade modal_add_form" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="list_pekerjaan">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_title2"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <form id="form-pekerjaan" name="form-pekerjaan">
          <div class="form-group">
            <input type="hidden" class="form-control" id="id" name="id">
            <label for="lbl_username" class="form-control-label">Jenis Pekerjaan (WO) :</label>
            <select class="form-control select2" name="pekerjaan" id="pekerjaan">
                <option value="">-- Pilih Mekanik -- </option>
                <?php
                  foreach ($pekerjaan as $value) { ?>
                    <option value="<?= $value->id?>"><?= $value->nama_pekerjaan ?></option>
                 <?php  }
                ?>
              </select>
              <span class="help-block"></span>
          </div>
          <div class="form-group">
             <button type="button" class="btn btn_1" id="btnSave" onclick="save_pekerjaan()">Simpan</button>
        </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Type (WO)</th>
                    <th>Nama Pekerjaan</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody id="tbody2">
            </tbody>
        </table>
      </div>
     
    </div>
  </div>
</div>
