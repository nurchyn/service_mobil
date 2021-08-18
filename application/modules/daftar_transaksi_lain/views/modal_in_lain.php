<?php 
  $obj_date = new DateTime();
  $timestamp = $obj_date->format('Y-m-d H:i:s');
  $tgl_now = $obj_date->format('d/m/Y');
?>
<div class="modal fade modal_detail" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="div-penerimaan-lain-lain-modal">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Penerimaan Lain-Lain</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      
      <div class="modal-body">
        <form id="form_in_lain" name="form_in_lain">
          <input type="hidden" class="form-control form-control-sm" id="id_trans_in" name="id_trans_in" value="">
          <input type="hidden" class="form-control form-control-sm" id="id_jenis_in" name="id_jenis_in" value="">
          <div class="col-md-12">
            <div class="kt-portlet__body">
              <div class="form-group">         
                <div class="col-12 row">
                  <label class="col-6 col-form-label">Tanggal :</label>
                  <label class="col-6 col-form-label">Pilih Penerimaan :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-6">
                    <input type="text" class="form-control kt_datepicker" id="tgl_in" name="tgl_in" value="<?=$tgl_now;?>" readonly="" placeholder="Pilih Tanggal">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-6">
                    <select class="form-control kt-select2" id="item_in" name="item_in" style="width: 100%;">
                      <option value="">Silahkan Pilih Penerimaan</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>

                <div class="col-12 row">
                  <label class="col-2 col-form-label">Qty :</label>
                  <label class="col-5 col-form-label">Harga Satuan :</label>
                  <label class="col-5 col-form-label">Harga Total :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-2">
                    <input type="text" class="form-control form-control-sm numberformat" id="qty_in" name="qty_in" value="" onkeyup="hitungTotalIn()">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-5">
                    <input type="text" data-thousands="." data-decimal="," id="harga_in" name="harga_in" class="form-control form-control-sm inputmask" onkeyup="hitungTotalIn()" value="0">
                    <input type="hidden" id="harga_in_raw" name="harga_in_raw" class="form-control form-control-sm" value="">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-5">
                    <input type="text" data-thousands="." data-decimal="," id="hargatot_in" name="hargatot_in" class="form-control form-control-sm inputmask" value="0">
                    <input type="hidden" id="hargatot_in_raw" name="hargatot_in_raw" class="form-control form-control-sm" value="">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" class="btn btn-primary" onclick="save('form_in_lain')">Tambahkan</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

