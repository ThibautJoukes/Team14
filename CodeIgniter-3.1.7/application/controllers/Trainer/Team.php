<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Team extends CI_Controller {

    // +----------------------------------------------------------
    // |    Trainingscentrum Wezenberg
    // +----------------------------------------------------------
    // |    Auteur: Klaus Daems     |       Helper: /
    // +----------------------------------------------------------
    // |
    // |    Team controller
    // |
    // +----------------------------------------------------------
    // |    Team 14
    // +----------------------------------------------------------

    public function __construct() {      
        parent::__construct();
        
        // controleren of bevoegde persoon is aangemeld        
        if (!$this->authex->isAangemeld()) {
            redirect('welcome/meldAan');
        } else {
            $persoon = $this->authex->getPersoonInfo();
            if ($persoon->soort != "Trainer") {
                redirect('welcome/meldAan');
            }
        }

        $this->load->helper('url');
        $this->load->helper('form');
    }

    // +----------------------------------------------------------
    // |
    // |    Zwemmers beheren
    // |
    // +----------------------------------------------------------

    public function index() {
        $data['titel'] = 'Team beheren';
     
        $zwemmers = $this->ladenZwemmers();
        
        $data['zwemmers'] = $zwemmers;

        $partials = array('hoofding' => 'main_header',
            'menu' => 'trainer_main_menu',
            'inhoud' => 'trainer/team_lijst',
            'voetnoot' => 'main_footer');

        $this->template->load('main_master', $partials, $data);
    }
    
    public function ladenZwemmers(){
        $this->load->model("trainer/zwemmers_model");
        $zwemmers = $this->zwemmers_model->getZwemmers();
        
        $data_zwemmers = array();
        
        foreach ($zwemmers as $zwemmer) {                    
            $data_zwemmers[] = array(
                "voornaam" => $zwemmer->Voornaam,
                "achternaam" => $zwemmer->Achternaam,
                "straat" => $zwemmer->Straat,
                "huisnummer" => $zwemmer->Huisnummer,
                "postcode" => $zwemmer->Postcode,
                "gemeente" => $zwemmer->Gemeente,
                "telefoonnummer" => $zwemmer->Telefoonnummer,
                "email" => $zwemmer->Email,
                "wachtwoord" => $zwemmer->Wachtwoord,
                "omschrijving" => $zwemmer->Omschrijving,
                "foto" => $zwemmer->Foto,
                "color" => '#FF7534',
                "textColor" => '#000'
            );
        }
        return $zwemmers;
    }
    
    public function aanpassen() {
        $data['titel'] = 'Team beheren';
        $zwemmers = $this->ladenZwemmers();
        $data['zwemmers'] = $zwemmers;
        
        $partials = array('hoofding' => 'main_header',
            'menu' => 'trainer_main_menu',
            'inhoud' => 'trainer/team_aanpassen',
            'voetnoot' => 'main_footer');
        
        $this->template->load('main_master', $partials, $data);
    }
    
    public function wijzig() {
        $data['titel'] = 'Team wijzigen';
        $zwemmers = $this->ladenZwemmers();
        $data['zwemmers'] = $zwemmers;
        
        $partials = array('hoofding' => 'main_header',
            'menu' => 'trainer_main_menu',
            'inhoud' => 'trainer/team_aanpassen',
            'voetnoot' => 'main_footer');
        
        $this->template->load('main_master', $partials, $data);
    }
    
    public function archiveer($id) {
        $this->load->model('trainer/zwemmers_model');
        $data['zwemmers'] = $this->zwemmers_model->delete($id);
        
        redirect('/trainer/team_lijst');
    }
    
    public function registreer()
    {       
        $persoon = new stdClass();
        
        $persoon->id=$this->input->post('id');
        $persoon->voornaam=$this->input->post('voornaam');
        $persoon->achternaam=$this->input->post('achternaam');
        $persoon->email=$this->input->post('email');
        $persoon->wachtwoord=$this->input->post('wachtwoord');
        $persoon->omschrijving=$this->input->post('omschrijving');
        
        $this->load->model('zwemmers_model');
        if ($persoon->id == 0) {
            $this->zwemmers_model->insert($persoon);
        } else {
            $this->zwemmers_model->update($persoon);
        }
        redirect('trainer/team');
        
    }
}
