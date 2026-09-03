function validate()
{
    var valid=true
    $("#product_modal .required").each(function(){
        if($(this).val()=="")
        {
            valid =false
            $(this).parent().find("#alrt").css("display","block");
            
        }
    })
    return valid
}
function validate2()
{
    var valid=true
    $("#customer_modal .required").each(function(){
        if($(this).val()=="")
        {
            valid =false
            $(this).parent().find("#alrt").css("display","block");
            
        }
    })

    return valid
}
$(document).ready(function(){

    // $("#prescription input").each(function(){
    //     $(this).attr("disabled",true);
    // })
    // $("#prescription textarea").each(function(){
    //     $(this).attr("disabled",true);
    // })


    $(document).keydown(function (e) {
        if(e.keyCode == 27)
        {
            e.preventDefault();
            $("#customer_modal").modal("toggle"); 
            $("#cust_name").focus();
            // escape press
        }
        if (e.key === "F1") { 
            e.preventDefault();
            add_tr()
            // F1
        }
        if (e.key === "F2") { 
            e.preventDefault();
            $("#wholesale").trigger("click")
            // F2
        }
        if (e.key === "F3") { 
            e.preventDefault();
            $("#wholesales").trigger("click")
            // F3
        }
        if (e.key === "F6") { 
            e.preventDefault();          
            $("#product_modal").modal("toggle");
            $("#cmb_category").focus();
            $("#btn_update_product").remove();
            $("#btn_save_product").remove();
            $(".image").remove();
            $(".service").remove();
            $("#btn_save_product2").css('display', 'block');
            $(".text-alrt").css('display', 'inline-block');
            // F6 press
        }
    });
    $("#product_modal .required").keyup(function(){
        $(this).parent().find("#alrt").css("display","none");
    });
    $("#customer_modal .required").keyup(function(){
        $(this).parent().find("#alrt").css("display","none");
    });
    $("#add-customer").click(function(){
        $("#customer_modal").modal("toggle");
        $("#cust_name").focus();
    });
    $("body .add").click(function() {
        add_tr();
    });
    /* ---- 1) Bring PHP flags into JS once ---- */
    
    const FLAGS = {
    prescription: $("#prescriptionFlag").val(),
    hasBatch: $("#hasbatchNo").val(),
    hasSerial: $("#hasSerial").val()
    };

    /* ---- 2) Helpers ---- */
    function nextRowIndex() {
    const last = $('.tr').last().data('example');
    return (typeof last === 'number' ? last : parseInt(last, 10)) + 1 || 0;
    }

    function widthClass(base = '') {
    // When prescription, many inputs use compact widths
    return FLAGS.prescription==1 ? ` ${base}` : '';
    }

    function renderBatchCell(i) {
    // Always render a batch cell to preserve column count; hide when not needed
    const hidden = FLAGS.prescription==1 ? "style='display:none;'" : (FLAGS.hasBatch!=1 ? "style='display:none;'" : '');
    const selectStyle = FLAGS.prescription==1 ? "style='display:none;'" : "";
    return `
        <td ${hidden}>
        <select name="batch_id[]" required id="batch_id${i}" class="form-select" onchange="batch(${i})" disabled ${selectStyle}>
            <option value=""></option>
        </select>
        <input type="hidden" name="batch[]" id="batch${i}" class="form-control" readonly>
        </td>
    `;
    }

    function renderDescOrBatchCell(i) {
    if (FLAGS.prescription==1) {
        return `
        <td colspan="2">
            <input type="text" name="prodDes[]" id="prodDes${i}" class="form-control w300p" disabled>
            <select name="batch_id[]" required id="batch_id${i}" class="form-select" onchange="batch(${i})" style="display:none;" disabled>
            <option value=""></option>
            </select>
            <input type="hidden" name="batch[]" id="batch${i}" class="form-control" readonly>
        </td>
        `;
    }
    return renderBatchCell(i);
    }

    function renderSerialCell(i) {
    if (!FLAGS.hasSerial) return '';
    return `
        <td>
        <input type="text" name="serial[]" id="serial${i}" class="form-control text-end "readonly>
        </td>
    `;
    }

    /* ---- 3) Row builder ---- */
    function rowHTML(i) {
    const w90 = widthClass('w90p');
    const w150 = FLAGS.prescription==1 ? ' w150p' : '';

    return `
        <tr id="${i}" class="tr" data-example="${i}">
        <td>
            <span class="text-danger" id="alert${i}"></span>
            <select name="item_id[]" onchange="item_id(${i})" id="item${i}" class="item form-control">
            <option value=""></option>
            </select>
        </td>

        ${renderDescOrBatchCell(i)}
        ${renderSerialCell(i)}

        <td>
            <input type="text" name="avl_qty[]" id="avl_qty${i}" class="form-control text-end${w90}" required readonly>
        </td>

        <td>
            <span class="text-danger" id="alertqty${i}"></span>
            <input type="text" name="qty[]" id="qty${i}"
                onkeyup="ttotal(${i})" onkeydown="ttotal(${i})" onkeypress="ttotal(${i})"
                class="qty form-control text-end${w90}" required disabled>
        </td>

        <td>
            <span class="text-danger" id="alertrate${i}"></span>
            <input type="text" name="rate[]" id="rate${i}"
                class="rate form-control text-end${w90}"
                onkeyup="ttotal(${i})" onkeydown="ttotal(${i})" onkeypress="ttotal(${i})"
                required disabled>
            <input type="hidden" name="original_rate[]" id="original_rate${i}" class="form-control" readonly>
            <input type="hidden" name="cost_rate[]" id="cost_rate${i}" class="form-control" readonly>
        </td>

        <td>
            <select name="discountType[]" id="discountType${i}" onchange="ttotal(${i})"
                    class="discountType form-select${w90}" disabled>
            <option value="1">Percentage</option>
            <option value="2">Flat Amount</option>
            </select>
        </td>

        <td>
            <input type="text" name="discount[]" id="discount${i}"
                class="discount form-control text-end${w90}"
                onkeyup="ttotal(${i})" onkeydown="ttotal(${i})" onkeypress="ttotal(${i})"
                value="0.00" disabled>
        </td>

        <td>
            <input type="text" name="totals[]" id="total${i}"
                class="total form-control text-end${w150}" required readonly>
            <input type="hidden" name="original_total[]" id="original_total${i}" class="form-control" readonly>
        </td>

        <td>
            <div class="d-flex gap-2">
            <a href="javascript:void(0)" class="btn btn-primary p-2 js-add-row"><i class="ti ti-plus"></i></a>
            <a href="javascript:void(0)" class="btn btn-danger p-2 js-remove-row"><i class="ti ti-trash"></i></a>
            </div>
        </td>
        </tr>
    `;
    }

    /* ---- 4) Public API ---- */
    function add_tr() {    
        const i = ( $('.tr').length ? nextRowIndex() : 0 );
        $("#tbody").append(rowHTML(i));
        initRowSelects(i);
    }

    /* ---- 5) Init Select2 etc. for a row ---- */
    function initRowSelects(i) {
    $("#item"+i).select2({
        ajax: {
        url: '../AJAX/WholeSaleInvoice/getItems.php',
        dataType: 'json',
        delay: 250,
        data: params => ({ search: params.term, type: 'item_search' }),
        processResults: data => ({ results: data })
        },
        cache: true,
        placeholder: 'Search for Items',
        minimumInputLength: 1,
        width: '100%',
    });

    }

    /* ---- 6) Delegated events (works for future rows) ---- */
    $(document)
    .on('click', '.js-add-row', function () {
        add_tr();
    })
    .on('click', '.js-remove-row', function () {
        $(this).closest('tr').remove();
        if (typeof grandTotal === 'function') grandTotal();
    });

  
    $("#btn_save_product2").click(function(){
        var cmb_category=$("#cmb_category").val();
        var cmb_subcategory=$("#cmb_subcategory").val();
        var prod_name=$("#prod_name").val();
        var barcode=$("#barcode").val();
        var second_name=$("#second_name").val();
        var prod_description=$("#prod_description").val();
        var prod_purchase_price=$("#prod_purchase_price").val();
        var prod_selling_price=$("#prod_selling_price").val();
        var prod_carton_qty=$("#prod_carton_qty").val();
        var cmb_purchase_unit=$("#cmb_purchase_unit").val();
        var conversion_rate=$("#conversion_rate").val();
        var cmb_selling_unit=$("#cmb_selling_unit").val();
        if(validate()==true)
        {
            $.ajax({
                url:'../Controller/productController.php',
                    method:'post',
                    data:{
                        cmb_category:cmb_category,
                        cmb_subcategory:cmb_subcategory,
                        prod_name:prod_name,
                        barcode:barcode,
                        second_name:second_name,
                        prod_description:prod_description,
                        prod_purchase_price:prod_purchase_price,
                        prod_selling_price:prod_selling_price,
                        prod_carton_qty:prod_carton_qty,
                        cmb_purchase_unit:cmb_purchase_unit,
                        conversion_rate:conversion_rate,
                        cmb_selling_unit:cmb_selling_unit,
                        btn_save_product:1
                        },
                    success:function(response)
                    {
                        $("#cmb_category").val("");
                        $("#cmb_subcategory").val("");
                        $("#prod_name").val("");
                        $("#barcode").val("");
                        $("#second_name").val("");
                        $("#prod_description").val("");
                        $("#prod_purchase_price").val("");
                        $("#prod_selling_price").val("");
                        $("#prod_carton_qty").val("");
                        $("#conversion_rate").val(1);  
                        $("#product_modal").modal("hide")                  
                    }
                });
        }
        else
        {
            alert("Please Fill The Required Fields");
        }
        
    });
    $("#add-prescriptions").click(function(){
        $("#prescription_modal").modal("toggle");
    });
    $("#btn_save_customer").click(function(){
        var cust_name=$("#cust_name").val();
        var cust_contact=$("#cust_contact").val();
        var cust_address=$("#cust_address").val();
        var cust_dob=$("#cust_dob").val();
        var gender=$("#gender").val();
        if(validate2()==true)
        {
            $.ajax({
                url:'../Controller/CustomerController.php',
                    method:'post',
                    data:{
                        CusName:cust_name,
                        Contact:cust_contact,
                        Address:cust_address,
                        DOB:cust_dob,
                        gender:gender,
                        CustomerStat:1,
                        WS_btn_save_customer:1
                        },
                    success:function(response)
                    {
                        $("#print").html(response)
                        
                        $("#cust_name").val("");
                        $("#cust_contact").val("");
                        $("#cust_address").val(""); 
                        $("#customer_modal").modal("hide")                  
                    }
                });
        }
        else
        {
            alert("Please Fill The Required Fields");
        }
        
    })
    $("#cmb_category").change(function(){
        var category_id = $(this).val();
        $.get("../AJAX/AjaxCategory/getSubcategory.php", {
            category_id: category_id
        }, function(data){
            $("#cmb_subcategory").html(data);
            $("#cmb_subcategory").focus();
        });//get subcategory
    });//cmb changed
    // const BREAKPOINT = 900;
    $("#cmb_customer").select2({
        ajax:{
            url: '../AJAX/WholeSaleInvoice/getCustomers.php',
            dataType: 'json',
            delay: 250,
            data: function(params){
                
                var query = {
                    search: params.term,
                    type: 'item_search'
                };
                return query;
            },
            processResults: function(data){
                return {
                    results: data
                }
            }
        },
        cache: true,
        placeholder: 'Search for Customer',
        minimumInputLength: 1,
        width: '70%',
    });//get customer search

    //cmb changed Added by chinthana 2024-10-22
    
    var prescriptionFlag=$("#prescriptionFlag").val();
    if(prescriptionFlag==1)
    {
        $("#cmb_customer").change(function(){
            var customer_id = $(this).val();
            $.get("../AJAX/WholeSaleInvoice/getPrescription.php", {
                customer_id: customer_id
            }, function(data){
                // alert(data);
                var obj = JSON.parse(data);
                let cmb_data = "";
                    cmb_data += "<option value='0'>=== Select Prescription ===</option>";
                $.each(obj, function (key, value) {
                    var pres_date = value['date'].substring(0, 10);

                    cmb_data += "<option value='"+value['PRHID']+"'>";
                    cmb_data += pres_date + " : " + value['pr_subjective_ref'];
                    cmb_data += "</option>";
                });

                $("#cmb_prescription").html(cmb_data);
            });

        });//customer changed
    }
    

    $("#cmb_salesman").select2({
        ajax:{
            url: '../AJAX/WholeSaleInvoice/getSalesman.php',
            dataType: 'json',
            delay: 250,
            data: function(params){
                
                var query = {
                    search: params.term,
                    type: 'item_search'
                };
                return query;
            },
            processResults: function(data){
                return {
                    results: data
                }
            }
        },
        cache: true,
        placeholder: 'Search for Salesman',
        minimumInputLength: 1,
        width: '70%',
    });//get customer search\
    $(".select2").addClass("form-control");
    $(".select2").css("border","1px solid #DFE5EF");
    
    $("#return_no").select2({
        ajax:{
            url: '../AJAX/WholeSaleInvoice/returnsales.php',
            dataType: 'json',
            delay: 250,
            data: function(params){
                var query = {
                    search: params.term,
                    type: 'item_search'
                };
                return query;
            },
            processResults: function(data){
                return {
                    results: data
                }
            }
        },
        cache: true,
        placeholder: 'Return No',
        minimumInputLength: 1,
        width: '100%',
    });//get customer search\

//========================== Prescription ==========================//
//added by Chinthana 2024-10-21
// $("#btn_save_prescription").click(function(){
//     var customer_id = $("#customer_id").val();

//     if(customer_id != null && customer_id != 1)
//     {
//         var allow=true;
//         $("#prescription input").each(function(){
//             if($(this).val()!="")
//             {
//                 allow=true;
//                 return false;
//             }
//             else
//             {
//                 allow=false
//             }
//         });
//         $("#prescription textarea").each(function(){
//             if($(this).val()!="")
//             {
//                 allow=true;
//                 return false;
//             }
//             else
//             {
//                 allow=false
//             }
//         });
//         if(allow==true)
//         {
//             var subjective_ref = $("#subjective-ref").val();

//             var f_right_sph = $("#f-right-sph").val();
//             var f_right_cyl = $("#f-right-cyl").val();
//             var f_right_axis = $("#f-right-axis").val();
//             var f_right_none = $("#f-right-none").val();

//             var f_left_sph = $("#f-left-sph").val();
//             var f_left_cyl = $("#f-left-cyl").val();
//             var f_left_axis = $("#f-left-axis").val();
//             var f_left_none = $("#f-left-none").val();

//             var add_f_right_add_cyl = $("#add-f-right-add-cyl").val();  
//             var add_f_right_add_axis = $("#add-f-right-add-axis").val();  
//             var add_f_right_add_none = $("#add-f-right-add-none").val();  
//             var add_f_left_add_cyl = $("#add-f-left-add-cyl").val();  

//             var prs_hb = $("#prs-hb").val();
//             var add_f_left_add_axis = $("#add-f-left-add-axis").val();
//             var add_f_left_add_none = $("#add-f-left-add-none").val();
//             var pres_remarks = $("#pres-remarks").val();

//             var s_right_sph = $("#s-right-sph").val();
//             var s_right_cyl = $("#s-right-cyl").val();
//             var s_right_axis = $("#s-right-axis").val();
//             var s_left_sph = $("#s-left-sph").val();
//             var s_left_cyl = $("#s-left-cyl").val();
//             var s_left_axis = $("#s-left-axis").val();

//             var add_s_right_cyl = $("#add-s-right-cyl").val();
//             var add_s_right_axis = $("#add-s-right-axis").val();
//             var add_s_left_cyl = $("#add-s-left-cyl").val();
//             var add_s_left_axis = $("#add-s-left-axis").val();

//             var prs_refraction = $("#prs-refraction").val();
//             var r_va_uva = $("#r-va-uva").val();
//             var r_va_ph = $("#r-va-ph").val();
//             var l_va_uva = $("#l-va-uva").val();
//             var l_va_ph = $("#l-va-ph").val();
//             var vision_acuity = $("#vision-acuity").val();

//             $.get("../AJAX/WholeSaleInvoice/setPrescription.php", {
//                 customer_id: customer_id,
//                 subjective_ref: subjective_ref,
//                 f_right_sph: f_right_sph,
//                 f_right_cyl: f_right_cyl,
//                 f_right_axis: f_right_axis,
//                 f_right_none: f_right_none,
//                 f_left_sph: f_left_sph,
//                 f_left_cyl: f_left_cyl,
//                 f_left_axis: f_left_axis,
//                 f_left_none: f_left_none,
//                 add_f_right_add_cyl: add_f_right_add_cyl,
//                 add_f_right_add_axis: add_f_right_add_axis,
//                 add_f_right_add_none: add_f_right_add_none,
//                 add_f_left_add_cyl: add_f_left_add_cyl,
//                 prs_hb: prs_hb, 
//                 add_f_left_add_axis: add_f_left_add_axis,
//                 add_f_left_add_none: add_f_left_add_none,
//                 pres_remarks: pres_remarks,
//                 s_right_sph: s_right_sph,
//                 s_right_cyl: s_right_cyl,
//                 s_right_axis: s_right_axis,
//                 s_left_sph: s_left_sph,
//                 s_left_cyl: s_left_cyl,
//                 s_left_axis: s_left_axis,
//                 add_s_right_cyl: add_s_right_cyl,
//                 add_s_right_axis: add_s_right_axis,
//                 add_s_left_cyl: add_s_left_cyl,
//                 add_s_left_axis: add_s_left_axis,
//                 prs_refraction: prs_refraction,
//                 r_va_uva: r_va_uva,
//                 r_va_ph: r_va_ph,
//                 l_va_uva: l_va_uva,
//                 l_va_ph: l_va_ph,
//                 vision_acuity: vision_acuity
//             }, function(data){
//                 alert(data);

//                 $("#prescription input").each(function(){
//                     $(this).val("");
//                 });
//                 $("#prescription textarea").each(function(){
//                     $(this).val("");
//                 });
//                 $("#prescription_modal").modal("hide")
//             });//set prescription
//         }
//         else
//         {
//             alert("Please fill atleast one field!");
//         }
        

//     }//has customer
//     else
//     {
//         alert("Please select a customer!");
//         $("#prescription_modal").modal('hide');
//         $("#cmb_customer").focus();
//     }//no customer
// });//save prescription
    
});