/**
 * Created by Thai Duc on 19-Feb-18.
 */

$('div.alert-success').delay(2000).slideUp();

function confirm_delete() {
    if(!window.confirm("Bạn có thực sự muốn xoá thành viên này ?")){
        return false;
    }else{
        return true;
    }
}