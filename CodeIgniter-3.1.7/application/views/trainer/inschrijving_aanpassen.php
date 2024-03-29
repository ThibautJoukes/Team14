<?php
/**
 * @file inschrijving_aanpassen.php
 * 
 * View waarin een lijst van inschrijving gegevens worden weergegeven
 * - krijgt een $inschrijvingen-object binnen
 */
// +----------------------------------------------------------
// |    Trainingscentrum Wezenberg
// +----------------------------------------------------------
// |    Auteur: Lise Van Eyck       |       Helper:
// +----------------------------------------------------------
// |
// |    Inschrijving aanpassen view
// |
// +----------------------------------------------------------
// |    Team 14
// +----------------------------------------------------------
?>

<table class="table">
    <thead>
        <tr>
            <th>Wedstrijd</th>
            <th>Reeks</th>
            <th>Zwemmer</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($inschrijvingen as $inschrijving) {
            echo "<tr id='" . $inschrijving->inschrijving . "'><td>" . $inschrijving->naam . "</td><td>" . $inschrijving->afstand . ' ' . $inschrijving->slag . "</td><td>" . $inschrijving->voornaam . ' ' . $inschrijving->achternaam . "</td><td>" .
            "<button type='button' class='btn btn-success' id='accepteer" . $inschrijving->inschrijving . "' onclick='inschrijvingGoedkeuren(this.id)' value='" . $inschrijving->inschrijving . "'><i class='fas fa-check'></i></button></td><td>"
            . "<button type='button' class='btn btn-danger' id='verwijder" . $inschrijving->inschrijving . "' onclick='inschrijvingAfkeuren(this.id)' value='" . $inschrijving->inschrijving . "'><i class='fas fa-times'></i></button></td></tr>\n";

            ;
        }
        ?>

    </tbody>
</table>


