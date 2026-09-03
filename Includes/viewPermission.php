<?php 
if($userType==0)
{
    $userObj=new User();
    $checkview=$userObj->userAcces($userRole_id,$feature_id);
    $create=$checkview[0]["is_create"];
    $view=$checkview[0]["is_view"];
    $edit=$checkview[0]["is_edit"];
    $delete=$checkview[0]["is_delete"];
    $verify=$checkview[0]["is_verify"];
    $print=$checkview[0]["is_print"];
    if($view==1)
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
}
else
{
    $create=1;
    $view=1;
    $edit=1;
    $delete=1;
    $verify=1;
    $print=1;
}
?>
<?php $today = date('d-m-Y');?>