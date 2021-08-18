<?php 
  $obj_date = new DateTime();
  $timestamp = $obj_date->format('Y-m-d H:i:s');
  $tgl_now = $obj_date->format('d/m/Y');
?>
<div class="modal fade modal_detail" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="div-operasional-modal">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="div_diagnosa_modal_title">Operasional</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      
      <div class="modal-body">
        <form id="form_operasional" name="form_operasional">
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
                <br>
                <div class="col-12">
                  <button type="button" id="btnSave" class="btn btn-primary" onclick="save('form_operasional')">Tambahkan</button>
                </div>
                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
               
                <div class=" col-lg-12 col-sm-12">
                  <h4>Tabel Operasional (10 Transaksi Terakhir)</h4>
                  <table class="table table-striped- table-bordered table-hover" id="tabel_modal_operasional">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Total</th>
                        <th style="width: 10%;">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
              
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-primary btn_direct_data" style="color:white;" target="_blank" href="">Klik Untuk Ke Daftar Transaksi</a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
