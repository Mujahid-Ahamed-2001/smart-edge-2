<?php
session_start();

include "../../Includes/config.php";
include "../../Model/DB_Class.php";

$dbObj = new DBTransactions();
$where = "WHERE 1=1";
if (
    (isset($_GET["CMID"]) && !empty($_GET["CMID"])) ||
    (isset($_POST["CMID"]) && !empty($_POST["CMID"]))
) {

    $CMID = isset($_GET["CMID"])
        ? (int)$_GET["CMID"]
        : (int)$_POST["CMID"];

    $where .= " AND c.CMID = $CMID";
}
$sql = "SELECT 
        c.*,
        ct.*,
        COUNT(s.SHID) AS SHOPCOUNT
    FROM company c
    LEFT JOIN companytype ct ON ct.CTID = c.CompanyType_CTID
    LEFT JOIN shop s ON s.Company_CMID = c.CMID
    $where
    GROUP BY c.CMID
    ORDER BY c.CMID DESC
";

$data = $dbObj->getData($sql);

$response = [];
$sl = 1;

foreach ($data as $row) {
    $logoFile = trim($row['ComLogo']);

    $logoPath = "../../Assets/Images/Company_Logos/" . $logoFile;
    $logoUrl  = "../Assets/Images/Company_Logos/" . $logoFile;

    if (!empty($logoFile) && file_exists($logoPath))
    {
        $ComLogo = $logoUrl;
    }
    else
    {
        $ComLogo = "../Assets/Images/SystemLogo/Smart_edge_logo_3.png";
    }

    $response[] = [
        "no"                    => $sl++,
        "CMD"                   => $row["CMID"],
        "CompanyLocation"       => $row["CompanyLocation"],
        "LicenceNo"             => $row["LicenceNo"],
        "SHOPCOUNT"             => (int)$row["SHOPCOUNT"],
        "logo"                  => $ComLogo,
        "name"                  => $row["ComName"],
        "type"                  => $row["CompanyTypeName"] ?? "",
        "version"               => $row["VersionNo"],
        "is_multicategory"      => $row["is_multicategory"],
        "is_commonStock"        => $row["is_commonStock"],
        "ComStartDate"          => $row["ComStartDate"],
        "expiry"                => $row["ComExpireDate"],
        "status"                => $row["ComStat"],
        "CTID"                  => $row["CTID"]
    ];
}

echo json_encode($response);