
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
              <label for="lbl_username" class="form-control-label">Nama Customer:</label>
              <input type="text" class="form-control" id="nama" name="nama" autocomplete="off">
              <span class="help-block"></span>
            </div>
            <div class="form-group col-sm-12">
              <label for="lbl_username" class="form-control-label">Alamat:</label>
              <textarea type="email" class="form-control" id="alamat" name="alamat" autocomplete="off"></textarea>
              <span class="help-block"></span>
            </div>
            <div class="form-group col-sm-12">
              <label for="lbl_username" class="form-control-label">Telp:</label>
              <input type="email" class="form-control" id="telp" name="telp" autocomplete="off">
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
