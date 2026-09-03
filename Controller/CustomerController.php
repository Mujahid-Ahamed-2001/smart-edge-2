<?php 
include "../Includes/includes.php";

if(isset($_GET['condition']))
{
    $condition = $_GET['condition'];
    $response = [];
    switch ($condition) {
        case 'new':
            $cusObj = new Customer(); 
            $commonObj = new Common();   
            $customer_name = isset($_POST['customer_name']) && !empty($_POST['customer_name']) ? $_POST['customer_name'] : "";
            $country_code = isset($_POST['country_code']) && !empty($_POST['country_code']) ? $_POST['country_code'] : "";
            $cust_contact = isset($_POST['cust_contact']) && !empty($_POST['cust_contact']) ? $_POST['cust_contact'] : "";
            $customer_email = isset($_POST['customer_email']) && !empty($_POST['customer_email']) ? $_POST['customer_email'] : "";
            $customer_address = isset($_POST['customer_address']) && !empty($_POST['customer_address']) ? $_POST['customer_address'] : "";
            $customer_dob = isset($_POST['customer_dob']) && !empty($_POST['customer_dob']) ? $_POST['customer_dob'] : "";
            $customer_credit_limit = isset($_POST['customer_credit_limit']) && !empty($_POST['customer_credit_limit']) ? $_POST['customer_credit_limit'] : "75000";
            $customer_gender = isset($_POST['customer_gender']) ? $_POST['customer_gender'] : 1;
            $customer_status = isset($_POST['customer_status']) ? $_POST['customer_status'] : 0;
            $shop_id = isset($_SESSION['shop_id']) ? $_SESSION['shop_id'] : "1";
            if(empty($customer_name) || empty($country_code) || empty($cust_contact))
            {
                $response = [
                    "status" => 0,
                    "message" => "Please provide all required fields: customer name, country code, and contact."
                ];
                echo json_encode($response);
                exit;
            }                        
            $customer = $cusObj->getCustomerCount();
            if(!empty($customer))
            {
                $Cus_count = floatval($customer[0]['CusCount']);
                $Cus_count += 1;
            }
            else
            {
                $Cus_count = 1;
            }//not max coun
            $CustomerNo = $commonObj->createCount("CU", $Cus_count);  
            $result = $cusObj->setCustomer(CustomerNo: $CustomerNo, CustName: $customer_name, CustAddress: $customer_address,CustContact: $cust_contact, MaxCreditAmount: $customer_credit_limit, PaymentTerm: 60, CustStat: $customer_status, shop_SHID: $shop_id, DOB: $customer_dob, gender: $customer_gender, country_code: $country_code, email: $customer_email);
            if($result)
            {
                $customerDetail = $cusObj->getlastCustomer();
                $response = [
                    "status" => 1,
                    "message" => "Customer created successfully.",
                    "customer" => $customerDetail
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Failed to create customer."
                ];
            }
            echo json_encode($response);
            break;
        case 'edit':
            $CTID = isset($_GET['CTID']) && !empty($_GET['CTID']) ? $_GET['CTID'] : "";
            if($CTID == 1) {
                $response = [
                    "status" => 0,
                    "message" => "Cannot update common customer."
                ];
                echo json_encode($response);
                exit;
            }
            $cusObj = new Customer(); 
            $commonObj = new Common();   
            $customer_name = isset($_POST['customer_name']) && !empty($_POST['customer_name']) ? $_POST['customer_name'] : "";
            $country_code = isset($_POST['country_code']) && !empty($_POST['country_code']) ? $_POST['country_code'] : "";
            $cust_contact = isset($_POST['cust_contact']) && !empty($_POST['cust_contact']) ? $_POST['cust_contact'] : "";
            $customer_email = isset($_POST['customer_email']) && !empty($_POST['customer_email']) ? $_POST['customer_email'] : "";
            $customer_address = isset($_POST['customer_address']) && !empty($_POST['customer_address']) ? $_POST['customer_address'] : "";
            $customer_dob = isset($_POST['customer_dob']) && !empty($_POST['customer_dob']) ? $_POST['customer_dob'] : "";
            $customer_credit_limit = isset($_POST['customer_credit_limit']) && !empty($_POST['customer_credit_limit']) ? $_POST['customer_credit_limit'] : "75000";
            $customer_gender = isset($_POST['customer_gender']) ? $_POST['customer_gender'] : 1;
            $customer_status = isset($_POST['customer_status']) ? $_POST['customer_status'] : 0;
            $shop_id = isset($_SESSION['shop_id']) ? $_SESSION['shop_id'] : "1";
            if(empty($CTID))
            {
                $response = [
                    "status" => 0,
                    "message" => "Customer ID is required for editing."
                ];
                echo json_encode($response);
                exit;
            }
            if(empty($customer_name) || empty($country_code) || empty($cust_contact))
            {
                $response = [
                    "status" => 0,
                    "message" => "Please provide all required fields: customer name, country code, and contact."
                ];
                echo json_encode($response);
                exit;
            }   

            $result = $cusObj->updateCustomer(CustName: $customer_name, cust_gender: $customer_gender, cust_dob: $customer_dob, CustAddress: $customer_address, CustContact: $cust_contact, MaxCreditAmount: $customer_credit_limit, PaymentTerm: 60, CustStat: $customer_status, CusID: $CTID, country_code: $country_code, CustEmail: $customer_email);
            if($result)
            {
                $customerDetail = $cusObj->getCustomerById($CTID);
                $response = [
                    "status" => 1,
                    "message" => "Customer updated successfully.",
                    "customer" => $customerDetail
                ];
            }
            else
            {
                $response = [
                    "status" => 0,
                    "message" => "Failed to update customer."
                ];
            }
            echo json_encode($response);
            break;
        default:
            # code...
            break;
    }
}

if(isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'create_ajax':
            $CustName = isset($_POST['cust_name']) ? $_POST['cust_name'] : "";
            $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : "+94";
            $CustContact = isset($_POST['cust_contact']) ? $_POST['cust_contact'] : "";
            $CustAddress = isset($_POST['cust_address']) ? $_POST['cust_address'] : "";
            $CustDOB = isset($_POST['cust_dob']) ? $_POST['cust_dob'] : "";
            $Custgender = isset($_POST['gender']) ? $_POST['gender'] : 1;
            $shop_id = isset($_SESSION['shop_id']) ? $_SESSION['shop_id'] : "1";
            $CustStat = 1;
            $MaxCreditAmount = empty($_POST['CreditAmount']) ? 75000 : $_POST['CreditAmount']; 
            if(!empty($CustName) && !empty($CustContact)) 
            {
                $cusObj = new Customer();                            
                $customer = $cusObj->getCustomerCount();
                if(!empty($customer))
                {
                    $Cus_count = floatval($customer[0]['CusCount']);
                    $Cus_count += 1;
                }
                else
                {
                    $Cus_count = 1;
                }//not max count

                $commonObj = new Common();                                
                $CustomerNo = $commonObj->createCount("CU", $Cus_count);  
                
                try 
                {
                    $response = $cusObj->setCustomer($CustomerNo,$CustName,$CustAddress,$CustContact,$MaxCreditAmount, 60, $CustStat,$shop_id,$CustDOB,$Custgender,$country_code);
                    if($response) 
                    {
                        $customerDetail = $cusObj->getlastCustomer();
                        $response = array(
                            "status" => "success",
                            "message" => "Customer created successfully.",
                            "customerDetail" => $customerDetail[0],
                        );
                    } 
                    else 
                    {
                        $response = array(
                            "status" => "error",
                            "message" => "Failed to add customer."
                        );
                    }
                    echo json_encode($response);
                }//try
                catch(Exception $e)
                {
                    // Handle any errors from setCustomer function
                    $response = array(
                        "status" => "error",
                        "message" => "An error occurred while adding the customer: " . $e->getMessage()
                    );
                    echo json_encode($response);
                    exit();
                }//catch
            }//has values
            else
            {
                // Handle empty fields
                $response = array(
                    "status" => "error",
                    "message" => "Error: insufficient data. Customer name and contact are required."
                );
                echo json_encode($response);
                exit();
            }//empty input values

            break;
        case 'create':
            include "customer.php";
            break;
        default:
            // Handle unknown action
            echo "Unknown action: " . htmlspecialchars($action);
            break;
    }
}
if(isset($_POST['btn_save_customer']))
{          
    $CustomerNo = "";
    $CustName = $_POST['CusName'];
    $CustContact = $_POST['Contact'];
    $Custgender = $_POST['gender'];
    $shop_id = $_SESSION['shop_id'];

    $CustAddress = isset($_POST['Address']) ? $_POST['Address'] : "";
    $CustDOB = isset($_POST['DOB']) ? $_POST['DOB'] : "";
    $PaymentTerm = isset($_POST['payment_term']) ? $_POST['payment_term'] : 0;
    //add default for payment term
    $PaymentTerm = empty($_POST['payment_term']) ? 60 : $_POST['payment_term'];
    // Validate checkbox
    $CustStat = 0;
    if(isset($_POST['CustomerStat'])) 
    {
        $CustStat = 1;
    }//Active customer 

    //default credit amount, should change later
    $MaxCreditAmount = empty($_POST['CreditAmount']) ? 75000 : $_POST['CreditAmount']; 

    // Validate and sanitize input data (optional)
    $CustName = htmlspecialchars($CustName);
    $CustContact = htmlspecialchars($CustContact);
    $MaxCreditAmount = htmlspecialchars($MaxCreditAmount);
    $CustAddress = htmlspecialchars($CustAddress);

    //get the Customer no                         
    $cusObj = new Customer();                            
    $customer = $cusObj->getCustomerCount();
    if(!empty($customer))
    {
        $Cus_count = floatval($customer[0]['CusCount']);
        $Cus_count += 1;
    }
    else
    {
        $Cus_count = 1;
    }//not max count

    $commonObj = new Common();                                
    $CustomerNo = $commonObj->createCount("CU", $Cus_count);  

    // Validate inputs
    if(!empty($CustName) && !empty($CustContact)) 
    {
        if(is_numeric($MaxCreditAmount))
        {
            // Error handling for setCustomer function
            try 
            {
                $cusObj->setCustomer($CustomerNo,$CustName,$CustAddress,$CustContact,$MaxCreditAmount, $PaymentTerm, $CustStat,$shop_id,$CustDOB,$Custgender);
                $_SESSION['customer_update'] = 2;
                header("Location: ../Public/Customerlist.php");
            }//try
            catch(Exception $e)
            {
                // Handle any errors from setCustomer function
                echo "An error occurred while adding the customer: " . $e->getMessage();
                exit();
            }//catch
        }//velid number
        else
        {
            // Handle empty fields
            $_SESSION['customer_update'] = 1;
            header("Location: ../Public/Customerlist.php");
            die("Error: not valid number.");
        }//not valid number

    }//has values
    else
    {
        // Handle empty fields
        $_SESSION['customer_update'] = 0;
        header("Location: ../Public/Customerlist.php");
        die("Error: insufficint data.");
    }//empty input values

}//save new customer
elseif(isset($_POST['WS_btn_save_customer']))
{          
    $CustomerNo = "";
    $CustName = $_POST['CusName'];
    $CustContact = $_POST['Contact'];
    $Custgender = $_POST['gender'];
    $shop_id = $_SESSION['shop_id'];

    $CustAddress = isset($_POST['Address']) ? $_POST['Address'] : "";
    $CustDOB = isset($_POST['DOB']) ? $_POST['DOB'] : "";
    $PaymentTerm = isset($_POST['payment_term']) ? $_POST['payment_term'] : 0;
    //add default for payment term
    $PaymentTerm = empty($_POST['payment_term']) ? 60 : $_POST['payment_term'];
    // Validate checkbox
    $CustStat = 0;
    if(isset($_POST['CustomerStat'])) 
    {
        $CustStat = 1;
    }//Active customer 

    //default credit amount, should change later
    $MaxCreditAmount = empty($_POST['CreditAmount']) ? 75000 : $_POST['CreditAmount']; 

    // Validate and sanitize input data (optional)
    $CustName = htmlspecialchars($CustName);
    $CustContact = htmlspecialchars($CustContact);
    $MaxCreditAmount = htmlspecialchars($MaxCreditAmount);
    $CustAddress = htmlspecialchars($CustAddress);

    //get the Customer no                         
    $cusObj = new Customer();                            
    $customer = $cusObj->getCustomerCount();
    if(!empty($customer))
    {
        $Cus_count = floatval($customer[0]['CusCount']);
        $Cus_count += 1;
    }
    else
    {
        $Cus_count = 1;
    }//not max count

    $commonObj = new Common();                                
    $CustomerNo = $commonObj->createCount("CU", $Cus_count);  

    // Validate inputs
    if(!empty($CustName) && !empty($CustContact)) 
    {
        if(is_numeric($MaxCreditAmount))
        {
            // Error handling for setCustomer function
            try 
            {
                $cusObj->setCustomer($CustomerNo,$CustName,$CustAddress,$CustContact,$MaxCreditAmount, $PaymentTerm, $CustStat,$shop_id,$CustDOB,$Custgender);
                $_SESSION['customer_update'] = 2;
                $custID=$cusObj->getCustomerCount();
                $string=$CustName." - ".$CustContact;
                ?>
                <script>
                if (typeof setPredefinedSupplier === "function") {
                    setPredefinedSupplier("<?=$custID[0]["CusCount"]?>","<?=$string?>");
                }
                // if (typeof setPredefinedCustomer === "function") {
                //     setPredefinedCustomer("<?=$custID[0]["CusCount"]?>","<?=$string?>");
                // }
                </script>
                <?php

            }//try
            catch(Exception $e)
            {
                // Handle any errors from setCustomer function
                echo "An error occurred while adding the customer: " . $e->getMessage();
                exit();
            }//catch
        }//velid number
        else
        {
            // Handle empty fields
            $_SESSION['customer_update'] = 1;
            die("Error: not valid number.");
        }//not valid number

    }//has values
    else
    {
        // Handle empty fields
        $_SESSION['customer_update'] = 0;
        die("Error: insufficint data.");
    }//empty input values

}//save new customer

if(isset($_POST['btn_update_customer']))
{    
    $customer_id = $_POST['hide_customer_id'];
    $CustomerNo = "";
    $CustName = $_POST['CusName'];
    $CustContact = $_POST['Contact'];  
    $shop_id = $_SESSION['shop_id'];

    $Custgender = isset($_POST['gender']) ? $_POST['gender'] : "1";
    $CustDOB = isset($_POST['DOB']) ? $_POST['DOB'] : "";
    $CustAddress = isset($_POST['Address']) ? $_POST['Address'] : "";
    //add default for payment term
    $PaymentTerm = empty($_POST['payment_term']) ? 60 : $_POST['payment_term'];

    // Customer stat
    $CustStat = isset($_POST['CustomerStat']) ? 1 : 0;

    //default credit amount, should change later
    $MaxCreditAmount = empty($_POST['CreditAmount']) ? 75000 : $_POST['CreditAmount']; 

    // Validate and sanitize input data (optional)
    $CustName = htmlspecialchars($CustName);
    $CustContact = htmlspecialchars($CustContact);
    $MaxCreditAmount = htmlspecialchars($MaxCreditAmount);
    $CustAddress = htmlspecialchars($CustAddress);

    //customer object                        
    $cusObj = new Customer();  

    // Validate inputs
    if(!empty($CustName) && !empty($CustContact)) 
    {
        if(is_numeric($MaxCreditAmount))
        {
            // Error handling for setCustomer function
            try 
            {
                //$cusObj->setCustomer($CustomerNo,$CustName,$CustAddress,$CustContact,$MaxCreditAmount,$CustStat,$shop_id);
                $cusObj->updateCustomer($CustName, $Custgender, $CustDOB, $CustAddress, $CustContact, $MaxCreditAmount, $PaymentTerm, $CustStat, $customer_id);
                $_SESSION['customer_update'] = 3;
                header("Location: ../Public/Customerlist.php");
            }//try update
            catch(Exception $e)
            {
                // Handle any errors from setCustomer function
                echo "An error occurred while adding the customer: " . $e->getMessage();
                exit();
            }//catch
        }//velid number
        else
        {
            // Handle empty fields
            $_SESSION['customer_update'] = 1;
            header("Location: ../Public/Customerlist.php");
            die("Error: not valid number.");
        }//not valid number

    }//has values
    else
    {
        // Handle empty fields
        $_SESSION['customer_update'] = 0;
        header("Location: ../Public/Customerlist.php");
        die("Error: insufficint data.");
    }//empty input values
}//update Supplier

if(isset($_POST['btn_delete_customer']))
{
    $customer_id = $_POST['hide_customer_id'];

    $dbObj = new DBTransactions();
    //check invoice
    $sql = "SELECT count(IHID) as invoice_count FROM `invoiceheader` WHERE customers_CTID = ".$customer_id.";";
    $invData = $dbObj->getData($sql);
    $invoice_count = floatval($invData[0]['invoice_count']);

    $has_invoice = $invoice_count > 0 ? 1 : 0;

    //check credit
    $sql_1 = "SELECT count(PRHID) as prescription_count FROM `prescriptionheader` WHERE customer_CTID = ".$customer_id.";";
    $presData = $dbObj->getData($sql_1);
    $prescription_count = floatval($presData[0]['prescription_count']);

    $has_prescription = $prescription_count > 0 ? 1 : 0;

    // echo "has invoice - " . $has_invoice . "<br>";
    // echo "has pres - " . $has_prescription . "<br>";

    if($has_invoice || $has_prescription)
    {
        //cannot delete customer
        $_SESSION['customer_update'] = 4;
        header("Location: ../Public/Customerlist.php");
        die("Error: constraint violation.");
    }  
    else
    {
        //can delete customer
        $custObj = new Customer();
        $custObj->deleteCustomer($customer_id);

        $_SESSION['customer_update'] = 5;
        header("Location: ../Public/Customerlist.php");
    }//can delet customer

}//delete customer