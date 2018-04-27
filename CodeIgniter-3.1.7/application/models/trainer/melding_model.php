<?php

class Melding_model extends CI_Model {

    // +----------------------------------------------------------
    // |    Trainingscentrum Wezenberg
    // +----------------------------------------------------------
    // |    Auteur: Lise Van Eyck       |       Helper:
    // +----------------------------------------------------------
    // |
    // |    Melding model
    // |
    // +----------------------------------------------------------
    // |    Team 14
    // +----------------------------------------------------------

    function __construct() {
        parent::__construct();

    }
    
    function get($id) {
        
        $this->db->where('id', $id);
        $query = $this->db->get('meldingperpersoon');
        $meldingPerPersoon = $query->row();
       
            $this->db->where('id', $meldingPerPersoon->persoonId);
            $queryPersoon = $this->db->get('persoon');
            $this->db->where('id', $meldingPerPersoon->meldingId);
            $queryMelding = $this->db->get('melding');
            $persoon = $queryPersoon->row();
            $melding = $queryMelding->row();
            
            $obj_merged = (object) array_merge((array)$persoon, (array)$melding);

        
        return $obj_merged;
    }

    public function getMeldingPerPersoon() {
        $query = $this->db->get('meldingperpersoon');
        $meldingPerPersoon = $query->result();
        
        
        
        $meldingenPerPersoon = array();
        foreach ($meldingPerPersoon as $item) {
            $meldingpersoon = array();
            $meldingpersoon['meldingPerPersoon'] = $item->id;
            
            
            $this->db->where('id', $item->persoonId);
            $queryPersoon = $this->db->get('persoon');
            $this->db->where('id', $item->meldingId);
            $queryMelding = $this->db->get('melding');
            $persoon = $queryPersoon->row();
            $melding = $queryMelding->row();
            
            $obj_merged = (object) array_merge((array)$persoon, (array)$melding, (array)$meldingpersoon);
            array_push($meldingenPerPersoon, $obj_merged);
             
        }
 
        return $meldingenPerPersoon;
    }
    
    /**
     * Verwijdert het record met id=$id uit de tabel melding
     * @param $id De id van het record dat opgevraagd wordt
     */
    
    function delete($id){
        $this->db->where('id', $id);
        $this->db->delete('melding');
    }
    
    /**
     * Voegt een nieuw record toe aan de tabel melding
     * 
     * @param $melding Het meldingen object waar de ingevulde data in zit
     */
    function insert($melding) {
        $this->db->insert('melding', $melding);
    }
    
    /**
     * Wijzigt een melding-record uit de tabel melding
     * 
     * @param $melding Het meldingen object waar de aangepaste data in zit
     */
    function update($melding) {
        $this->db->where('id', $melding->id);
        $this->db->update('melding', $melding);
    }
}
