function clearForm()
{
    $("#status").prop("checked", true);
    $("#shopFeatureName").val("");
    $("#spfid").val("");
}
$(document).ready(function(){
    $("#btn_Add_ShopFeature_modal").click(function(){
        clearForm();
        $("#btn_Update_Feature").hide();
        $("#btn_save_Features").show();
        $(".modal-title").text(" Add Shop Features");
        $("#ShopFeature_modal").modal("toggle");
    });
    $(".delete").click(function(){
        var SFID = $(this).data("spfid");
        var FeatureName = $(this).closest("tr").find("td:eq(2)").text();
        // alert(SFID);
        var button = $(this);
        if(confirm(`Are you sure you want to delete ${FeatureName} ?`))
        {
            $.ajax({
                url: '../Ajax/ShopFeatures/deleteShopFeature.php',
                method: 'post',
                data: { SFID: SFID },
                dataType: 'json',
                success: function(response) {
                    if(response.success)
                    {

                        toastr.success(`${FeatureName} Deleted Successfully`);
                        button.closest("tr").remove();

                    }
                    else
                    {
                        console.log("Error:", response.message);
                        toastr.error(response.message, "Error");
                    }

                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText);
                    toastr.error("An error occurred while processing.", "Error");
                }
            });   
        }
        

    });
    $(".edit").click(function(){
        var SFID = $(this).data("spfid");
        // alert(SFID);
        clearForm();
        $.ajax({
            url: '../Ajax/ShopFeatures/getShopFeature.php',
            method: 'post',
            data: { SFID: SFID },
            dataType: 'json',
            success: function(response) {
                console.log("response: "+response.data);
                
                if (response.success && response.data.length > 0) 
                {
                    var data = response.data[0];
                    var SPFID = data["SPFID"];
                    var FeatureName = data["FeatureName"];
                    var SFstatus = data["SFstatus"];
                    $(".modal-title").text(` Edit Shop Features ${FeatureName}`);
                    
                    $("#shopFeatureName").val(FeatureName);
                    $("#spfid").val(SPFID);
                    if( SFstatus===1)
                    {
                        $("#status").prop("checked", true);
                    }
                    else
                    {
                        $("#status").prop("checked", false);
                    }
                    $("#btn_Update_Feature").show();
                    $("#btn_save_Features").hide();


                    $("#ShopFeature_modal").modal("toggle");
                    
                } else {
                    console.log("Error:", response.message);
                    toastr.error(response.message, "Error");
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", status, error);
                console.log("Response:", xhr.responseText);
                toastr.error("An error occurred while processing.", "Error");
            }
        });
    })
})