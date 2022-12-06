<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Intro extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->model("default_model");

        $admins = $this->session->userdata("admins");
        if (!$admins) {
            redirect(base_url("login"));
        }

    }

    public function index()
    {
        $viewData = new stdClass();

        $admin = $this->default_model->get("admin", array("ID" => 1));
        $viewData->admin = $admin;

        $settings = $this->default_model->get("settings", array("ID" => 1));
        $viewData->settings = $settings;

        $intro = $this->default_model->get("intro", array(
            "ID" => 1
        ));
        $viewData->intro = $intro;

        $about_us = $this->default_model->get("about_us", array(
            "ID" => 1
        ));

        $viewData->about_us = $about_us;
        $viewData->url = "intro";
        $viewData->title = $settings->Title;
        $this->load->view('intro', $viewData);
    }

    public function update($ID)
    {

        $Name = $this->input->post("Name");
        $Content = $this->input->post("Content");
        $Btn = $this->input->post("Btn");
        $Btn_Link = $this->input->post("Btn_Link");

        if (!$Name || !$Content || !$Btn || !$Btn_Link) {
            $alert = array(
                'title' => "Dikkat Et!",
                'subtitle' => "Lütfen boş alan bırakmayınız!",
                'type' => "warning"
            );

            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("intro"));
        } else {

            if (!empty($_FILES["Image"]["name"])) {

                $image_data = $this->default_model->get("intro",
                    array(
                        "ID" => $ID
                    ));
                unlink("uploads/intro/" . $image_data->Image);

                // Dosyanın yüklenecek olan yolunu belirttik
                $config["upload_path"] = "uploads/intro/";

                // Yüklenmesine izin verdiğimiz uzantılar
                $config["allowed_types"] = "gif|jpg|png|jpeg|svg";

                // Dosyanın ismini değiştirme
                $config["file_name"] = uniqid();

                // Upload kütüphanesi yükledik
                $this->load->library("upload", $config);

                // Resim yükleme işlemini burada gerçekleştirdik
                $upload = $this->upload->do_upload("Image");

                if ($upload) {

                    // Dosyanın sadece uzantısını almak için kullanılır.
                    //$uploaded_filename = $this->upload->data("file_type");

                    // Yüklediğimiz dosyanın ismini ve uzantısını alıyoruz. (Image Manipulation Class)
                    $uploaded_filename = $this->upload->data("file_name");

                    // Yeni kütüphanenin ayarlarını yapıyoruz.
                    $config["image_library"] = "gd2";

                    // Ayarları ile oynayacağı fotoğrafın yolunu gösterdik.
                    $config["source_image"] = "uploads/intro/" . $uploaded_filename;

                    // Thumbnail oluştursun mu
                    $config["create_thumb"] = FALSE;

                    // Oranları korusun mu
                    $config["maintain_ratio"] = FALSE;

                    // Resmin kalitesi
                    $config["quality"] = "100%";

                    $config["width"] = 1920;
                    $config["height"] = 1190;

                    $this->load->library("image_lib", $config);

                    $this->image_lib->resize();

                    $update = $this->default_model->update("intro",
                        array(
                            "ID" => $ID
                        ),
                        array(
                            "Name" => $Name,
                            "Content" => $Content,
                            "Btn" => $Btn,
                            "Btn_Link" => $Btn_Link,
                            "Image" => $uploaded_filename
                        ));

                    if ($update) {
                        $alert = array(
                            'title' => "Tebrikler!",
                            'subtitle' => "Güncelleme işlemi başarıyla gerçekleşti!",
                            'type' => "success"
                        );
                    } else {
                        $alert = array(
                            'title' => "Hata!",
                            'subtitle' => "Güncelleme işlemi başarısız oldu!",
                            'type' => "error"
                        );
                    }

                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("intro"));

                } else {
                    $alert = array(
                        'title' => "Hata!",
                        'subtitle' => "Fotoğraf yüklenirken bir hata oluştu!",
                        'type' => "error"
                    );

                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("intro"));
                }


            } else {
                // Fotoğraf güncellenmezse
                $update = $this->default_model->update("intro",
                    array(
                        "ID" => $ID
                    ),
                    array(
                        "Name" => $Name,
                        "Content" => $Content,
                        "Btn" => $Btn,
                        "Btn_Link" => $Btn_Link
                    ));

                if ($update) {
                    $alert = array(
                        'title' => "Tebrikler!",
                        'subtitle' => "Güncelleme işlemi başarıyla gerçekleşti!",
                        'type' => "success"
                    );
                } else {
                    $alert = array(
                        'title' => "Hata!",
                        'subtitle' => "Güncelleme işlemi başarısız oldu!",
                        'type' => "error"
                    );
                }

                $this->session->set_flashdata("alert", $alert);
                redirect(base_url("intro"));

            }
        }

    }

}
