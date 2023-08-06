<div class="card card-outline card-primary m-4">
  <div class="container">
    <div class="container rounded bg-white mb-2">
      <div class="row">
        <div class="col-md-4 border-right">
          <div class="d-flex flex-column align-items-center text-center p-3 py-5">
            <img class=" mt-5" width="200px" height="200" src="<?= base_url() ?>assets/template/landing_page/assets/img/user.png"">
            <span class="font-weight-bold mt-3 stext-110 cl2"><?= $user['username']; ?></span>
            <span class="text-black-50 stext-115 cl6"><?= $user['email']; ?></span>
            <span class="text-black-50 stext-115 cl6"><?= $user['tlp']; ?></span>
            <span class="text-black-50 stext-115 cl6">Level <?= $user['level']; ?></span>
          </div>
        </div>

        <div class="col-md-4">
          <div class="p-3 py-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mtext-112 cl2">Edit Profil</h4>
            </div>
            <div class="row">
              <div class="col-lg-12">
                  <?= $this->session->flashdata('message'); ?>
              </div>
            </div>

            <?php echo form_open('user/pengguna/editPengguna/' . $user['id_user']); ?>

            <div class="row mt-3">
            <?php echo form_hidden('id_user', $user['id_user']) ?>
              <div class="col-md-12 pb-2">
                <label for="nama" class=" stext-110 cl2">Nama</label>
                <input type="text" class="form-control stext-115 cl6" id="nama" name="nama" value="<?= $user['nama']; ?>">
              </div>

              <div class="col-md-12 pb-2">
                <label for ="alamat" class="stext-110 cl2">Alamat</label>
                <input type="text" class="form-control stext-115 cl6" id="alamat" name="alamat" value="<?= $user['alamat']; ?>" >
              </div>
                    
              <div class="col-md-12 pb-2">
                <label for ="jk" class="stext-110 cl2">Jenis Kelamin</label>
                <select class="custom-select" name="jk">
                    <option><?php echo $user['jenis_kelamin'] ?></option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="perempuan">Perempuan</option>
                </select>
              </div>

              <div class="col-md-12 pb-2">
                <label for ="email" class="stext-110 cl2">E-mail</label>
                <input type="text" class="form-control stext-115 cl6" id="email" name="email" value="<?= $user['email']; ?>">
              </div>

              <div class="col-md-12 pb-2">
                <label for ="tlp" class="stext-110 cl2">Telphone</label>
                <input type="tlp" class="form-control stext-115 cl6" id="tlp" name="tlp" value="<?= $user['tlp']; ?>">
              </div>
            </div>

            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-block">
                        Perbarui
                    </button>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>

    <div class="col-md-4 border-left">
      <div class="p-3 py-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="text-right mtext-112 cl2 ">Ubah Password</h4>
        </div>

        <div class="row">
          <div class="col-lg-12">
              <?= $this->session->flashdata('pesan'); ?>
          </div>
        </div>

        <form action="<?= base_url('wisatawan/password'); ?>" method="post">

          <div class="row mt-3">
            <div class="col-md-12 pb-2">
              <label for="current_password" class="stext-110 cl2">Password saat ini</label>
              <input type="password" class="form-control" id="current_password" name="current_password">
              <?= form_error('current_password',' <small class="text-danger">', '</small>'); ?>
            </div>

            <div class="col-md-12 pb-2">
              <label for ="new_password1" class="stext-110 cl2">Password baru</label>
              <input type="password" class="form-control" id="new_password1" name="new_password1">
              <?= form_error('new_password1',' <small class="text-danger">', '</small>'); ?>
            </div>

            <div class="col-md-12 pb-3">
              <label for ="new_password2" class="stext-110 cl2">Konfirmasi password baru</label>
              <input type="password" class="form-control" id="new_password2" name="new_password2">
              <?= form_error('new_password2',' <small class="text-danger">', '</small>'); ?>
            </div>
          </div>

          <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-primary btn-block">
                        Ubah Password
                    </button>
                </div>
          </div>
      </form>
    </div>
  </div>
</div>
</div>
</div>
</div>

