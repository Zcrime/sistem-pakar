<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ciri extends CI_Controller
{
    public function index()
    {
        $tmp = array(
            'title' => 'ciri',
        );

        $tmp['contents'] = $this->load->view('admin/pages/ciri', $tmp, TRUE);
        $this->load->view('admin/layout/template', $tmp);
    }

    ///data untuk kriteria
    public function tambahKriteria()
    {
        $tmp = array(
            'title' => 'tambah data kriteria',
        );

        $tmp['contents'] = $this->load->view('admin/pages/ciri-tambah', $tmp, TRUE);
        $this->load->view('admin/layout/template', $tmp);
    }

    public function editKriteria()
    {
        $tmp = array(
            'title' => 'update data kriteria',
        );

        $tmp['contents'] = $this->load->view('admin/pages/ciri-update', $tmp, TRUE);
        $this->load->view('admin/layout/template', $tmp);
    }

    public function hapusKriteria()
    {
        $tmp = array(
            'title' => 'hapus data kriteria',
        );

        $tmp['contents'] = $this->load->view('admin/pages/ciri-update', $tmp, TRUE);
        $this->load->view('admin/layout/template', $tmp);
    }

    ///data untuk gejala
    public function tambahGejala()
    {
        $tmp = array(
            'title' => 'tambah data gejala',
        );

        $tmp['contents'] = $this->load->view('admin/pages/ciri-tambah', $tmp, TRUE);
        $this->load->view('admin/layout/template', $tmp);
    }

    public function editGejala()
    {
        $tmp = array(
            'title' => 'update data gejala',
        );

        $tmp['contents'] = $this->load->view('admin/pages/ciri-update', $tmp, TRUE);
        $this->load->view('admin/layout/template', $tmp);
    }

    public function hapusGejala()
    {
        $tmp = array(
            'title' => 'hapus data gejala',
        );

        $tmp['contents'] = $this->load->view('admin/pages/ciri-update', $tmp, TRUE);
        $this->load->view('admin/layout/template', $tmp);
    }
}