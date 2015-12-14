<?php

include_once '../includes/connection.php';
include_once '../includes/functions.php';
// include_once '../pages/index.php';

// $results = get_all_identifications($dbh);
$header = get_incident($_GET['id'],$dbh);
$detail = get_incident_history($_GET['id'],$dbh);
$statuses = get_statuses($dbh);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Routerboard Resources</title>

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="../dist/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="../bower_components/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- DataTables CSS -->
    <link href="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Belhar HighSite Admin Area</a>
            </div>
            <!-- /.navbar-header -->
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="login.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        <li>                   
                            <a href="index.php"><i class="fa fa-dashboard fa-fw"></i>Highsite Information Centre</a>
                        </li>
                    <li>
                        <a href="#"><i class="fa fa-files-o fa-fw"></i> Pages<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="login.html">Login Page</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Highsite Issue Manager</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            
            <div class="row">



                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i> Incident Information
                    </div>
                
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-2 col-md-2 col-lg-2">
                                <strong>Subject:</strong><br />
                                <strong>Status:</strong><br />
                                <strong>Date Created:</strong><br />
                            </div>
                            <div class="col-xs-10 col-md-10 col-lg-10">
                                
                                <?php echo $header[0]['subject']; ?><br />
                                <?php echo $header[0]['status']; ?><br />
                                <?php echo $header[0]['date_created']; ?><br />
                            </div>
                        </div>

                        <hr />

                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#add-communication">Add Communication</a>
                                    </h4>
                                </div>
                                <div id="add-communication" class="panel-collapse collapse collapse">
                                    <div class="panel-body">
                                                    
                                        <div class="row">
                                            <div class="col-lg-12">
                                                    <form role="form" action="incident_logger.php?id=<?php echo $_GET['id'] ?>" method="POST">

                                                        <input type="hidden" name="logged_in" value="<?php echo $_COOKIE['id']; ?>">
                                                        <input type="hidden" name="new" value="0">
                                                        <input type="hidden" name="subject" value="<?php echo $header[0]['subject']; ?>">

                                                        <?php
                                                            for ($i=0; $i < count($detail); $i++) {  
                                                                $last_cc = $detail[$i]['cc_recipient'];
                                                            }
                                                        ?>
                                                        
                                                          <div class="form-group">
                                                            <label for="email">CC Recipients</label>
                                                            <input type="text" class="form-control" name="cc_recipient" value="<?php echo $last_cc; ?>" placeholder="CC Recipients" >
                                                          </div>

                                                          <?php
                                                            if ($_COOKIE['id'] == 0) {
                                                                echo '  <div class="form-group">';
                                                                echo '    <label for="status">Status</label>';
                                                                echo '    <select class="form-control" name="status">';
                                                                for ($i=0; $i < count($statuses); $i++) {
                                                                    $selected = ($statuses[$i]['status'] == $header[0]['status'] ? 'selected' : '');
                                                                    echo "<option ".$selected." value='".$statuses[$i]['id']."'>".$statuses[$i]['status']."</option>";
                                                                }
                                                                echo '    </select>';
                                                                echo '  </div>';
                                                            }
                                                            else {
                                                                echo '<input type="hidden" name="status" value="'.$header[0]['status'].'">';
                                                            }
                                                          ?>

                                                          <div class="form-group">
                                                                <label for="body">Description</label>
                                                                <textarea name="body" class="form-control" rows="6" placeholder="Describe your incident" ></textarea>
                                                          </div>

                                                        <div id="email"></div>
                                                          <button type="submit" class="btn btn-default">Submit</button>
                                                        </form>                         
                                                <!-- /.table-responsive -->
                                            </div>
                                            <!-- /.col-lg-12 (nested) -->
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .panel-body -->
                </div>


                <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-clock-o fa-fw"></i> History
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <ul class="timeline">

                            
                            <?php

                                for ($i=0; $i < count($detail); $i++) { 

                                    $class = ($i % 2 == 0 ? "" : "class='timeline-inverted'");

                                    switch ($detail[$i]['status']) {
                                        case 'Open':
                                            $icon = 'fa-folder-open-o';
                                            $colour = 'danger';
                                            break;
                                        case 'Pending':
                                            $icon = 'fa-folder-open-o';
                                            $colour = 'warning';
                                            break;
                                        case 'Solved':
                                            $icon = 'fa-folder-open-o';
                                            $colour = 'success';
                                            break;
                                    }

                                    echo '<li '.$class.'>';
                                    echo '    <div class="timeline-badge '.$colour.'"><i class="fa '.$icon.'"></i>';
                                    echo '    </div>';
                                    echo '    <div class="timeline-panel">';
                                    echo '        <div class="timeline-heading">';
                                    echo '            <h4 class="timeline-title">'.$header[0]['subject'].'</h4>';
                                    echo '            <p><small class="text-muted"><i class="fa fa-clock-o"></i>'.$detail[$i]['status'].'</small>';
                                    echo '            </p>';
                                    echo '        </div>';
                                    echo '        <div class="timeline-body">';
                                    echo '            <p>'.$detail[$i]['body'].'</p>';
                                    echo '        </div>';
                                    echo '    </div>';
                                    echo '</li>';
                                }

                            ?>

                        </ul>
                    </div>
                    <!-- /.panel-body -->
                </div>




            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="../bower_components/raphael/raphael-min.js"></script>
    <script src="../bower_components/morrisjs/morris.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
    $(document).ready(function() {
        $('#identity_table').DataTable({
                responsive: true
        });
    });

    function change(value) {
    	// document.getElementById('email').innerHTML = value.value
    }
    </script>

</body>

</html>
