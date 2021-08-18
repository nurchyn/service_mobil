<?php 
  $obj_date = new DateTime();
  $timestamp = $obj_date->format('Y-m-d H:i:s');
  $tgl_now = $obj_date->format('d/m/Y');
?>
<div class="modal fade modal_detail" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="div-pengeluaran-lain-lain-modal">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pengeluaran Lain-Lain</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      
      <div class="modal-body">
        <form id="form_out_lain" name="form_out_lain">
          <div class="col-md-12">
            <div class="kt-portlet__body">
              <div class="form-group">         
                <div class="col-12 row">
                  <label class="col-6 col-form-label">Tanggal :</label>
                  <label class="col-6 col-form-label">Pilih Pengeluaran :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-6">
                    <input type="text" class="form-control kt_datepicker" id="tgl_out" name="tgl_out" value="<?=$tgl_now;?>" readonly="" placeholder="Pilih Tanggal">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-6">
                    <select class="form-control kt-select2" id="item_out" name="item_out" style="width: 100%;">
                      <option value="">Silahkan Pilih Pengeluaran</option>
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
                    <input type="text" class="form-control form-control-sm numberformat" id="qty_out" name="qty_out" value="" onkeyup="hitungTotalOut()">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-5">
                    <input type="text" data-thousands="." data-decimal="," id="harga_out" name="harga_out" class="form-control form-control-sm inputmask" onkeyup="hitungTotalOut()" value="0">
                    <input type="hidden" id="harga_out_raw" name="harga_out_raw" class="form-control form-control-sm" value="">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-5">
                    <input type="text" data-thousands="." data-decimal="," id="hargatot_out" name="hargatot_out" class="form-control form-control-sm inputmask" value="0">
                    <input type="hidden" id="hargatot_out_raw" name="hargatot_out_raw" class="form-control form-control-sm" value="">
                    <span class="help-block"></span>
                  </div>
                </div>
                <br>
                <div class="col-12">
                  <button type="button" id="btnSave" class="btn btn-primary" onclick="save('form_out_lain')">Tambahkan</button>
                </div>
                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
               
                <div class=" col-lg-12 col-sm-12">
                  <h4>Tabel Pengeluaran (10 Transaksi Terakhir)</h4>
                  <table class="table table-striped- table-bordered table-hover" id="tabel_modal_pengeluaran_lain">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Harga</th>
                        <th>Qty</th>
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

