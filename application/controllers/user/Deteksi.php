<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Deteksi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pertanyaan_model');
        $this->load->model('User_model');
        $this->load->model('Kriteria_model');
        $this->load->model('Hasil_model');
        $this->load->model('Admin_model');
        $this->load->model('Kriteria_model');
        $this->load->model('Certainty_model');
        $this->load->model('Bayes_model');

        $logged_in = $this->session->userdata('logged_in');
        $level = $this->session->userdata('level');

        if (!$logged_in || $level != "user") {
            redirect('auth');
        }
    }

    public function index()
    {
        $username = $this->session->userdata('username');
        $data['id_user'] = $this->session->userdata('id_user');

        $data = array(
            'title' => 'deteksi',
            'pertanyaan' =>  $this->Pertanyaan_model->getPertanyaan(),
            'username' => $username
        );

        $data['contents'] = $this->load->view('user/pages/deteksi', $data, TRUE);
        $this->load->view('user/layout/template', $data);
    }

    public function hasil()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $sesi = isset($_GET['sesi']) ? $_GET['sesi'] : 1;

        if ($id) {
            // Mengambil nilai parameter 'id' dan 'sesi' dari URL
            $username = $this->session->userdata('username');
            $id_user = $this->User_model->get_user_by_username($username)['id_user'];

            $sortedDataFromCF = $this->_quickSort($this->cf($id_user, 1));
            $sortedDataFromBayes = $this->_quickSort($this->bayes($id_user, 1));

            $hasil_cf = array();
            for ($i = 0; $i < 3; $i++) {
                $kriteria = $this->Kriteria_model->get_kriteria($sortedDataFromCF[$i]['kode_ciri']);
                $nama = $kriteria->nama_kriteria; // Ambil deskripsi dari objek $kriteria
                $deskripsi = $kriteria->deskripsi; // Ambil deskripsi dari objek $kriteria
                $kode = $sortedDataFromCF[$i]['kode_ciri'];
                $bobot = $sortedDataFromCF[$i]['nilai'];

                $hasil_cf[] = (object) array(
                    'kode' => $kode,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'bobot' => $bobot
                );
            }

            $hasil_nb = array();
            for ($i = 0; $i < 3; $i++) {
                $kriteria = $this->Kriteria_model->get_kriteria($sortedDataFromBayes[$i]['kode_ciri']);
                $nama = $kriteria->nama_kriteria; // Ambil deskripsi dari objek $kriteria
                $deskripsi = $kriteria->deskripsi; // Ambil deskripsi dari objek $kriteria
                $kode = $sortedDataFromBayes[$i]['kode_ciri'];
                $bobot = $sortedDataFromBayes[$i]['nilai'];

                $hasil_nb[] = (object) array(
                    'kode' => $kode,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'bobot' => $bobot
                );
            }

            $data = array(
                'title' => 'hasil',
                'usernmae' => $username,
                'hasil_cf' => $hasil_cf,
                'hasil_nb' => $hasil_nb
            );

            var_dump($data);

            $data['contents'] = $this->load->view('user/pages/deteksi-hasil', $data, TRUE);
            $this->load->view('user/layout/template', $data);
        } else {
            // redirect(base_url("user/deteksi"));
        }
    }

    public function submit_jawaban()
    {
        $nama = $this->input->post('nama');
        $usia = $this->input->post('usia');
        $username = $this->session->userdata('username');

        // Get the user ID based on the username (assuming 'users' table has 'id_user' and 'username' columns).
        $this->db->where('username', $username);
        $user = $this->db->get('user')->row();

        // check apakah user sudah pernah tes?
        $this->db->select_max('sesi'); // Pilih kolom 'section' saja
        $this->db->where('id_user', $user->id_user);
        $isSesion = $this->db->get('jawaban')->row();

        $sesi = 1;
        if ($isSesion == null) {
            // Jika user sudah pernah tes, ambil nilai sesi dari $isSesion dan tambahkan 1
            $sesi = 1;
        } else {
            $sesi = (int)$isSesion->sesi + 1;
            // Jika user belum pernah tes, set nilai sesi menjadi 1
        }

        // Save each answer to the 'jawaban' table.
        foreach ($_POST['jawaban'] as $id_pertanyaan => $jawaban) {
            if (empty($jawaban)) {
                $jawaban = 0;
            }

            $kriteria = $this->Pertanyaan_model->get_kriteria_id($id_pertanyaan);
            $gejala = $this->Pertanyaan_model->get_gejala_id($id_pertanyaan);

            $data = array(
                'id_user' => $user->id_user,
                'nama' => $nama,
                'usia' => $usia,
                'id_pertanyaan' => $id_pertanyaan,
                'id_kriteria' => $kriteria->id_kriteria,
                'id_gejala' => $gejala->id_gejala,
                'kode_gejala' => $gejala->kode_gejala,
                'cf_user' => $jawaban,
                'sesi' => $sesi,
                'tanggal' => date('Y-m-d'),
            );

            $this->Pertanyaan_model->save_jawaban($data);
        }

        $result_cf = $this->cf($user->id_user, $sesi);
        $result_nb = $this->bayes($user->id_user, $sesi);

        $sortedDataFromCF = $this->_quickSort($result_cf);
        $sortedDataFromBayes = $this->_quickSort($this->bayes($result_nb));

        $id_hasil = rand(1000000, 9999);

        $hasil_cf = array();
        for ($i = 0; $i < 3; $i++) {
            $kriteria = $this->Kriteria_model->get_kriteria($sortedDataFromCF[$i]['kode_ciri']);
            $title = $kriteria->nama_kriteria; // Ambil deskripsi dari objek $kriteria
            $deskripsi = $kriteria->deskripsi; // Ambil deskripsi dari objek $kriteria
            $kode = $sortedDataFromCF[$i]['kode_ciri'];
            $bobot = $sortedDataFromCF[$i]['nilai'];

            $hasil_cf[] = (object) array(
                'kode' => $kode,
                'title' => $title,
                'deskripsi' => $deskripsi,
                'bobot' => $bobot
            );
        }

        // menyimpen data cf
        $this->Pertanyaan_model->save_hasil(array(
            'id_hasil' => $id_hasil, // Menggunakan uniqid() untuk mendapatkan nilai acak
            'id_user' => $user->id_user,
            'nama' => $nama,
            'tanggal' => date('Y-m-d'),
            'usia' => $usia,
            'sesi' => $sesi,
            // 'bobot' => $result['nilai'],
            // 'hasil_kriteria' => $result['kode_kriteria'],
        ));

        foreach ($hasil_cf as $cf) {
            $this->Certainty_model->save_hasil(array(
                'id_hasil' => $id_hasil,
                'kode_kriteria' => $cf->kode,
                'kriteria' => $cf->title,
                'bobot' => $cf->bobot,
            ));
        }

        $hasil_nb = array();
        for ($i = 0; $i < 3; $i++) {
            $kriteria = $this->Kriteria_model->get_kriteria($sortedDataFromBayes[$i]['kode_ciri']);
            $title = $kriteria->nama_kriteria; // Ambil deskripsi dari objek $kriteria
            $deskripsi = $kriteria->deskripsi; // Ambil deskripsi dari objek $kriteria
            $kode = $sortedDataFromCF[$i]['kode_ciri'];
            $bobot = $sortedDataFromCF[$i]['nilai'];

            $hasil_nb[] = (object) array(
                'kode' => $kode,
                'title' => $title,
                'deskripsi' => $deskripsi,
                'bobot' => $bobot
            );
        }

        foreach ($hasil_nb as $nb) {
            $id_user = 4;
            $sesi = 5;

            $result_cf = $this->cf($user->id_user, $sesi);
            $result_nb = $this->bayes($user->id_user, $sesi);
            $this->Bayes_model->save_hasil(array(
                'id_hasil' => $id_hasil,
                'kode_kriteria' => $nb->kode,
                'kriteria' => $nb->title,
                'bobot' => $nb->bobot,
            ));
        }

        redirect(base_url("user/deteksi/hasil?id=" . $id_hasil . "&sesi=" . $sesi));
    }

    public function certainty_factor($user_id, $user_sesi)
    {
        // Array yang berisi nilai-nilai kriteria
        echo "<br/>";

        $kriteria = array('K1', 'K2', 'K3', 'K4', 'K5', 'K6', 'K7', 'K8');

        $cf_user = array();
        for ($i = 0; $i < count($kriteria); $i++) {
            array_push($cf_user, $this->Kriteria_model->get_cf_user($kriteria[$i], $user_id, $user_sesi));
        }

        // Array yang berisi nama-nama kriteria
        $maxCfCombine = array();
        // Menggunakan perulangan untuk mendapatkan nilai maksimum untuk setiap kriteria
        foreach ($kriteria as $index => $kriteriaNama) {
            $nilaiGejala = $cf_user[$index];

            $result = $this->Kriteria_model->nilai_gejala($kriteriaNama, $nilaiGejala);

            // $result = $this->Kriteria_model->nilai_gejala($kriteriaNama, $nilaiGejala);
            // $formatted_result = number_format($result, 15, '.', ''); // Format angka
            $formatted_result = $result;


            $decimal_position = strpos($formatted_result, '.') + 3;
            $trimmed_result = substr($formatted_result, 0, $decimal_position);

            $maxCfCombine[$index] = array(
                "kode_ciri" => $kriteria[$index],
                "nilai" => (float) $trimmed_result // Ubah menjadi float
            );
        }
    }

    public function coba()
    {
        $id = 4;
        $sesi = 5;
        $result_cf = $this->cf($id, $sesi);
        $result_nb = $this->bayes($id, $sesi);

        $sortedDataFromCF = $this->_quickSort($result_cf);
        $sortedDataFromNB = $this->_quickSort($result_nb);

        // var_dump($sortedDataFromBayes)

        for ($i = 0; $i < 3; $i++) {
            echo $sortedDataFromNB[$i]['nilai'] .  $sortedDataFromNB[$i]['nilai'] . '<br/>';
            echo $sortedDataFromCF[$i]['nilai'] .  $sortedDataFromNB[$i]['nilai'] . '<br/>';
        }
    }

    // public function bayes($user_id, $user_sesi)
    public function bayes()
    {
        $result = array();
        $kriteria = array('K1', 'K2', 'K3', 'K4', 'K5', 'K6', 'K7', 'K8');

        // $this->_bayes('K1', 3, 1);
        for ($index = 0; $index < count($kriteria); $index++) {
            $result[$index] = array(
                "kode_ciri" => $kriteria[$index],
                "nilai" => $this->_bayes($kriteria[$index], 4, 1),
            );
        }

        return $result;
    }

    public function cf($user_id, $user_sesi)
    {
        $kriteria = array('K1', 'K2', 'K3', 'K4', 'K5', 'K6', 'K7', 'K8');
        $cf_user = array();
        for ($i = 0; $i < count($kriteria); $i++) {
            array_push($cf_user, $this->Kriteria_model->get_cf_user($kriteria[$i], $user_id, $user_sesi));
        }

        $maxCfCombine = array();
        // Menggunakan perulangan untuk mendapatkan nilai maksimum untuk setiap kriteria
        foreach ($kriteria as $index => $kriteria_nama) {
            $nilai_gejala = $cf_user[$index];

            $result = $this->Kriteria_model->nilai_gejala($kriteria_nama, $nilai_gejala);
            $formatted_result = number_format($result, 15, '.', ''); // Format angka
            $decimal_position = strpos($formatted_result, '.') + 3;
            $trimmed_result = substr($formatted_result, 0, $decimal_position);

            $maxCfCombine[$index] = array(
                "kode_ciri" => $kriteria[$index],
                "nilai" => (float) $trimmed_result // Ubah menjadi float
            );
        }

        return $maxCfCombine;
    }

    public function _bayes($ciri, $user_id, $user_sesi)
    {
        $cf_pakar = [];
        $pakar_from_db = $this->Kriteria_model->get_cf_pakar($ciri, $user_id, $user_sesi);

        $cf_user = $this->Kriteria_model->get_cf_user($ciri, $user_id, $user_sesi);

        for ($i = 0; $i < count($pakar_from_db); $i++) {
            $cf_pakar[] = $pakar_from_db[$i]->cf_pakar;
        }

        $cf_pakar_arr = array(); // Inisialisasi array kosong
        for ($i = 0; $i < count($cf_pakar); $i++) {
            $cf_pakar_arr[] = $cf_pakar[$i];
        }

        // mencari nilai semesta
        $sum_of_gejala = array_sum($cf_pakar_arr);

        $result_of_division = array(); // Inisialisasi array kosong
        for ($i = 0; $i < count($cf_pakar_arr); $i++) {
            $result = $cf_pakar_arr[$i] / $sum_of_gejala;
            $formatted_result = number_format($result, 15, '.', ''); // Mengambil cukup banyak angka di belakang koma
            $decimal_position = strpos($formatted_result, '.') + 3; // Menentukan posisi dua digit di belakang koma
            $trimmed_result = substr($formatted_result, 0, $decimal_position); // Memotong angka

            // echo $cf_pakar_arr[$i] . "/" . $sum_of_gejala . " = " . $trimmed_result . "<br/>";
            $result_of_division[] = (float) $trimmed_result;
        }

        $sum_of_product_arr = [];
        for ($i = 0; $i < count($cf_pakar_arr); $i++) {
            $product = $result_of_division[$i] * $cf_pakar_arr[$i];
            $formatted_product = number_format($product, 15, '.', ''); // Mengambil cukup banyak angka di belakang koma
            $decimal_position = strpos($formatted_product, '.') + 3; // Menentukan posisi dua digit di belakang koma
            $trimmed_product = substr($formatted_product, 0, $decimal_position); // Memotong angka

            $sum_of_product_arr[] = (float) $trimmed_product;
        }

        $result_probabilitas = array();
        $sum_of_product_total = array_sum($sum_of_product_arr);

        for ($i = 0; $i < count($cf_pakar_arr); $i++) {
            $probability = $sum_of_product_arr[$i] / $sum_of_product_total;
            $formatted_probability = number_format($probability, 15, '.', ''); // Mengambil cukup banyak angka di belakang koma
            $decimal_position = strpos($formatted_probability, '.') + 4; // Menentukan posisi dua digit di belakang koma
            $trimmed_probability = substr($formatted_probability, 0, $decimal_position); // Memotong angka

            $result_probabilitas[] = (float) $trimmed_probability * (float)$cf_user[$i];
        }


        return array_sum($result_probabilitas) * 100;
    }

    public function _nb($ciri, $user_id, $user_sesi)
    {
        $cf_user = $this->Kriteria_model->get_cf_user($ciri, $user_id, $user_sesi);
        $cf_pakar = $this->Kriteria_model->get_cf_pakar($ciri, $user_id, $user_sesi);

        $sum_of_H = 0;
        for ($i = 0; $i < count($cf_pakar); $i++) {
            $sum_of_H += ((float)$cf_pakar[$i]->cf_pakar);
        }

        $result = 0;
        foreach ($cf_pakar as $i => $pakar) {
            $nilai_pakar = (float) $pakar->cf_pakar;
            $PHgi = $nilai_pakar / $sum_of_H;

            $PEHgi = $cf_user[$i] * $PHgi;

            $result += $PEHgi;
        }

        return round($result * 100, 2);
    }

    public function _quickSort($arr)
    {
        $length = count($arr);

        if ($length <= 1) {
            return $arr;
        }

        $pivot = $arr[0]['nilai'];
        $left = $right = array();

        for ($i = 1; $i < $length; $i++) {
            if ($arr[$i]['nilai'] > $pivot) {
                $left[] = $arr[$i];
            } else {
                $right[] = $arr[$i];
            }
        }

        return array_merge($this->_quickSort($left), array(array("kode_ciri" => $arr[0]['kode_ciri'], "nilai" => $pivot)), $this->_quickSort($right));
    }
}
