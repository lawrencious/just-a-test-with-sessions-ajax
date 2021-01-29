var firstName = undefined;
var lastName = undefined;


function logout()
{
  callAjaxLogOut();
}

function callAjaxLogOut()
{
  $.ajax
  (
    {
      url : "logout.php",
      dataType : "json",
      success : redirect,
      error : quit,
    }
  )
}


function redirect(json)
{
  window.location.href = json.href;
  $("errMsg").text(json.msg);
}


/**
 *  If an error occures, it prints the error message str
 **/
function errorMsg(str)
{
  $("#results").addClass("toHide");
  $("#errMsg").text(str);
  $("#errMsg").removeClass("toHide");
}


/**
 *  After a successfull AJAX call, it check whether
 *    some results have been found in database or not.
 *  If found, it prints them inside the HTML page;
 *  Otherwise, it prints an error message inside of it.
 **/
function showList(json)
{
  if(jQuery.isEmptyObject(json))
  {
    errorMsg("Could not find any match for \'"+firstName+" "+lastName+"\'.");
  }
  else
  {
    $("#firstN").text(firstName);
    $("#lastN").text(lastName);
    $("#list tr").not("#list tr:first").remove();
    json.movieList.forEach(
      function(item)
      {
        var newElem = $("<tr></tr>");
        var idData = $("<td></td>").text(item.id);
        var filmData = $("<td></td>").text(item.name);
        var yearData = $("<td></td>").text(item.year);
        newElem.append(idData, filmData, yearData);
        $("#list").append(newElem);
      }
    )
    firstName = undefined;
    lastName = undefined;
  }
}


/**
 *  Function invoked when AJAX call fails. 
 **/
function quit(err)
{
  alert(err.responseText);
}


/** 
 *  Function for defining the AJAX call.
 **/
function callAjax(input)
{
  $.ajax
  (
    {
      url : "getMovieList.php",
      type : "GET",
      data : input,
      dataType : "json",
      success : showList,
      error : quit,
    }
  );
}


/**
 *  It checks user input: it makes sure that
 *    neither the first name nor the second name
 *    contain digits or are empty.
 **/
function checkInput()
{
  $("#username").parent().addClass("toHide");
  firstName = $(this).prev().prev().val();
  lastName = $(this).prev().val();
  var regex = /\d/g;
  if(regex.test(firstName) || regex.test(lastName)  ||
     firstName.localeCompare("") == 0 || lastName.localeCompare("") == 0)
  {
    errorMsg("Name and last name must be non empty and with alphabetical characters only.");
  }
  else
  {
    var input = "firstname="+firstName+"&lastname="+lastName;
    if($(this).parent().parent().attr("id").localeCompare("searchall") == 0)
    {
      input += "&all=true";
    }
    else
    {
      input += "&all=false";
    }

    callAjax(input);

    $("#results").removeClass("toHide");
    $("#errMsg").addClass("toHide");
  }
}


function setColumns()
{
  var col1 = $("<col></col>");
  var col2 = $("<col></col>").addClass("onCentre");
  var col3 = $("<col></col>");
  var colG = $("<colgroup></colgroup>").append(col1, col2, col3);
  $("#list").prepend(colG);
}


function pageLoad()
{
  setColumns();
  $("#results").addClass("toHide");
  $("[value=go]").click(checkInput);
  $("#submitLogout").click(logout);
}

window.onload = pageLoad;