/* 
* @Author: mrhengbing
* @Date:   2016-04-22 23:41:13
* @Last Modified by:   mrhengbing
* @Last Modified time: 2016-04-23 21:40:38
*/

/**
 * 刷新验证码
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function changeVerify(obj){
    $("#verifyImg").attr("src",VerifyURL+'/'+Math.random());
    return false;
}


/**
 * 验证登陆表单
 */
function CheckForm()
{
    if($("#username").val() == "")
    {
        alert("请输入用户名！");
        $("#username").focus();
        return false;
    }
    if($("#password").val() == "")
    {
        alert("请输入密码！");
        $("#password").focus();
        return false;
    }
    if($("#code").val() == "")
    {
        alert("请输入验证码！");
        $("#code").focus();
        return false;
    }
}

$(function(){
    $(".loginForm input").keydown(function(){
        $(this).prev().hide();
    }).blur(function(){
        if($(this).val() == ""){
            $(this).prev().show();
        }
    });

    $("#username").focus(function(){
        $("#username").attr("class", "uname inputOn");
    }).blur(function(){
        $("#username").attr("class", "uname input");
    });

    $("#password").focus(function(){
        $("#password").attr("class", "pwd inputOn");
    }).blur(function(){
        $("#password").attr("class", "pwd input");
    });

    $("#username").focus();
});

