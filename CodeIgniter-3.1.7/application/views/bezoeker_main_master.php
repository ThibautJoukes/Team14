<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php echo $titel ?></title>

        <!-- Stylesheets -->

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <?php echo pasStylesheetAan("bezoeker_eigenStijl.css"); ?>
        <?php echo pasStylesheetAan("scroll.css"); ?>
        <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

        <script type="text/javascript">
            var site_url = '<?php echo site_url(); ?>';
            var base_url = '<?php echo base_url(); ?>';
        </script>

    </head>

    <body>

        <!-- Navigatie -->

        <nav class="navbar navbar-expand-sm navbar-dark fixed-top">
            <div class="container">

                <!-- logo in navigatie -->

                <a class="navbar-brand font-weight-bold" href="#">TCW</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- links in navigatie -->

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Startpagina</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Wedstrijden</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Team</a>
                        </li>

                        <!-- login knop -->
                        <li class="nav-item">
                            <a href="#">
                                <button type="button" class="btn btn-outline-light" id="login-button">
                                    <?php if ($persoon == null) {
                                      
                                    // niet aangemeld                                    
                                    echo divAnchor('Welcome/meldAan', 'Aanmelden'); 
                                    // aangemeld
                                    } else {
                                        echo divAnchor('Welcome/meldAf', 'Afmelden');} ?>
                                </button>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Achtergrond -->

        <div class="jumbotron jumbotron-fluid d-flex align-items-center position-relative">
            <div id="background" class="container text-white">

                <!-- Tekst die getoond wordt op de achtergrondafbeelding -->

                <div id="background-text">
                    <p class="lead mb-0 font-weight-light">Welkom bij</p>
                    <h2 class="font-weight-bold">Trainingscentrum Wezenberg</h2>
                </div>
                <div id="parallelogram"></div>

                <!-- Symbool dat aanduidt dat je kan scrollen -->

                <a href="#inhoud" id="scroll-symbol">
                    <div class="center-do-not-use position-absolute">
                        <div class="mouse">
                            <div class="wheel"></div>
                        </div>
                        <div>
                            <span class="arrow"></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Inhoud -->

        <main id="inhoud">
        </main>

        <!-- Voetnoot -->

        <footer>
            <div class="container pb-3 font-weight-light">
                <div class="text-center">
                    <p>Trainingscentrum WEZENBERG</p>
                    <p id="footer-follow">Volg ons op <a href="https://www.facebook.com/Trainingscentrum-Wezenberg-TCW-514839802012875/"><i class="fa fa-facebook-square"></i></a> facebook</p>
                </div>
                <hr>

                <!-- Navigatie-links in de voetnoot -->

                <div class="d-flex justify-content-center col-12">
                    <div id="footer-links" class="col-lg-6 col-md-9 col-sm-12 col-12 d-flex justify-content-sm-between text-center flex-sm-row flex-column">
                        <a href="#">Startpagina</a>
                        <a href="#">Wedstrijden</a>
                        <a href="#">Team</a>
                        <a href="#">Login</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Javascript scriptjes -->

        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script>

            $(document).ready(function () {
                
                //Script om de navigatie te veranderen bij het scrollen
                
                $(window).scroll(function () {
                    var scroll = $(window).scrollTop();
                    if (scroll > 54) {
                        $('nav').removeClass("navbar-dark");
                        $('nav').addClass("navbar-scrolling navbar-light");
                        $('.nav-link').addClass('text-darkblue');
                        $('#login-button').addClass('login-button-darkblue');
                        $('#login-button').removeClass('btn-outline-light');
                        $('.navbar-brand').addClass('text-darkblue');
                        $('.active').addClass('active-darkblue');
                    } else {
                        $('nav').removeClass("navbar-scrolling navbar-light");
                        $('nav').addClass("navbar-dark");
                        $('.nav-link').removeClass('text-darkblue');
                        $('#login-button').removeClass('login-button-darkblue');
                        $('#login-button').addClass('btn-outline-light');
                        $('.navbar-brand').removeClass('text-darkblue');
                        $('.active').removeClass('active-darkblue');
                    }
                });
                
                //Script om te scrollen naar een id
                
                $('a[href*=\\#]').on('click', function (event) {
                    event.preventDefault();
                    $('html,body').animate({scrollTop: $(this.hash).offset().top}, 500);
                });

            });

        </script>
    </body>

</html>
