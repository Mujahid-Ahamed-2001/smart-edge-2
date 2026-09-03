<?php 
include '../Includes/includes.php';
include '../Includes/authcheck.php';
if($userObj->checkusertype($_SESSION["user_id"])==1)
{

}
else
{
    ?>
    <script>
        window.location.href = "../Public/home.php";
    </script>
    <?php
}

?> 

<!doctype html>
<html lang="en">

<head>
  <?php 
  include '../View/head.php';
  // include '../View/loader.php';
  ?>
</head>

<body data-sidebartype="mini-sidebar">
<div id="modal"></div>
<!--  Body Wrapper -->
    <div class="h-100vh">
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
            data-sidebar-position="fixed" data-header-position="fixed">
            <!-- Sidebar Start -->
            <?php 
            include '../View/sidebar.php';
            ?>
            <!--  Sidebar End -->
            <!--  Main wrapper -->
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php 
                include '../View/header.php';
                ?>
                <!--  Header End -->
                <div class="container-fluid h-100">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title fw-semibold mb-2 color_white" style="margin-top: 0px;">Company <a href="javascript:void(0)" id="refresh"><i class="ti ti-reload"></i></a></h5>    
                        </div>
                        <div class="col-md-6 d-flex justify-content-end">
                            <a href="../View/modals/company-modal.php?condition=new" class="btn btn-primary open-modal">Add Company</a>
                        </div>
                    </div> 
                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tbl_company">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Logo</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Version</th>
                                                <th>Licence exp date</th>
                                                <th>Status</th>
                                                <th>No. of Shops</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>                        
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  Body Wrapper End -->
        <!-- footer Start  -->
        <?php include '../View/footer.php';?> 
        <!-- footer End  -->
        <script src="../Assets/jquery/company2.js"></script>
    </div>
</body>
</html>