/* this script is here only for site admins, to handle SASS autocompile */

console.warn("Since you're logged in as administrator, picostrap is checking every four seconds for SASS folder changes via an AJAX request. If this is bothering you or your server, you can disable SCSS Autocompile / LiveReload in the Customizer / Global Utilities panel.");

//set frequency of check
var picostrap_livereload_timeout=4000;

function picostrap_livereload_woodpecker(){
    //console.log("picostrap_livereload_woodpecker start");

    //build the request
    const formdata = new FormData();
    formdata.append("nonce", picostrap_ajax_obj.nonce);
    formdata.append("action", "picostrap_check_for_sass_changes");
    fetch(picostrap_ajax_obj.ajax_url, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Cache-Control": "no-cache",
        },
        body: formdata
    }).then(response => response.text())
        .then(response => {
            //console.log(response);

            if (response === "N") {
                //no sass change has been detected
                //console.log("No sass change has been detected");
                setTimeout(function () { picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
            }
            if (response === "Y") {
                //sass change has been detected
                //console.log("Sass change has been detected");
                picostrap_recompile_sass();
            }

        })
        .catch(err => {
            alert("Form submit error. Details: " + err);
        });

} //end function



function picostrap_recompile_sass(){
    console.log("picostrap_recompile_sass start");

    //write message: compiling SASS....
    if (document.querySelector("#scss-compiler-output")) document.querySelector("#scss-compiler-output").innerHTML = "<div style='font-size:30px;background:#212337;color:lime;font-family:courier;border:8px solid red;padding:15px;display:block;user-select: none;'>Compiling SCSS....</div>";
    
    //build the request
    const formdata = new FormData();
    formdata.append("nonce", picostrap_ajax_obj.nonce);
    formdata.append("action", "picostrap_recompile_sass");
    formdata.append("ps_compiler_api","1");
    fetch(picostrap_ajax_obj.ajax_url, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Cache-Control": "no-cache",
        },
        body: formdata
    }).then(response => response.text())
        .then(response => {
            //console.log(response);
             
            if (response.includes("New CSS bundle")) {
                //SUCCESS

                //as there are no errors, clear the output feedback
                document.querySelector("#scss-compiler-output").innerHTML = ''; 
                
                //un-cache the frontend css
                url = document.getElementById('picostrap-styles-css').href;
                document.getElementById('picostrap-styles-css').href = url;

                //retrigger the woodpecker
                setTimeout(function(){ picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
            }
            else {
                //COMPILE ERRORS
                document.querySelector("#scss-compiler-output").innerHTML = response; //display errors
                setTimeout(function(){ picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
            }
            
        }).catch(function(err) {
            console.log("picostrap_recompile_sass Fetch Error");
        }); 
} //end function

//END FUNCTIONS ////

//ADD DIV TO SHOW COMPILER MESSAGES / FEEDBACK
document.querySelector("html").insertAdjacentHTML("afterbegin","<div id='scss-compiler-output' style=' position: fixed; z-index: 99999999;'></div>");            

//IF CSS BUNDLE FILE DOES NOT LOAD SUCCESSFULLY, IT MAY NOT EXIST: REBUILD
document.querySelector("#picostrap-styles-css").onerror = function(){
    console.log("CSS bundle does not exist, recompiling");
    picostrap_recompile_sass();  
}

//ON DOMContentLoaded START THE ENGINE / Like document ready :)
document.addEventListener('DOMContentLoaded', function(event) {          
    
    //trigger the woodpecker
    picostrap_livereload_woodpecker();
});
