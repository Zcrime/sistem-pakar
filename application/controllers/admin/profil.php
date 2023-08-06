<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profil extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');

        $logged_in = $this->session->userdata('logged_in');
        $level = $this->session->userdata('level');

        if (!$logged_in || $level != "admin") {
            redirect('auth');
        }
    }

    public function index()
    {
        $username = $this->session->userdata('username');

        $data = array(
            'title' => 'profil',
            'user' =>  $this->User_model->get_user_by_username($username),
            'username' => $username
        );

        $data['contents'] = $this->load->view('admin/pages/profil', $data, TRUE);
        $this->load->view('admin/layout/template', $data);
    }

    public function editProfil($id_user)
    {
        $data['title'] = 'update data Profil';
        $data['user'] = $this->User_model->get_user_by_id($id_user);

        // Jika ada data yang dikirimkan melalui form
        if ($this->input->post()) {
            $this->form_validation->set_rules('nama', 'Nama', 'required');
            $this->form_validation->set_rules('alamat', 'Alamat', 'required');
            $this->form_validation->set_rules('jk', 'Jenis Kelamin', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('tlp', 'Telphone', 'required');

            if ($this->form_validation->run() == TRUE) {
                // Jika validasi sukses, lakukan proses update data ke model
                $id_user = $this->input->post('id_user');
                $data = array(
                    'nama' => $this->input->post('nama'),
                    'alamat' => $this->input->post('alamat'),
                    'jenis_kelamin' => $this->input->post('jk'),
                    'email' => $this->input->post('email'),
                    'tlp' => $this->input->post('tlp'),
                );

                $this->User_model->updateUser($id_user, $data);

                // Redirect ke halaman ciri ketika halaman sukses update data
                redirect('admin/profil');
            }
        }
    }
}
