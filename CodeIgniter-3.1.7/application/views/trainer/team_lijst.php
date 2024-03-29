
<?php
// +----------------------------------------------------------
// |    Trainingscentrum Wezenberg
// +----------------------------------------------------------
// |    Auteur: Klaus Daems       |       Helper:
// +----------------------------------------------------------
// |
// |    Training home
// |
// +----------------------------------------------------------
// |    Team 14
// +----------------------------------------------------------
?>
<?php
$aanpassen = array('class' => 'btn btn-warning btn-lg btn-round', 'data-toggle' => 'tooltip', 'title' => 'Team aanpassen');
?>


<div id="team">
    <div>
        <?php
        echo "<p>" . anchor('Trainer/Team/aanpassen', form_button("knopaanpassen", "<i class='fas fa-bars'></i>", $aanpassen)) . "</p>";
        ?>
        <br>
    </div>
    <?php
    foreach ($zwemmers as $zwemmer){
        echo "<div><h2>" . toonAfbeelding('Profiel/Avatar_' . $zwemmer->voornaam . "_" . $zwemmer->achternaam . '.jpg', 'id="avatar" class="shadow img-circle"') . " " . $zwemmer->voornaam . " " . $zwemmer->achternaam . "</h2></div>";
        } ?>
</div>
