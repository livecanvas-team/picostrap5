/* this script is here only for site admins, to handle SASS autocompile */

if (picostrap_ajax_obj.disable_livereload != '1')  console.warn("Since you're logged in as administrator, picostrap will be checking every four seconds for SASS folder changes via an AJAX request. If this is bothering you or your server, you can disable SCSS Autocompile / LiveReload in the Customizer / Global Utilities panel.");

//set frequency of check
var picostrap_livereload_timeout=4000;

function picostrap_livereload_woodpecker(){
    //console.log("picostrap_livereload_woodpecker start");

    //check if browser tab has focus
    if (document.visibilityState !== 'visible') {
        //schedule for later
        setTimeout(function () { picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
        return;
    }

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
            //console.log('picostrap_check_for_sass_changes returns: '+response);

            if (response.includes('<NO>')) {
                //no sass change has been detected
                //console.log("No sass change has been detected");
                if (picostrap_ajax_obj.disable_livereload != '1') setTimeout(function () { picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
                return;
            }
            if (response.includes('<YES>')) {
                //sass change has been detected
                //console.log("Sass change has been detected");
                picostrap_recompile_sass();
                return;
            }

            //if didnt exit yet...it's an error
            console.log("Invalid ajax response during picostrap_check_for_sass_changes fetch. Maybe you're unlogged? Response: " + response);
            
            //do more..
        })
        .catch(err => {
            console.log("Error during picostrap_check_for_sass_changes fetch. Error: " + err);
            if (picostrap_ajax_obj.disable_livereload != '1') setTimeout(function () { picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout*2);
        });

} //end function



function picostrap_recompile_sass(){
    console.log("Picostrap detected changes in SASS folder. Recompiling...");

    //write message: compiling SASS....
    if (document.querySelector("#scss-compiler-output")) document.querySelector("#scss-compiler-output").innerHTML = " Compiling SCSS. Please wait... ";
    
    //build the request
    const formdata = new FormData();
    formdata.append("nonce", picostrap_ajax_obj.nonce);
    formdata.append("action", "picostrap_recompile_sass"); 
    fetch(picostrap_ajax_obj.ajax_url, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Cache-Control": "no-cache",
        },
        body: formdata
    }).then(response => response.text())
        .then(response => { 
            console.log(response.replace(/(<([^>]+)>)/gi, "").replace('generatedView','generated. '));
             
            if (response.includes("<compiler-success>")) {
                //SUCCESS

                //as there are no errors, clear the output feedback
                document.querySelector("#scss-compiler-output").innerHTML = ''; 
                
                //un-cache the frontend css
                url = document.getElementById('picostrap-styles-css').href;
                document.getElementById('picostrap-styles-css').href = url;

                //retrigger the woodpecker
                setTimeout(function(){ picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout/4);
            }
            else {
                //COMPILE ERRORS
                document.querySelector("#scss-compiler-output").innerHTML = response; //display errors
                setTimeout(function(){ picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout/2);
            }
            
        }).catch(function(err) {
            console.log("picostrap_recompile_sass Error. Details: " + err);
        }); 
} //end function

//END FUNCTIONS ////

//ADD DIV TO SHOW COMPILER MESSAGES / FEEDBACK
var theStyle = `
    <style>
        #scss-compiler-output { 
            position: fixed; 
            z-index: 99999999;
            font-size:30px;
            background:#212337;
            color:lime;
            font-family:courier;
            border:8px solid red;
            padding:15px;
            display:block;
            user-select: none;
        }
        #scss-compiler-output:empty {display:none}
    </style>

`; 
document.querySelector("html").insertAdjacentHTML("afterbegin", "<div id='scss-compiler-output'></div>" + theStyle);            

//ON DOMContentLoaded: START THE ENGINE / Like document ready :)
document.addEventListener('DOMContentLoaded', function(event) {          
 
    //check if main stylesheet was loaded fine
    if (document.querySelector("#picostrap-styles-css").sheet === null) picostrap_recompile_sass();

    //trigger the woodpecker
    picostrap_livereload_woodpecker();
});
