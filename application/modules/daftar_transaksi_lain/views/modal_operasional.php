<?php 
  $obj_date = new DateTime();
  $timestamp = $obj_date->format('Y-m-d H:i:s');
  $tgl_now = $obj_date->format('d/m/Y');
?>
<div class="modal fade modal_detail" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="div-operasional-modal">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Operasional</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      
      <div class="modal-body">
        <form id="form_operasional" name="form_operasional">
          <input type="hidden" class="form-control form-control-sm" id="id_trans_op" name="id_trans_op" value="">
          <input type="hidden" class="form-control form-control-sm" id="id_jenis_op" name="id_jenis_op" value="">
          <div class="col-md-12">
            <div class="kt-portlet__body">
              <div class="form-group">       
                <div class="col-12 row">
                  <label class="col-12 col-form-label">Tanggal :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-12">
                    <input type="text" class="form-control kt_datepicker" id="tgl_op" name="tgl_op" value="<?=$tgl_now;?>" readonly="" placeholder="Pilih Tanggal">
                    <span class="help-block"></span>
                  </div>
                </div>

                <div class="col-12 row">
                  <label class="col-7 col-form-label">Item Operasional :</label>
                  <label class="col-5 col-form-label">Nilai Total :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-7">
                    <select class="form-control kt-select2" id="item_op" name="item_op" style="width: 100%;">
                      <option value="">Silahkan Pilih Transaksi</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-5">
                    <input type="text" data-thousands="." data-decimal="," id="harga_op" name="harga_op" class="form-control inputmask" onkeyup="hitungTotalOperasional()" value="0">
                    <input type="hidden" id="harga_op_raw" name="harga_op_raw" class="form-control" value="">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" class="btn btn-primary" onclick="save('form_operasional')">Tambahkan</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
