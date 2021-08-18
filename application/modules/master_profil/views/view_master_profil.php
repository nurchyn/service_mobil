<style>
.cover{
  background-color: #1ba79d;
  position: relative;
  display: block;
  min-height: 130px;
  border-radius: 5px 5px 0px 0px;
}
.photo-and-name{
  background-color: #fff;
  /* position: relative; */
  display: block;
  min-height: 130px;
  border-radius: 0px 0px 5px 5px;
}
.photo-and-name .content{
  position: relative;
  margin-top: -60px;
}
.photo-and-name .photo-profile{
  position: relative;
  background-size: cover;
  background-position: center;
  width: 120px;
  border: 1px solid #1ba79d;
  border-radius: 50%;
  left: 50%;
  transform: translateX(-50%);
  -webkit-transform: translateX(-50%);
  -moz-transform: translateX(-50%);
  -ms-transform: translateX(-50%);
  -o-transform: translateX(-50%);
  background-color: #fff;
}
.photo-and-name .photo-profile::after{
  content: "";
  position: relative;
  display: block;
  padding-bottom: 100%;
}
.photo-and-name .photo-profile{
  width: 120px;
  height: 120px;
  background-size: contain;
  background-repeat: no-repeat;
  background-color: #fff;
}
.photo-and-name .photo-profile span{
  position: relative;
  font-size: 70px;
  width: 110px;
  height: 110px;
  display: block;
  text-align: center;
  line-height: 110px;
  color: #fff;
}
.photo-and-name .profile-username {
  position: relative;
  text-align: center;
  display: block;
  margin-top: 8px;
  margin-bottom: 35px;
}
.photo-and-name .profile-username h1 {
  position: relative;
  color: #777;
  font-size: 18px;
  font-weight: 700;

}



#manage_product {
  margin-top: -20px;
}

.upload-dropZone {
    position: relative;
    width: 100%;
    display: block;
    border: 2px dashed #ddd;
    /* height: 160px; */
    cursor: pointer;
}
</style>
<!-- begin:: Content -->
<div class="col-lg-12" id="manage_product">
        <div class="row">
                    <div class="col-lg-5">
            <div class="card card-user">
              <div class="card-body">
                <div class="cover">
                  <!-- background -->
                </div>
                <div class="photo-and-name">
                  <div class="content">
                    <?php
                      $value = (isset($profil->gambar))?$profil->gambar:"";
                      if ($value != '') { ?>
                         <div class="photo-profile"  style="background-image:
                      url('<?php echo base_url('assets/images/'.$profil->gambar);?>')">
                            <span>
                            </span>
                          </div>
                      <?php } else{ ?>
                        <div class="photo-profile" style="border: 5px solid #fff;background: #1ba79d;">
                        <span>
                        </span>
                        </div>
                     <?php }
                    ?>
                   
                                        
                    <div class="profile-username">
                                            <h1><?php $value = (isset($profil->nama))?$profil->nama:""; echo $value;?></h1>
                    </div>
                    <div class="product-transaksi">
                      <!-- <div class="row">
                        <div class="col-sm-6">
                          <p>Product Saya</p>
                          <span>0</span>
                        </div>
                        <div class="col-sm-6">
                          <p>Transaksi</p>
                          <span>2</span>
                        </div>
                      </div> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <a class="card button-card" id="edit-photo" onclick="edit_photo()">
              <span class="card-body">Edit Foto Profile</span>
            </a>
            <a href="#password-modal" class="card button-card" id="edit-password">
              <span class="card-body">Edit Password</span>
            </a>
          </div>
          <div class="col-lg-7" id="show-profile" style="display: block;">
            <form action="#" enctype="multipart/form-data" method="POST" id="profile-show">
              <div class="card card-edit-profile">
                <div class="card-header">
                  <i class="icon-doc-text-inv"></i> Profile
                  <div class="pull-right">
                    <i class="fa fa-edit" onclick="edit()"></i>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Nama</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->nama))?$profil->nama:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Deskripsi</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->deskripsi))?$profil->deskripsi:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Alamat</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->alamat))?$profil->alamat:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kelurahan</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->kelurahan))?$profil->kelurahan:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kecamatan</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->kecamatan))?$profil->kecamatan:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kota</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->kota))?$profil->kota:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kode Pos</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->kode_pos))?$profil->kode_pos:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Provinsi</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->provinsi))?$profil->provinsi:""; echo $value;?>" readonly="">
                      </div>
                    </div>
               
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Email</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->email))?$profil->email:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Telp</label>
                        <input name="" type="text" class="form-control-plaintext" value="<?php $value = (isset($profil->telp))?$profil->telp:""; echo $value;?>" readonly="">
                      </div>
                    </div>
                   
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="col-lg-7" style="display: none;" id="edit-profile">
            <form action="" method="POST" id="form-profile">
              <div class="card card-edit-profile">
                <div class="card-header">
                  <i class="icon-doc-text-inv"></i> Edit Profile
                  <div class="pull-right">
                    <i class="fa fa-times" onclick="closeedit()"></i>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Nama</label>
                        <input name="nama" type="text" class="form-control" placeholder="Nama" value="<?php $value = (isset($profil->nama))?$profil->nama:""; echo $value;?>" >
                        <input type="hidden" name="id" value="1">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Deskripsi</label>
                        <input name="deskripsi" type="text" class="form-control" placeholder="Deskripsi" value="<?php $value = (isset($profil->deskripsi))?$profil->deskripsi:""; echo $value;?>">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat" ><?php $value = (isset($profil->alamat))?$profil->alamat:""; echo $value;?></textarea>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kelurahan</label>
                        <input name="kelurahan" type="text" class="form-control" placeholder="Kelurahan" value="<?php $value = (isset($profil->kelurahan))?$profil->kelurahan:""; echo $value;?>" >
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kecamatan</label>
                        <input name="kecamatan" type="text" class="form-control" placeholder="Kecamatan" value="<?php $value = (isset($profil->kecamatan))?$profil->kecamatan:""; echo $value;?>" >
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kota</label>
                        <input name="kota" type="text" class="form-control" placeholder="Kota" value="<?php $value = (isset($profil->kota))?$profil->kota:""; echo $value;?>" >
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Kode Pos</label>
                        <input name="kode_pos" type="text" class="form-control" placeholder="60186" value="<?php $value = (isset($profil->kode_pos))?$profil->kode_pos:""; echo $value;?>" >
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="">Provinsi</label>
                        <input name="provinsi" type="text" class="form-control" placeholder="Provinsi" value="<?php $value = (isset($profil->provinsi))?$profil->provinsi:""; echo $value;?>" >
                      </div>
                    </div>
                   
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="Email" value="<?php $value = (isset($profil->email))?$profil->email:""; echo $value;?>" >
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="">Telp</label>
                        <input name="telp" type="text" class="form-control" placeholder="+62 31-873264" value="<?php $value = (isset($profil->telp))?$profil->telp:""; echo $value;?>" >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <br>
              <div class="button-submit">
                <button class="btn_1" type="button" onclick="save()">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>



