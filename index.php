<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Disi IN Portal">

  <meta name="author" content="Franz Bertin" />

  <title>Eden - Projet Cassiopée - 2024</title>

  <meta http-equiv="refresh" content="30">

  <!-- Favicon-->
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />

  <!-- Core theme CSS (includes Bootstrap)-->
  <link href="css/styles.css" rel="stylesheet" />
</head>

<body>
    <div class="d-flex" id="wrapper">

        <!-- Sidebar-->
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-light" align="center">
                <?php
                echo ($_SERVER['REMOTE_USER'] ?? '').PHP_EOL;
                ?>
                &nbsp;
            </div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="">Documentation</a>
                
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="./index.php">Home</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="./teachers/index.php">Teachers pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="./students/index.php">Students pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="./admin/index.php">Admin pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="./fbertin/index.php">Perso fbertin (tests CAS)</a>

		<a class="list-group-item list-group-item-action list-group-item-light p-3" href="">
                    <table width="100%">
                        <tr><td width="100%" align="center"><b>2024</b></td></tr>
                    </table>
                </a>
            </div>
        </div>

        <!--  Page content wrapper - Top navigation -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">Menu</button>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item active" style="margin: 5px"><a class="nav-link" href="https://github.com/Nishogi/EDEN" target="_blank">code source</a></li>
                        <liv class="nav-item active">
                        <a class="nav-link dropdown-toggle" style="margin: 5px" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Team</a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#!">franz.bertin@imtbs-tsp.eu</b></a>
                                <a class="dropdown-item" href="#!">christophe.gaboret@imtbs-tsp.eu</b></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#!">tom.burellier@telecom-sudparis.eu</a>
                                <a class="dropdown-item" href="#!">nicolas.rocq@telecom-sudparis.eu</a>
                                <a class="dropdown-item" href="#!">mathis.williot@telecom-sudparis.eu</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#!">olivier.berger@telecom-sudparis.eu</a>
                                <a class="dropdown-item" href="#!">arthur.jovart@telecom-sudparis.eu</a>
                                <a class="dropdown-item" href="#!"></a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Page content-->
            <div class="container-fluid">
                <p>&nbsp;<p>
                <table width="100%" border="0">
                    <tr>
                        <td width="80%"><h1><i>Eden - Environnement de développement virtualisé</i></h1></td>
                        <td width="20%" align="right">&nbsp;</td>
                    </tr>
                </table>

                <p>&nbsp;</p>

                <table width="100%">
                    <tr>
                        <td align="center"><a href="https://www.telecom-sudparis.eu/"><img src="../images/tsp.png" ></a></td>
                    </tr>
                </table>

                <p>&nbsp;</p>

                <div align="center">
                    <hr style="height:2px; width:80%; border-width:0; color:blue; background-color:blue" />
                </div>

                <div align="center">
                    <table width="100%">
                        <tr>
                            <td width="20%">&nbsp;</td>
                            <td width="60%" align="center">Cassiopée 2024</td>
                            <td width="20%">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Core theme JS-->
<script src="js/scripts.js"></script>

</body>
</html>

