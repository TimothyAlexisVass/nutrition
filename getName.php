<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
function validateForm() {
    var name = document.forms["inputForm"]["name"].value;
    if (name == "") {
        swal("Please", "Enter your name.", "warning");
        return false;
    }
};
</script>
<form name="inputForm" id="inputForm" action="index.php" method="post" autocomplete="off" onsubmit="return validateForm()">
<table width="100%" style="font-size: 22px; text-shadow: none;"><tbody>
    <tr>
        <th style="border-top-left-radius: 8px">
            Name
        </th>
    </tr>
    <tr>
        <td>
            <input type="text" name="name" />
        </td>
    </tr>
</tbody></table>
<center>
    <input id="setName" type="submit" name="setName" value="OK" />
</center>
</form>