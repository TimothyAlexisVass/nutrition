<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
$(document).ready(function(){
//Autocomplete search using PHP, MySQLi, Ajax and jQuery
    $('#foodItem').keyup(function(e){
        e.preventDefault();
        var form = $('#inputForm').serialize();
        $.ajax({
            type: 'POST',
            url: 'foodItemSearch.php',
            data: form,
            dataType: 'json',
            success: function(response){
                if(response.error || response.data == '') {
                    $('#foodItemsList').hide();
                    $('#foodItemAdd').prop('disabled', true);
                }
                else {
                    $('#foodItemsList').show().html(response.data);
                }
            }
        });
    });

    $(document).on('click', '.autocomplete-listitem', function(e){
        e.preventDefault();
        $('#foodItemsList').hide();
        var foodItem = $(this).data('fooditem');
        $('#foodItem').val(foodItem);
        $('#foodItemAdd').prop('disabled', false);
    });
});
function validateForm() {
    var foodItem = document.forms["inputForm"]["foodItem"].value;
    var amount = document.forms["inputForm"]["amount"].value;
    var weight = document.forms["inputForm"]["weight"].value;
    var age = document.forms["inputForm"]["age"].value;
    if (age == "" || age == 0) {
        swal("Please", "Enter your age.", "warning");
        return false;
    }
    else if (weight == "" || weight == 0) {
        swal("Please", "Enter your weight.", "warning");
        return false;
    }
    else if (amount == "" || amount == 0) {
        swal("Please", "Enter amount.", "warning");
        return false;
    }
    else if (foodItem == "") {
        swal("Please", "Select a food item.", "warning");
        return false;
    }
};
</script>
<form name="inputForm" id="inputForm" action="index.php" method="post" autocomplete="off" onsubmit="return validateForm()">
<table width="100%" style="font-size: 22px; text-shadow: none;"><tbody>
    <tr>
        <th colspan="2">
            <select style="font-size: 20px; text-align: center" name="gender">
                <option value="1.25" <?php if($bodyInfo[gender] == "1.25") { echo "selected"; }?>>Male</option>
                <option value="1" <?php if($bodyInfo[gender] == "1") { echo "selected"; }?>>Female</option>
            </select>
        </th>
        <th colspan="2">
            Activity: <select style="font-size: 20px; text-align: center" name="activityLevel">
                <option value="1" <?php if($bodyInfo[activityLevel] == 1) { echo "selected"; }?>>Sedentary</option>
                <option value="1.12" <?php if($bodyInfo[activityLevel] == 1.12) { echo "selected"; }?>>Active</option>
                <option value="1.2" <?php if($bodyInfo[activityLevel] == 1.2) { echo "selected"; }?>>Lively</option>
                <option value="1.28" <?php if($bodyInfo[activityLevel] == 1.28) { echo "selected"; }?>>Vibrant</option>
            </select>
        </th>
    </tr>
    <tr>
        <td colspan="2">
            <input type="text" style="width: 40%; text-align: right;" name="weight" value="<?php echo $bodyInfo[weight];?>" /><span style="width: 40%; display: inline-block; text-align: left;">&nbsp;kg</span>
        </td>
        <td colspan="2">
            <input type="text" style="width: 40%; text-align: right;" name="age" value="<?php echo $bodyInfo[age];?>" /><span style="width: 40%; display: inline-block; text-align: left;">&nbsp;years</span>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="position:relative">
            <input type="text" name="foodItem" id="foodItem" placeholder="Add food item" />
            <ul hidden id="foodItemsList" class="autoselect-list"><ul>
        </td>
        <td width="120px">
            <input type="text" style="width: 60%; text-align: right;" name="amount" value="100" /><span style="width: 20%; display: inline-block; text-align: left;">&nbsp;g</span>
        </td>
    </tr>
</tbody></table>
<center>
    <input disabled id="foodItemAdd" type="submit" name="add" value="ADD" />
</center>
</form>