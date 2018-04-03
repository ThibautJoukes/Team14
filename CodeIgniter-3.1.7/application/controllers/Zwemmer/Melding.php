<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Melding extends CI_Controller {

    // +----------------------------------------------------------
    // |    Trainingscentrum Wezenberg
    // +----------------------------------------------------------
    // |    Auteur: Lise Van Eyck       |       Helper:
    // +----------------------------------------------------------
    // |
    // |    Melding controller
    // |
    // +----------------------------------------------------------
    // |    Team 14
    // +----------------------------------------------------------

    public function __construct() {

        parent::__construct();

        // Helpers inladen
        $this->load->helper('url');
        
        // Auteur inladen in footer
        $this->data = new stdClass();
        $this->data->team = array("Klied Daems" => "false", "Thibaut Joukes" => "false", "Jolien Lauwers" => "false", "Tom Nuyts" => "false", "Lise Van Eyck" => "true");
    }

    public function index() {
        $data['titel'] = 'Meldingen';
        $data['team'] = $this->data->team;
        
        $persoonId = 1;

        $this->load->model('zwemmer/melding_model');
        $data['meldingen'] = $this->melding_model->getMeldingByPersoon($persoonId);

        $partials = array('hoofding' => 'main_header',
            'menu' => 'main_menu',
            'inhoud' => 'zwemmer/melding',
            'voetnoot' => 'main_footer');

        $this->template->load('main_master', $partials, $data);
    }
}