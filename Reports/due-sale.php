<?php 
require_once '../Includes/includes.php';
require_once '../Includes/authcheck.php';
$duecustomer = new credit_customer();
$duecustomerdata = $duecustomer->select_credit_customer();
if(isset($_GET["from_date"]) && isset($_GET["to_date"]))
{
    $duecustomerdata = $duecustomer->select_credit_customer(startDate:$_GET["from_date"],endDate:$_GET["to_date"]);
}
$from_date= isset($_GET["from_date"])? $_GET["from_date"]: date("Y-m-d");
$to_date= isset($_GET["to_date"])? $_GET["to_date"]: date("Y-m-d");
?>
<!doctype html>
<html lang="en">

<head>
  <?php 
  require_once '../View/head.php';
  require_once '../View/datatables.php';
  ?>
</head>
<body>
<!--  Body Wrapper -->
<div class="h-100vh">
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <?php 
    require_once '../View/sidebar.php';
    $feature_id=46;
    include '../Includes/viewPermission.php';
    if($userType==1 || $view==1)
    {
        
    }
    else
    {
        ?>
        <script>
            setInterval(function(){
                $(".dt-buttons").addClass("d-none");  
            }, 100);
            
        </script>
        <?php
    }
    ?>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
        <?php 
        require_once '../View/header.php';
        require_once "../View/modals/main-category.php";
         $shops = new Shop();
         $shop = $shops->getOneShop($shop_id);
        ?>
         <div class="container-fluid">
            <input type="hidden" name="" id="shop_name" value="<?=$shop[0]['ShopName']?>">
            <input type="hidden" name="" id="shop_address_one" value="<?=$shop[0]['AddressLineOne']?>">
            <input type="hidden" name="" id="shop_address_two" value="<?=$shop[0]['AddressLineTwo']?>">
            <input type="hidden" name="" id="shop_city" value="<?=$shop[0]['City']?>">  
            <input type="hidden" name="" id="shop_number" value="<?=$shop[0]['PhoneNumber']?>">
            <input type="hidden" name="" id="title" value="Customer Due - <?=$from_date?> to <?=$to_date?>">
            <h5 class="card-title fw-semibold mb-2" style="margin-top: 0px;">Customer Due</h5>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <form action="" class="form-inline mb-3" method="get" accept-charset="utf-8">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="" for="from_date">Start Date</label>
                                        <input type="date" name="from_date" class="form-control datepicker hasDatepicker" id="from_date" placeholder="Start Date" value="<?=$from_date?>" required>
                                    </div> 
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="" for="to_date">End Date</label>
                                        <input type="date" name="to_date" class="form-control datepicker hasDatepicker" id="to_date" placeholder="End Date" value="<?=$to_date?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <button type="submit" id="btn-filter" class="btn btn-success">Find</button>
                                </div>
                            </div>
                        </form>

                    <table class="table table-hover" id="tbl_category">
                    <thead>  
                        <tr>
                            <th>SL</th>
                            <th>Customer No</th>
                            <th>Customer Name</th>
                            <th>Customer Address</th>
                            <th>Customer Contact</th>
                            <th>Credit Amount</th>
                            <th>Customer Status</th>
                            <th>Total Credit</th>
                            <th>Total Debit</th>
                            <th>Total Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach ($duecustomerdata as $row) 
                        {
                            $due=$row['total_credit'] - $row ['total_debit'];
                            ?>
                            <tr>
                                <td> <?php echo $i;?> </td>
                                <td> <?php echo $row['CustomerNo'] ?></td>
                                <td> <?php echo $row['CustName'] ?></td>
                                <td> <?php echo $row['CustAddress'] ?></td>
                                <td> <?php echo $row['CustContact'] ?></td>
                                <td> <?php echo $row['MaxCreditAmount'] ?></td>
                                <td> <?php echo $row['CustStat'] ?></td>
                                <td><?php  echo $row['total_credit']?></td>
                                <td><?php  echo $row ['total_debit']?></td>
                                <td><?=number_format($due,2,'.')?></td>
                            </tr>
                            <?php 
                            $i++;
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
<?php require_once '../View/footer.php';?> 

    <script src="../Assets/jquery/due.js"></script>
    <!-- <script src="../Assets/libs/jquery/dist/jquery.min.js"></script> -->
    <script src="../Assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Assets/js/sidebarmenu.js"></script>
    <script src="../Assets/js/app.min.js"></script>
    <script src="../Assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../Assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="../Assets/js/dashboard.js"></script>
</body>
</html>
