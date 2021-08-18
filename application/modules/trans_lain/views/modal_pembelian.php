<?php 
  $obj_date = new DateTime();
  $timestamp = $obj_date->format('Y-m-d H:i:s');
  $tgl_now = $obj_date->format('d/m/Y');
?>
<div class="modal fade modal_detail" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="div-pembelian-modal">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Pembelian</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      
      <div class="modal-body">
        <form id="form_pembelian" name="form_pembelian">
          <div class="col-md-12">
            <div class="kt-portlet__body">
              <div class="form-group">
                <div class="col-12 row">
                  <label class="col-6 col-form-label">Tanggal :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-12">
                    <input type="text" class="form-control kt_datepicker" id="tgl_beli" name="tgl_beli" value="<?=$tgl_now;?>" readonly="" placeholder="Pilih Tanggal">
                    <span class="help-block"></span>
                  </div>
                </div>

                <div class="col-12 row">
                  <label class="col-6 col-form-label">Pembelian :</label>
                  <label class="col-6 col-form-label">Supplier :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-6">
                    <select class="form-control kt-select2" id="item_beli" name="item_beli" style="width: 100%;">
                      <option value="">Silahkan Pilih Pembelian</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-6">
                    <select class="form-control kt-select2" id="sup_beli" name="sup_beli" style="width: 100%;">
                      <option value="">Silahkan Pilih Supplier</option>
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
                    <input type="text" class="form-control form-control-sm numberformat" id="qty_beli" name="qty_beli" value="" onkeyup="hitungTotalBeli()">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-5">
                    <input type="text" data-thousands="." data-decimal="," id="harga_beli" name="harga_beli" class="form-control form-control-sm inputmask" onkeyup="hitungTotalBeli()" value="0">
                    <input type="hidden" id="harga_beli_raw" name="harga_beli_raw" class="form-control form-control-sm" value="">
                    <span class="help-block"></span>
                  </div>
                  <div class="col-5">
                    <input type="text" data-thousands="." data-decimal="," id="hargatot_beli" name="hargatot_beli" class="form-control form-control-sm inputmask" value="0">
                    <input type="hidden" id="hargatot_beli_raw" name="hargatot_beli_raw" class="form-control form-control-sm" value="">
                    <span class="help-block"></span>
                  </div>
                </div>
                <br>
                <div class="col-12">
                  <button type="button" id="btnSave" class="btn btn-primary" onclick="save('form_pembelian')">Tambahkan</button>
                </div>
                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
               
                <div class=" col-lg-12 col-sm-12">
                  <h4>Tabel Pembelian (10 Transaksi Terakhir)</h4>
                  <table class="table table-striped- table-bordered table-hover" id="tabel_modal_pembelian">
                    <thead>
                      <tr>
                        <th>Nos</th>
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
