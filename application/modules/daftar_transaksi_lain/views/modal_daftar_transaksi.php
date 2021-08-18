
<div class="modal fade modal_add_form" tabindex="-1" role="dialog" aria-labelledby="add_menu" aria-hidden="true" id="modal_pegawai_form">
  <div class="modal-dialog modal-xs" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <form id="form-pegawai" name="form-pegawai">
          <div class="form-group">
            <input type="hidden" class="form-control" id="id" name="id">
            <label for="lbl_nama_pegawai" class="form-control-label">Jenis Transaksi:</label>
            <select class="form-control required" name="id_jenis_trans" id="id_jenis_trans">
              <option value=""> Pilih Jenis Transaksi </option>
              <?php
              foreach ($jenis_trans as $val) { ?>
                  <option value="<?php echo $val->id; ?>">
                      <?php echo $val->kode_jenis.' | '.$val->nama_jenis; ?>    
                  </option>
              <?php } ?>
            </select>
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="lbl_telp1" class="form-control-label">Nama Item Transaksi:</label>
            <input type="text" class="form-control" id="nama" name="nama">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="lbl_telp1" class="form-control-label">Keterangan:</label>
            <input type="text" class="form-control" id="keterangan" name="keterangan">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="lbl_telp1" class="form-control-label">Harga Awal:</label>
            <input type="number" class="form-control" id="harga_awal" name="harga_awal">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="lbl_telp1" class="form-control-label">Harga:</label>
            <input type="number" class="form-control" id="harga" name="harga">
            <span class="help-block"></span>
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

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalDetail" aria-hidden="true" id="modal_detail_transaksi">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Transaksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <div class="kt-portlet">
          <div class="kt-form kt-form--label-right">
            <div class="kt-portlet__body">
              <div class="form-group form-group-xs row">
                <label class="col-3 col-form-label">Tanggal :</label>
                <div class="col-9">
                  <span class="form-control-plaintext kt-font-bolder" id="spn-tanggal"></span>
                </div>
              </div>
              <div class="form-group form-group-xs row">
                <label class="col-3 col-form-label">Nama User :</label>
                <div class="col-9">
                  <span class="form-control-plaintext kt-font-bolder" id="spn-user"></span>
                </div>
              </div>
              <div class="form-group form-group-xs row" id="div_supplier_modal">
               
              </div>
              <div class="form-group form-group-xs row">
                <label class="col-3 col-form-label">Nilai Total :</label>
                <div class="col-9">
                  <span class="form-control-plaintext kt-font-bolder" id="spn-total"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">
                Rincian Transaksi
              </h3>
            </div>
          </div>
          <div class="kt-portlet__body" id="div_tabel_detail"></div>
        </div>
      </div>
      <div class="modal-footer" id="div_button_detail"></div>
    </div>
  </div>
</div>