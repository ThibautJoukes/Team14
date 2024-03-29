<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Agenda extends CI_Controller {

    // +----------------------------------------------------------
    // |    Trainingscentrum Wezenberg
    // +----------------------------------------------------------
    // |    Auteur: Tom Nuyts       |       Helper:
    // +----------------------------------------------------------
    // |
    // |    Agenda controller
    // |
    // +----------------------------------------------------------
    // |    Team 14
    // +----------------------------------------------------------
    
    /**
    * @class Agenda
    * @brief Controller-klasse voor het aanpassen van de agenda's van de zwemmers.
    * 
    * Controller-klasse voor het aanpassen van de agenda's van alle zwemmers.
    */

    public function __construct() {

        parent::__construct();
        
        /**
        * Laadt de auteur van de geschreven code van deze pagina in de footer.
        */

        // controleren of bevoegde persoon is aangemeld
        if (!$this->authex->isAangemeld()) {
            redirect('welcome/meldAan');
        } else {
            $persoon = $this->authex->getPersoonInfo();
            if ($persoon->soort != "Trainer") {
                redirect('welcome/meldAan');
            }
        }

        // Helpers inladen
        $this->load->helper("url");
        $this->load->helper('form');
        $this->load->helper("my_form_helper");
        $this->load->helper("my_html_helper");
        $this->load->helper("notation_helper");

        // Auteur inladen in footer
        $this->data = new stdClass();
        $this->data->team = array("Klied Daems" => "false", "Thibaut Joukes" => "false", "Jolien Lauwers" => "false", "Tom Nuyts" => "true", "Lise Van Eyck" => "false");
    }

    // +----------------------------------------------------------
    // |
    // |    Agenda beheren
    // |
    // +----------------------------------------------------------

    /**
     * Laat de index pagina zien.
     * 
     * @see ladenListGroup()
     * @see ladenZwemmers()
     * @see agenda_model::getAllTypeTraining()
     * @see agenda_model::getAllSupplementen()
     * @see agenda_model::getKleurenActiviteiten()
     * @see authex::getPersoonInfo()
     */
    
    public function index() {
        $data['titel'] = 'Agenda\'s zwemmers';
        $data['team'] = $this->data->team;
        $data['persoonAangemeld'] = $this->authex->getPersoonInfo();

        $this->load->model("zwemmer/agenda_model");
        $data['kleuren'] = json_encode($this->agenda_model->getKleurenActiviteiten());
        $data['listGroupItems'] = $this->ladenListGroup();
        $data['voorPersonen'] = $this->ladenZwemmers();
        $data['soortTraining'] = $this->agenda_model->getAllTypeTraining();
        $data['supplementennamen'] = $this->agenda_model->getAllSupplementen();

        $partials = array('hoofding' => 'main_header',
            'menu' => 'trainer_main_menu',
            'inhoud' => 'trainer/agenda_aanpassen',
            'voetnoot' => 'main_footer');

        $this->template->load('main_master', $partials, $data);
    }
    
    /**
     * Haalt alle zwemmers op en genereert een listGroup. Met deze listgroup kan je wisselen van agenda naar de agende van de persoon waarop je klikt.
     *
     * @see zwemmers_model::getZwemmers()
     */

    public function ladenListGroup() {
        $this->load->model("trainer/zwemmers_model");
        $zwemmers = $this->zwemmers_model->getZwemmers();
        sort($zwemmers);
        $zwemmersListGroup = [];

        foreach ($zwemmers as $zwemmer) {
            $zwemmersListGroup[] = '<a href="#" class="list-group-item list-group-item-action runFunction" data-id="' . $zwemmer->id . '">' . $zwemmer->voornaam . ' ' . $zwemmer->achternaam . '</a>';
        }

        return $zwemmersListGroup;
    }
    
    /**
     * Haalt alle zwemmers op en steekt deze in een array.
     *
     * @see zwemmers_model::getZwemmers()
     */

    public function ladenZwemmers() {
        $this->load->model("trainer/zwemmers_model");
        $zwemmers = $this->zwemmers_model->getZwemmers();

        $voorPersonen = [];
        foreach ($zwemmers as $zwemmer) {
            $voorPersonen[$zwemmer->id] = $zwemmer->voornaam . ' ' . $zwemmer->achternaam;
        }

        return $voorPersonen;
    }




    //////////////////
    ////////////////// INLADEN AGENDA
    //////////////////


    /**
     * Haalt alle activiteiten van de aangeduide zwemmer op en toont deze op het scherm in een agenda.
     * 
     * De verschillende soorten activiteiten worden via aparte methoden ingeladen en omgevormd tot één grote array.
     * Deze array wordt omgezet in een JSON object dat wordt ingeladen in de agenda.
     *
     * @see ladenWedstrijden($persoonId)
     * @see ladenOnderzoeken($persoonId)
     * @see ladenSupplementen($persoonId)
     * @see ladenActiviteiten($persoonId)
     */

    public function ladenAgendaPersoon() {
        $persoonId = $this->input->post('persoonId');

        $data_wedstrijden = $this->ladenWedstrijden($persoonId);
        $data_onderzoeken = $this->ladenOnderzoeken($persoonId);
        $data_supplementen = $this->ladenSupplementen($persoonId);
        $data_activiteiten = $this->ladenActiviteiten($persoonId);
        // Eén grote array maken van alle arrays om deze om te kunnen zetten in JSON code
        $data_agenda = array_merge($data_supplementen, $data_onderzoeken, $data_wedstrijden, $data_activiteiten);

        // $data_agenda omzetten in JSON code -> Deze wordt in de variabele $activiteiten gestopt
        $activiteiten = json_encode($data_agenda);

        print $activiteiten;
    }
    
    /**
     * Haalt alle activiteiten (trainingen en stages) van de aangemelde persoon op uit de database via het model en zet deze in een array.
     * 
     * @see agenda_model::getActiviteitenByPersoon($persoonId)
     * 
     * De verschillende trainingen krijgen allemaal een verschillende kleur toegewezen. Dit wordt gedaan met de volgende functie
     * 
     * @see kiesKleurActiviteiten($id)
     * @param int $persoonId De id van de persoon die aangemeld is.
     */

    public function ladenActiviteiten($persoonId) {
        // Trainingen en stages worden opgehaald uit het model en in een lijst gestoken
        $this->load->model("zwemmer/agenda_model");
        $activiteiten = $this->agenda_model->getActiviteitenByPersoon($persoonId);

        $data_activiteiten = array();

        // Trainingen en stages worden in een array gestoken -> dit doen we om later van de array JSON code te kunnen maken
        foreach ($activiteiten as $activiteit) {
            $color = $this->kiesKleurActiviteiten($activiteit->activiteit->id);

            $data_activiteiten[] = array(
                "extra" => $activiteit->activiteit->id,
                "description" => '',
                "title" => $activiteit->activiteit->stageTitel,
                "start" => $activiteit->activiteit->tijdstipStart,
                "end" => $activiteit->activiteit->tijdstipStop,
                "persoon" => $activiteit->persoonId,
                "color" => $color,
                "textColor" => '#000'
            );
        }

        return $data_activiteiten;
    }
    
    /**
     * Haalt alle wedstrijden van de aangemelde persoon op uit de database via het model en zet deze in een array.
     *
     * @see agenda_model::getWedstrijdenByPersoon($persoonId)
     * @param int $persoonId De id van de persoon die aangemeld is.
     */

    public function ladenWedstrijden($persoonId) {
        // Wedstrijden worden opgehaald uit het model en in een lijst gestoken
        $this->load->model("zwemmer/agenda_model");
        $wedstrijden = $this->agenda_model->getWedstrijdenByPersoon($persoonId);

        $data_wedstrijden = array();

        // Wedstrijden worden in een array gestoken -> dit doen we om later van de array JSON code te kunnen maken
        foreach ($wedstrijden as $wedstrijd) {
            $data_wedstrijden[] = array(
                "extra" => $wedstrijd->wedstrijd->id,
                "title" => $wedstrijd->wedstrijd->naam, // Titel van het event in de agenda
                "description" => '',
                "start" => $wedstrijd->wedstrijd->datumStart, // Beginuur/begindatum van het event in de agenda
                "end" => $wedstrijd->wedstrijd->datumStop, // Einduur/einddatum van het event in de agenda
                "persoon" => $wedstrijd->persoonId,
                "color" => $this->agenda_model->getKleurActiviteit(1)->kleur, // Kleur van het event in de agenda
                "textColor" => '#000' // Tekstkleur van het event in de agenda
            );
        }

        return $data_wedstrijden;
    }
    
    /**
     * Haalt alle medische afspraken van de aangemelde persoon op uit de database via het model en zet deze in een array.
     *
     * @see agenda_model::getOnderzoekenByPersoon($persoonId)
     * @param int $persoonId De id van de persoon die aangemeld is.
     */

    public function ladenOnderzoeken($persoonId) {
        // Medische onderzoeken worden opgehaald uit het model en in een lijst gestoken
        $this->load->model("zwemmer/agenda_model");
        $onderzoeken = $this->agenda_model->getOnderzoekenByPersoon($persoonId);

        $data_onderzoeken = array();

        // Medische onderzoeken worden in een array gestoken -> dit doen we om later van de array JSON code te kunnen maken
        foreach ($onderzoeken as $onderzoek) {
            $data_onderzoeken[] = array(
                "extra" => $onderzoek->id,
                "title" => $onderzoek->omschrijving,
                "description" => '',
                "start" => $onderzoek->tijdstipStart,
                "end" => $onderzoek->tijdstipStop,
                "persoon" => $onderzoek->persoonId,
                "color" => $this->agenda_model->getKleurActiviteit(2)->kleur,
                "textColor" => '#000'
            );
        }

        return $data_onderzoeken;
    }
    
     /**
     * Haalt alle supplementen van de aangemelde persoon op uit de database via het model en zet deze in een array.
     *
     * @see agenda_model::getSupplementenByPersoon($persoonId)
     * @param int $persoonId De id van de persoon die aangemeld is.
     */

    public function ladenSupplementen($persoonId) {
        // Supplementen worden opgehaald uit het model en in een lijst gestoken
        $this->load->model("zwemmer/agenda_model");
        $supplementen = $this->agenda_model->getSupplementenByPersoon($persoonId);

        $data_supplementen = array();

        // Supplementen worden in een array gestoken -> dit doen we om later van de array JSON code te kunnen maken
        foreach ($supplementen as $supplement) {
            if ($supplement->datumStop !== null) {
                $data_supplementen[] = array(
                    "extra" => $supplement->id,
                    "description" => $supplement->functie->supplementFunctie . ', ' . $supplement->hoeveelheid . ' keer',
                    "title" => $supplement->supplement->naam,
                    "start" => $supplement->datumStart,
                    "end" => $supplement->datumStop,
                    "persoon" => $supplement->persoonId,
                    "color" => $this->agenda_model->getKleurActiviteit(8)->kleur,
                    "textColor" => '#fff'
                );
            }
            else {
                $data_supplementen[] = array(
                    "extra" => $supplement->id,
                    "description" => $supplement->functie->supplementFunctie . ', ' . $supplement->hoeveelheid . ' keer',
                    "title" => $supplement->supplement->naam,
                    "start" => $supplement->datumStart,
                    "persoon" => $supplement->persoonId,
                    "color" => $this->agenda_model->getKleurActiviteit(8)->kleur,
                    "textColor" => '#fff'
                );
            }
        }

        return $data_supplementen;
    }




    //////////////////
    ////////////////// INLADEN AGENDA - EXTRA'S
    //////////////////



    
    /**
     * Geeft elke soort training een verschillende kleur.
     *
     * @see agenda_model::getActiviteit($id)
     * @param int $id De id van de activiteit.
     */

    public function kiesKleurTraining($id) {
        $this->load->model("zwemmer/agenda_model");
        $activiteit = $this->agenda_model->getActiviteit($id);

        // Verschillende typen trainingen krijgen allemaal een andere achtergrondkleur
        switch ($activiteit->typeTrainingId) {
            case 1:
                $color = $this->agenda_model->getKleurActiviteit(3)->kleur; // Kleur krachttraining
                break;

            case 2:
                $color = $this->agenda_model->getKleurActiviteit(4)->kleur; // Kleur houdingstraining
                break;

            case 3:
                $color = $this->agenda_model->getKleurActiviteit(5)->kleur; // Kleur zwemtraining
                break;

            case 4:
                $color = $this->agenda_model->getKleurActiviteit(6)->kleur; // Kleur conditietraining
                break;

            case NULL:
                $color = $this->agenda_model->getKleurActiviteit(7)->kleur; // Kleur stage
                break;
        }

        return $color;
    }
    
    /**
     * Onderscheidt de twee soorten activiteiten in stages en trainingen. Stages krijgen een kleur en elke soort training krijgt een kleur (via een andere functie).
     *
     * @see kiesKleurTraining($id)
     * @param int $id De id van de activiteit.
     */

    public function kiesKleurActiviteiten($id) {
        $this->load->model("zwemmer/agenda_model");
        $activiteit = $this->agenda_model->getActiviteit($id);

        // Stage en training krijgen beiden een andere achtergrondkleur
        switch ($activiteit->typeActiviteitId) {
            case 1:
                $color = $this->kiesKleurTraining($id); // Meerdere typen trainingen -> Kleur wordt bepaald in nieuwe functie
                break;

            case 2:
                $color = $this->agenda_model->getKleurActiviteit(7)->kleur; // Kleur stage
                break;

            default:
                break;
        }

        return $color;
    }




    //////////////////
    ////////////////// INVULLEN MODAL AGENDA
    //////////////////



    
     /**
     * Haalt de activiteit op die gewijzigd moet worden en zet deze om in een JSON object.
     *
     * @see agenda_model::getActiviteit($id)
     * @param int $id De id van de activiteit die gewijzigd dient te worden.
     */

    public function wijzigActiviteit($id) {
        $this->load->model("trainer/agenda_model");
        $data = $this->agenda_model->getActiviteit($id);

        print json_encode($data);
    }
    
    /**
     * Maakt een nieuwe activiteit aan om toe te voegen en zet deze om in een JSON object
     *
     * @see agenda_model::getTypeActiviteit($typeActiviteitId)
     * @see agenda_model::getTypeTraining($typeTrainingId)
     * @see agenda_model::getPersonenFromActiviteit($id)
     * @param bool $isReeks Bepaalt of je een reeks of enkele training/stage toevoegt.
     * @param Date $startDate De startdatum van je nieuwe activiteit.
     * @param Date $endDate De stopdatum van je nieuwe activiteit.
     */

    public function toevoegenActiviteit($isReeks, $startDate, $endDate) {
        $this->load->model('trainer/agenda_model');

        $data = new stdClass();

        $data->id = '0';

        $startDatum = date('Y-m-d H:i:s', strtotime(str_replace('%20', ' ', $startDate)));
        $stopDatum = date('Y-m-d H:i:s', strtotime(str_replace('%20', ' ', $endDate)));

        $data->tijdstipStart = $startDatum;
        $data->tijdstipStop = $stopDatum;

        $data->typeTrainingId = '1';
        $data->stageTitel = '';
        $data->typeActiviteitId = '1';
        if ($isReeks === 'true') {
            $reeksen = $this->agenda_model->getAllReeksen();

            $data->reeksId = -end($reeksen) - 1;
        }
        else {
            $data->reeksId = null;
        }

        $data->typeActiviteit = $this->agenda_model->getTypeActiviteit($data->typeActiviteitId);
        $data->typeTraining = $this->agenda_model->getTypeTraining($data->typeTrainingId);
        $data->personen = $this->agenda_model->getPersonenFromActiviteit($data->id);


        print json_encode($data);
    }
    
    /**
     * Haalt de wedstrijd op die gewijzigd moet worden en zet deze om in een JSON object.
     *
     * @see agenda_model::getWedstrijd($id)
     * @param int $id De id van de wedstrijd die gewijzigd dient te worden.
     */

    public function wijzigWedstrijd($id) {
        $this->load->model("trainer/agenda_model");
        $data = $this->agenda_model->getWedstrijd($id);

        print json_encode($data);
    }
    
    /**
     * Haalt de medische afspraak op die gewijzigd moet worden en zet deze om in een JSON object.
     *
     * @see agenda_model::getOnderzoek($id)
     * @param int $id De id van de medische afspraak die gewijzigd dient te worden.
     */

    public function wijzigOnderzoek($id) {
        $this->load->model("zwemmer/agenda_model");
        $data = $this->agenda_model->getOnderzoek($id);

        print json_encode($data);
    }
    
    /**
     * Maakt een nieuwe medische afspraak aan om toe te voegen en zet deze om in een JSON object
     *
     * @param bool $persoonId De Id van de persoon waarvoor je een nieuwe medische afspraak toevoegt.
     * @param Date $startDate De startdatum van je nieuwe medische afspraak.
     * @param Date $endDate De stopdatum van je nieuwe medische afspraak.
     */

    public function toevoegenOnderzoek($persoonId, $startDate, $endDate) {
        $data = new stdClass();

        $data->id = 0;
        $data->persoonId = $persoonId;
        $data->tijdstipStart = date('Y-m-d H:i:s', strtotime(str_replace('%20', ' ', $startDate)));
        $data->tijdstipStop = date('Y-m-d H:i:s', strtotime(str_replace('%20', ' ', $endDate)));
        $data->omschrijving = '';

        print json_encode($data);
    }
    
    /**
     * Haalt het supplement op dat gewijzigd moet worden en zet deze om in een JSON object.
     *
     * @see agenda_model::getSupplement($id)
     * @param int $id De id van het supplement dat gewijzigd dient te worden.
     */

    public function wijzigSupplement($id) {
        $this->load->model("zwemmer/agenda_model");
        $data = $this->agenda_model->getSupplement($id);

        print json_encode($data);
    }
    
    /**
     * Maakt een nieuw supplement aan om toe te voegen en zet deze om in een JSON object
     *
     * @see agenda_model::getSupplementPersoon($supplementId)
     * @see agenda_model::getSupplementFunctie($supplementFunctieId)
     * @param bool $persoonId De Id van de persoon waarvoor je een nieuw supplement toevoegt.
     * @param Date $startDate De startdatum van je nieuw supplement.
     * @param Date $endDate De stopdatum van je nieuw supplement.
     */

    public function toevoegenSupplement($persoonId, $startDate, $endDate) {
        $this->load->model('zwemmer/agenda_model');

        $data = new stdClass();

        $data->id = 0;
        $data->supplementId = 1;
        $data->persoonId = $persoonId;

        $stopDatum = new DateTime(str_replace('%20', ' ', $endDate));
        $startDatum = new DateTime(str_replace('%20', ' ', $startDate));
//        var_dump($stopDatum->modify('-1 day'));
//        var_dump($startDatum);
        $data->datumStart = $startDatum->format('Y-m-d');

        if ($startDatum != $stopDatum->modify('-1 day')) {
            $data->datumStop = $stopDatum->format('Y-m-d');
        }
        else {
            $data->datumStop = null;
        }
        $data->hoeveelheid = '';
        $data->supplement = $this->agenda_model->getSupplementPersoon($data->supplementId);
        $data->functie = $this->agenda_model->getSupplementFunctie($data->supplement->supplementFunctieId);

        print json_encode($data);
    }




    //////////////////
    ////////////////// UPDATEN, INSERTEN AGENDA
    //////////////////



    
    /**
     * Haal alle informatie van de activiteit op uit het modal en stuur deze door naar het insert- of updatemodel om deze in de database te zetten.
     *
     * @see agenda_model::getReeksActiviteiten($reeksId)
     * @see agenda_model::deleteActiviteitPerPersoonWithActiviteitId($activiteitId)
     * @see agenda_model::deleteActiviteit($activiteitId)
     * @see agenda_model::insertActiviteit($activiteit)
     * @see agenda_model::updateActiviteit($activiteit)
     * @see agenda_model::getPersonenFromActiviteit($activiteitId)
     * @see agenda_model::insertActiviteitPerPersoon($activiteitPerPersoon)
     * @see agenda_model::updateActiviteitPerPersoon($activiteitPerPersoon)
     * @see agenda_model::deleteActiviteitPerPersoon($activiteitPerPersoonId)
     */

    public function registreerActiviteit() {
        $this->load->model('trainer/agenda_model');
        $this->load->model('trainer/zwemmers_model');
        $uren = array('06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30', '24:00');
        $activiteit = new stdClass();
        $activiteitPerPersoon = new stdClass();
        $activiteitId = 0;
        $datums[] = '';
        $activiteitenIDs = [];

        // Tabel activiteit
        $postId = $this->input->post('id');
        $id = intval($postId);

        if ($this->input->post('soort') !== '4') {
            $activiteit->typeTrainingId = $this->input->post('soort')+1;
            $activiteit->typeActiviteitId = 1;
        }
        else {
            $activiteit->typeTrainingId = null;
            $activiteit->typeActiviteitId = 2;
        }
        $activiteit->stageTitel = $this->input->post('gebeurtenisnaam');

        $checkIfReeks = $this->input->post('reeksId');

        if ($checkIfReeks !== '') {
            $reeksId = $checkIfReeks;
            var_dump($reeksId);

            $begindatumReeks = zetOmNaarYYYYMMDD($this->input->post('begindatumReeks'));
            $einddatumReeks = zetOmNaarYYYYMMDD($this->input->post('einddatumReeks'));
            $beginuur = $uren[$this->input->post('beginuurReeks')];
            $einduur = $uren[$this->input->post('einduurReeks')];

            $datums = $this->maakReeksen($begindatumReeks, $einddatumReeks, $beginuur, $einduur);

            // Enkel voor UPDATEN

            if ($id !== 0) {
                $activiteiten = $this->agenda_model->getReeksActiviteiten($reeksId);

                foreach ($activiteiten as $activiteit1) {
                    $this->agenda_model->deleteActiviteitPerPersoonWithActiviteitId($activiteit1->id);
                    $this->agenda_model->deleteActiviteit($activiteit1->id);
                }
            }

            for ($i = 0; $i < count($datums); $i++) {
                $activiteit->tijdstipStart = $datums[$i][0];
                $activiteit->tijdstipStop = $datums[$i][1];

                if ($id === 0) {
                    $activiteit->reeksId = $reeksId;
                    $activiteitenIDs[] = $this->agenda_model->insertActiviteit($activiteit);
                }
                else {
                        print('inserting...');
                        $activiteit->reeksId = $reeksId;
                        $this->agenda_model->insertActiviteit($activiteit);
                }
            }
        }
        else {
            $activiteit->tijdstipStart = zetOmNaarYYYYMMDD($this->input->post('begindatum')) . ' ' . $uren[$this->input->post('beginuur')] . ':00';
            $activiteit->tijdstipStop = zetOmNaarYYYYMMDD($this->input->post('einddatum')) . ' ' . $uren[$this->input->post('einduur')] . ':00';

            if ($id === 0) {
                $activiteit->reeksId = null;
                $activiteitId = $this->agenda_model->insertActiviteit($activiteit);
            }
            else {
                $activiteit->id = $id;
                $activiteit->reeksId = null;
                $this->agenda_model->updateActiviteit($activiteit);
            }
        }

        // Tabel activiteitenPerPersoon

        $personenChecked = $this->input->post('personen');

        if ($checkIfReeks !== '') {
            $reeksId = $checkIfReeks;
            if ($id !== 0) {
                $activiteiten = $this->agenda_model->getReeksActiviteiten($reeksId);

                $personenActiviteit = $this->agenda_model->getPersonenFromActiviteit($activiteiten[0]->id);
            }

            if ($id === 0) {
                foreach ($personenChecked as $persoonChecked) {
                    foreach ($activiteitenIDs as $activiteitenID) {
                        $activiteitPerPersoon->persoonId = $persoonChecked;
                        $activiteitPerPersoon->activiteitId = $activiteitenID;
                        $this->agenda_model->insertActiviteitPerPersoon($activiteitPerPersoon);
                    }
                }
            }
            else {
                for ($i = 0; $i < count($datums); $i++) {
                    if ($id !== 0) {
                        $activiteitPerPersoon->activiteitId = $activiteiten[$i]->id;

                        foreach ($personenActiviteit as $persoonActiviteit) {
                            if (!in_array($persoonActiviteit, $personenChecked)) {
                                $activiteitPerPersoonDelete = $this->agenda_model->getActiviteitPerPersoon($persoonActiviteit, $activiteiten[$i]->id);
                                $this->agenda_model->deleteActiviteitPerPersoon($activiteitPerPersoonDelete->id);
                            }
                        }
                    }

                    if (array_search(end($activiteiten), $activiteiten) < $i) {
                        print('inserting...');
                        $activiteitPerPersoon->persoonId = $persoonChecked;
                        $this->agenda_model->insertActiviteitPerPersoon($activiteitPerPersoon);
                    }
                    else {
                        foreach ($personenChecked as $persoonChecked) {
                            $activiteitPerPersoon->persoonId = $persoonChecked;

                            if ($this->agenda_model->getActiviteitPerPersoon($persoonChecked, $activiteiten[$i]->id) !== null) {
                                $activiteitPerPersoon->activiteitId = $activiteiten[$i]->id;
                                $this->agenda_model->updateActiviteitPerPersoon($activiteitPerPersoon);
                            }
                            else {
                                $this->agenda_model->insertActiviteitPerPersoon($activiteitPerPersoon);
                            }
                        }
                    }
                }
            }
        }
        else{
            $personenActiviteit = $this->agenda_model->getPersonenFromActiviteit($id);
            $activiteitPerPersoon->activiteitId = $id;

            foreach ($personenActiviteit as $persoonActiviteit) {
                if (!in_array($persoonActiviteit, $personenChecked)) {
                    $activiteitPerPersoonDelete = $this->agenda_model->getActiviteitPerPersoon($persoonActiviteit, $id);
                    $this->agenda_model->deleteActiviteitPerPersoon($activiteitPerPersoonDelete->id);
                }
            }

            if ($id === 0) {
                foreach ($personenChecked as $persoonChecked) {
                    var_dump(5);
                    $activiteitPerPersoon->persoonId = $persoonChecked;
                    $activiteitPerPersoon->activiteitId = $activiteitId;
                    $this->agenda_model->insertActiviteitPerPersoon($activiteitPerPersoon);
                }
            }
            else {
                foreach ($personenChecked as $persoonChecked) {
                    $activiteitPerPersoon->persoonId = $persoonChecked;

                    if ($this->agenda_model->getActiviteitPerPersoon($persoonChecked, $id) !== null) {
                        $activiteitPerPersoon->activiteitId = $id;
                        $this->agenda_model->updateActiviteitPerPersoon($activiteitPerPersoon);
                    }
                    else {
                        var_dump(6);
                        $this->agenda_model->insertActiviteitPerPersoon($activiteitPerPersoon);
                    }
                }
            }
        }

        redirect('/Trainer/agenda');
    }
    
    /**
     * Maakt reeksen aan van de begin- en einddatum.
     *
     * @see agenda_model::getReeksActiviteiten($reeksId)
     * @see agenda_model::deleteActiviteitPerPersoonWithActiviteitId($activiteitId)
     * 
     * @param Date $begindatumReeks De startdatum van de reeks.
     * @param Date $einddatumReeks De stopdatum van de reeks.
     * @param String $beginuur Het startuur van de reeks.
     * @param String $einduur Het einduur van de reeks.
     */

    public function maakReeksen($begindatumReeks, $einddatumReeks, $beginuur, $einduur) {
        $datums = [];
        $datumStart = new \DateTime($begindatumReeks);
        $datumStop = new \DateTime($einddatumReeks);

        if ($datumStart > $datumStop) {
            return $datums;
        }

        while ($datumStart <= $datumStop) {
            $dataMetUur = [];
            $dataMetUur[] = $datumStart->format('Y-m-d') . ' ' . $beginuur . ':00';
            $dataMetUur[] = $datumStart->format('Y-m-d') . ' ' . $einduur . ':00';

            $datums[] = $dataMetUur;
            $datumStart->modify('+1 week');
        }

        return $datums;
    }
    
    /**
     * Haal alle informatie van het supplement op uit het modal en stuur deze door naar het insert- of updatemodel om deze in de database te zetten.
     *
     * @see agenda_model::insertSupplement($supplement)
     * @see agenda_model::updateSupplement($supplement)
     */

    public function registreerSupplement() {
        $this->load->model('trainer/agenda_model');
        $supplement = new stdClass();
        $id = intval($this->input->post('id'));
        $supplement->supplementId = $this->input->post('supplementnaam') + 1;
        $supplement->persoonId = $this->input->post('persoonSupplement');
        $supplement->hoeveelheid = $this->input->post('hoeveelheid');

        // Checken of het enkel of een reeks is
        if ($this->input->post('einddatumSupplement') === '') {
            $supplement->datumStart = zetOmNaarYYYYMMDD($this->input->post('datum'));
            $supplement->datumStop = null;


            // Checken of je iets wijzigt of toevoegt
            if ($id === 0) {
                $this->agenda_model->insertSupplement($supplement);
            }
            else {
                $supplement->id = $id;
                $this->agenda_model->updateSupplement($supplement);
            }
        }
        else {
            $supplement->datumStart = zetOmNaarYYYYMMDD($this->input->post('begindatumSupplement'));
            $datumStop = zetOmNaarYYYYMMDD($this->input->post('einddatumSupplement'));
            $supplement->datumStop = date('Y-m-d', strtotime($datumStop . ' +1 day'));

            // Checken of je iets wijzigt of toevoegt
            if ($id === 0) {
                $this->agenda_model->insertSupplement($supplement);
            }
            else {
                $supplement->id = $id;
                $this->agenda_model->updateSupplement($supplement);
            }
        }

        redirect('Trainer/agenda');
    }
    
    /**
     * Haal alle informatie van de medische afspraak op uit het modal en stuur deze door naar het insert- of updatemodel om deze in de database te zetten.
     *
     * @see agenda_model::insertOnderzoek($onderzoek)
     * @see agenda_model::updateOnderzoek($onderzoek)
     */

    public function registreerOnderzoek() {
        $this->load->model('trainer/agenda_model');
        $uren = array('06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30', '24:00');
        $onderzoek = new stdClass();
        $id = intval($this->input->post('id'));

        $onderzoek->persoonId = $this->input->post('persoonSupplement');
        $onderzoek->tijdstipStart = zetOmNaarYYYYMMDD($this->input->post('begindatum')) . ' ' . $uren[$this->input->post('beginuur')] . ':00';
        $onderzoek->tijdstipStop = zetOmNaarYYYYMMDD($this->input->post('einddatum')) . ' ' . $uren[$this->input->post('einduur')] . ':00';
        $onderzoek->omschrijving = $this->input->post('gebeurtenisnaam');

        if ($id === 0) {
            $this->agenda_model->insertOnderzoek($onderzoek);
        }
        else {
            $onderzoek->id = $id;
            $this->agenda_model->updateOnderzoek($onderzoek);
        }

        redirect('Trainer/agenda');
    }




    //////////////////
    ////////////////// VERWIJDEREN ACTIVITEIT
    //////////////////




    /**
     * Verwijdert de activiteit.
     *
     * @see agenda_model::getActiviteit($activiteitId)
     * @see agenda_model::getReeksActiviteiten($reeksId)
     * @see agenda_model::deleteActiviteitPerPersoonWithActiviteitId($activiteitId)
     * @see agenda_model::deleteActiviteit($activiteitId)
     * 
     * @param int $id De id van de activiteit die verwijdert dient te worden.
     */
    
    public function verwijderActiviteit($id) {
        $this->load->model('trainer/agenda_model');

        $activiteit = $this->agenda_model->getActiviteit($id);

        if ($activiteit->reeksId === null) {
            $this->agenda_model->deleteActiviteitPerPersoonWithActiviteitId($id);
            $this->agenda_model->deleteActiviteit($id);
        }
        else {
            $activiteiten = $this->agenda_model->getReeksActiviteiten($activiteit->reeksId);
            foreach ($activiteiten as $activiteit1) {
                $this->agenda_model->deleteActiviteitPerPersoonWithActiviteitId($activiteit1->id);
                $this->agenda_model->deleteActiviteit($activiteit1->id);
            }
        }

        redirect('/Trainer/agenda');
    }
    
    /**
     * Verwijdert de medische afspraak.
     *
     * @see agenda_model::getOnderzoek($onderzoekId)
     * @see agenda_model::deleteOnderzoek($onderzoekId)
     * 
     * @param int $id De id van de medische afspraak die verwijdert dient te worden.
     */

    public function verwijderOnderzoek($id) {
        $this->load->model('trainer/agenda_model');
        $onderzoek = $this->agenda_model->getOnderzoek($id);

        $this->agenda_model->deleteOnderzoek($onderzoek->id);

        redirect('/Trainer/agenda');
    }
    
    /**
     * Verwijdert het supplement.
     *
     * @see agenda_model::getSupplementPerPersoon($supplementId)
     * @see agenda_model::deleteSupplement($supplementId)
     * 
     * @param int $id De id van het supplement dat verwijdert dient te worden.
     */

    public function verwijderSupplement($id) {
        $this->load->model('trainer/agenda_model');
        $supplement = $this->agenda_model->getSupplementPerPersoon($id);

        $this->agenda_model->deleteSupplement($supplement->id);

        redirect('/Trainer/agenda');
    }
}
