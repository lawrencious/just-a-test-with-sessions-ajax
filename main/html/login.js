function callAjaxLogIn(input)
{
  $.ajax
  (
    {
      url : "access.php",
      type : "POST",
      data : input,
      dataType : "json",
      success : redirect,
      error : quit,
    }
  );
}


/**
 *  Checks if the strings the user wrote in
 *  the form are valid
 **/
function checkInput()
{
  $("#errMsg").text("Session expired: please log back in to proceed.");
  username = $("[name=name]").val();
  password = $("[name=password]").val();
  if(username.localeCompare("") == 0 || password.localeCompare("") == 0)
  {
    $("#errMsg").text("Username and password cannot be empty strings.");
    $("#errMsg").removeClass("toHide");
  }
  else
  {
    $("#errMsg").text("");
    $("#errMsg").addClass("toHide");
    input = $("#login").serialize();
    callAjaxLogIn(input);
  }
}

/**
 *  Redirects to page specified in
 *  json.href attribute
 **/
function redirect(json)
{
  window.location.href = json.href;
}


/**
 *  Function invoked when AJAX call fails. 
 **/
function quit(err)
{
  alert(err.responseText);
}


function pageLoad()
{
  $("#submitLogin").click(checkInput);
}

window.onload = pageLoad;