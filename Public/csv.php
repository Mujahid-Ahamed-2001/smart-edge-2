<?php 
$name="products_sample";
$filename = $name.".csv";
$filename_url = "../Assets/csv/".$filename;
header("Content-Type: text/csv; charset=UTF-16LE");
if(file_exists($filename_url))
{
    header("Content-Disposition: attachment;filename=$filename");
    // echo file_get_contents($filename_url);
    readfile($filename_url);
}

exit()
?>