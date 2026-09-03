<?php 
include "../Includes/includes.php";
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

<!DOCTYPE html>
<html lang="en">

<head>
    <?php 
 include '../View/head.php';
 // include '../View/loader.php';

  ?>
  <style>
    .btn-close 
    {
        background-color: #fff;
    }
    /* Success toast background */
    #toast-container > .toast-success {
        background-color: #28a745 !important; /* Green (you can change this) */
        color: #fff;
    }

    /* Error toast background */
    #toast-container > .toast-error {
        background-color: #dc3545 !important; /* Red (you can change this) */
        color: #fff;
    }

    /* Optional: Info and Warning styles too */
    #toast-container > .toast-info {
        background-color: #17a2b8 !important;
        color: #fff;
    }

    #toast-container > .toast-warning {
        background-color: #ffc107 !important;
        color: #000;
    }

  </style>
</head>

<body>

<?php 
    include '../View/modals/shopFeatures.php';
    if(isset($_SESSION["Error"]))
    {
        ?>
        <script>
                $(document).ready(function(){
                    setTimeout(function(){
                        $("#toast").fadeOut();
                    },5000)
                })
            </script>
        <?php
        $message="";
        $message2="";
        if($_SESSION["Error"]==0)
        {
            $message="Missing Info";
            $message2="Please fill all the required fields";
            
        }
        elseif($_SESSION["Error"]==1)
        {
            $message="Oops! Somthing went wrong";
            $message2="Please try again.";
        }
        ?>
            <div class="toast toast-onload align-items-center text-bg-danger border-0 fade  show" role="alert" aria-live="assertive" aria-atomic="true" id="toast" style="position:fixed; top:70px; right:60px; z-index:999;">
                <div class="toast-body hstack align-items-start gap-6">
                    <img  src="../Assets/Images/SystemLogo/Smart_edge_logo_3.png" alt="" class="w-10">
                    <div>
                        <h5 class="text-white fs-3 mb-1"><strong><?=$message?> </strong></h5>
                        <h6 class="text-white  mb-0"><?=$message2?></h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white fs-5 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php
        unset($_SESSION["Error"]);
    }
    if(isset($_SESSION["success"]))
    {
        ?>
        <script>
                $(document).ready(function(){
                    setTimeout(function(){
                        $("#toast").fadeOut();
                    },5000)
                })
            </script>
        <?php
        $message="";
        $message2="";
        if($_SESSION["success"]==1)
        {
            $message="Success";
            $message2="Shop feature inserted successfully";
        }
        ?>
            <div class="toast toast-onload align-items-center text-bg-success border-0 fade  show" role="alert" aria-live="assertive" aria-atomic="true" id="toast" style="position:fixed; top:70px; right:60px; z-index:999;">
                <div class="toast-body hstack align-items-start gap-6">
                    <img  src="../Assets/Images/SystemLogo/Smart_edge_logo_3.png" alt="" class="w-10">
                    <div>
                        <h5 class="text-white fs-3 mb-1"><strong><?=$message?></strong></h5>
                        <h6 class="text-white  mb-0"><?=$message2?></h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white fs-5 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php
        unset($_SESSION["success"]);
    }
    if(isset($_SESSION["Usuccess"]))
    {
        ?>
        <script>
                $(document).ready(function(){
                    setTimeout(function(){
                        $("#toast").fadeOut();
                    },5000)
                })
            </script>
        <?php
        $message="";
        $message2="";
        if($_SESSION["Usuccess"]==1)
        {
            $message="Success";
            $message2="Shop feature updated successfully";
        }
        ?>
            <div class="toast toast-onload align-items-center text-bg-success border-0 fade  show" role="alert" aria-live="assertive" aria-atomic="true" id="toast" style="position:fixed; top:70px; right:60px; z-index:999;">
                <div class="toast-body hstack align-items-start gap-6">
                    <img  src="../Assets/Images/SystemLogo/Smart_edge_logo_3.png" alt="" class="w-10">
                    <div>
                        <h5 class="text-white fs-3 mb-1"><strong><?=$message?></strong></h5>
                        <h6 class="text-white  mb-0"><?=$message2?></h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white fs-5 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php
        unset($_SESSION["Usuccess"]);
    }
?>


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
    
                <div class="container-fluid">
                    <h5 class="card-title fw-semibold mb-4">Shop Features List</h5>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary rounded-pill ml-1 mb-2" id="btn_Add_ShopFeature_modal" data-bs-dismiss="modal">Add New Feature</button>    
                    </div>                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                      <table class="table search-table align-middle text-nowrap" id="tbl_active_store">
                                        <thead class="header-item">  
                                            <tr>
                                                <th>No</th>
                                                <th>ID</th>
                                                <th>Feature Name</th>   
                                                <th>Status</th> 
                                                <th>Action</th> 

                                            </tr>                                        
                                        </thead>
                                        <tbody>
                                            <?php
                                                $ModuleObj = new sysModels();
                                                $ModuleName = $ModuleObj->getShopFeatures(); 
                                                $count = count($ModuleName);
                                                if($count > 0)
                                                {
                                                    $i=1;
                                                    foreach ($ModuleName as $Module): ?>   
                                                        <tr>
                                                            <td><?php echo $i;?></td>
                                                            <td><?php echo $Module['SPFID'];?></td>
                                                            <td><?php echo $Module['FeatureName'];?></td>
                                                            <td>
                                                                <?php 
                                                                if($Module["SFstatus"]==1)
                                                                {
                                                                    ?>
                                                                    <span class="badge bg-success">Active</span>
                                                                    <?php
                                                                }
                                                                else
                                                                {
                                                                    ?>
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <button data-spfid="<?=$Module["SPFID"]?>" class="edit btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Edit Shop Feature">
                                                                    <i class="ti ti-edit"></i>
                                                                </button>    
                                                                <button data-spfid="<?=$Module["SPFID"]?>" data-toggle="tooltip" data-placement="bottom" title="Delete Shop Feature" class="delete btn btn-danger">
                                                                    <i class="ti ti-trash-x"></i>
                                                                </button> 
                                                            </td>    
                                                        </tr>                                         
                                                    <?php 
                                                    $i++;
                                                    endforeach;
                                                }
                                                else
                                                {
                                                    ?>
                                                    <tr>
                                                        <td colspan="4">
                                                            <span class="w-100 text-danger text-center d-inline-block">
                                                                No Results Found
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                 ?>
                                        </tbody>
                                      </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer Start  -->
    <?php include '../View/footer.php';?> 
    <!-- footer End  -->
    <script src="../Assets/jquery/ShopFeature.js"></script>                                          
</body>

</html>


