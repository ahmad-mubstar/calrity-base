var install = function(){

    var rawId      = $(this).attr("id");
    var extName    = $(this).val();
    var extVersion = document.getElementById(extName).value;

    document.getElementById('loading').style.display = 'block';

    $.ajax({url:"action.php",type : "GET",
        data: { ext: extName, version: extVersion, action:"install" },
        dataType: 'json',
        success:function(result)
        {

            document.getElementById('loading').style.display = 'none';
            $("#result").empty();
            $("#result").html(result[0]);

            if(result[1] != "null") {
                $("#countInstalled").fadeOut(300);
                $("#countInstalled").html(result[1]);
                document.getElementById("countInstalled").style.color = "green";
                $("#countInstalled").fadeIn(1000);

                $("#countAvailable").fadeOut(300);
                $("#countAvailable").html(result[2]);
                document.getElementById("countAvailable").style.color = "blue";
                $("#countAvailable").fadeIn(1000);

                $("#" + rawId).hide(500);
            }
        },
        error: function()
        {
            alert('error!');
        }
    });
}

var remove = function(){

    var rawId   = $(this).attr("id");
    var extName = $(this).val();

    document.getElementById('loading').style.display = 'block';

    $.ajax({url:"action.php",type : "GET",
        data: { ext: extName, action:"delete" },
        dataType: 'json',
        success:function(result)
        {
            document.getElementById('loading').style.display = 'none';

            $("#result").empty();
            $("#result").html(result[0]);

            if(result[1] != "null") {

                $("#countInstalled").fadeOut(300);
                $("#countInstalled").html(result[1]);
                document.getElementById("countInstalled").style.color = "green";
                $("#countInstalled").fadeIn(1000);

                $("#countAvailable").fadeOut(300);
                $("#countAvailable").html(result[2]);
                document.getElementById("countAvailable").style.color = "blue";
                $("#countAvailable").fadeIn(1000);

                $("#"+rawId).hide(500);
            }
        },
        error: function()
        {
            alert('error!');
        }
    });
}

var update = function (){

    var extName = $(this).val();

    document.getElementById('loading').style.display = 'block';

    $.ajax({url:"action.php",type : "GET",
        data: { ext: extName, action:"update" },
        dataType: 'json',
        success:function(result)
        {
            $("#result").empty();
            $("#result").html(result[0]);
            document.getElementById('loading').style.display = 'none';
        },
        error: function()
        {
            alert('error!');
        }
    });
}

var activeSymolnik = function () {

    $.ajax({url:"action.php",type : "GET",
        data: { action:"allowSymlink" },
        dataType: 'html',
        success:function(result)
        {
            $("#result").empty();
            $("#result").append(result);
        },
        error: function()
        {
            alert('error!');
        }
    });
}

var reinstall = function () {

    var extName    = $(this).val();
    var extVersion = document.getElementById(extName).value;
    document.getElementById('loading').style.display = 'block';

    $.ajax({url:"action.php",type : "GET",
        data: { ext: extName, action:"delete" },
        dataType: 'json',
        success:function(result)
        {
            $("#result").html(result[0]);

            if(result[1] != "null") {
                $.ajax({url:"action.php",type : "GET",
                    data: { ext: extName, version: extVersion, action:"install" },
                    dataType: 'json',
                    success:function(result)
                    {
                        $("#result").html(result[0]);
                        document.getElementById('loading').style.display = 'none';
                    },
                    error: function()
                    {
                        alert('error!');
                    }
                });
            }
        },
        error: function()
        {
            alert('error!');
        }
    });
}


$(document).ready(function(){
    $(".install").click(install),
    $(".delete").click(remove),
    $(".update").click(update),
    $(".symlinks").click(activeSymolnik),
    $(".reinstall").click(reinstall);
});