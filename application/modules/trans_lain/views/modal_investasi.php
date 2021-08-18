<?php 
  $obj_date = new DateTime();
  $timestamp = $obj_date->format('Y-m-d H:i:s');
  $tgl_now = $obj_date->format('d/m/Y');
?>
<div class="modal fade modal_detail" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="div-investasi-modal">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="div_diagnosa_modal_title">Investasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      
      <div class="modal-body">
        <form id="form_investasi" name="form_investasi">
          <div class="col-md-12">
            <div class="kt-portlet__body">
              <div class="form-group">       
                <div class="col-12 row">
                  <label class="col-12 col-form-label">Tanggal :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-12">
                    <input type="text" class="form-control kt_datepicker" id="tgl_inves" name="tgl_inves" value="<?=$tgl_now;?>" readonly="" placeholder="Pilih Tanggal">
                    <span class="help-block"></span>
                  </div>
                </div>

                <div class="col-12 row">
                  <label class="col-6 col-form-label">Item Transaksi :</label>
                  <label class="col-6 col-form-label">Nilai Total Investasi :</label>
                </div>

                <div class="col-12 row">
                  <div class="col-6">
                    <select class="form-control kt-select2" id="item_inves" name="item_inves" style="width: 100%;">
                      <option value="">Silahkan Pilih Transaksi</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-6">
                    <input type="text" data-thousands="." data-decimal="," id="harga_inves" name="harga_inves" class="form-control inputmask" onkeyup="hitungTotalInvestasi()" value="0">
                    <input type="hidden" id="harga_inves_raw" name="harga_inves_raw" class="form-control" value="">
                    <span class="help-block"></span>
                  </div>
                  
                </div>
                <br>
                <div class="col-12">
                  <button type="button" id="btnSave" class="btn btn-primary" onclick="save('form_investasi')">Tambahkan</button>
                </div>
                <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg kt-separator--portlet-fit"></div>
               
                <div class=" col-lg-12 col-sm-12">
                  <h4>Tabel Investasi (10 Transaksi Terakhir)</h4>
                  <table class="table table-striped- table-bordered table-hover" id="tabel_modal_investasi">
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
