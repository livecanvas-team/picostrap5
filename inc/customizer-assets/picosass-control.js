//this file integrates picoSASS and picostrap

import * as picosass from './picosass.js';

//set frequency of check
const picoRecompileInterval = 6000;

function picoWoodpecker() {
    //console.log("picoWoodpecker start");

    //check if browser tab has focus
    if (document.visibilityState == 'visible') {

        //run the compiler
        const compiled = picosass.Compile({  /* style: "compressed" */ });
    }

    //re-schedule for later
    setTimeout(function () { picoWoodpecker(); }, picoRecompileInterval);

} //end function


// DISABLE THE LIBRARY'S AUTOCOMPILE 
document.querySelector("body").classList.add("prevent-sass-autocompile");

////// ON DOM CONTENT LOADED //////////////
window.addEventListener("DOMContentLoaded", (event) => {

    //picoWoodpecker(); //turn on livereload if desired

    //attach listener so we will be programmatically able to launch compiling
    //just calling: document.querySelector('#picosass-output-feedback').click();
    /*
    document.addEventListener('click', function (event) {

        // If the clicked element doesn't have the right selector, bail
        if (!event.target.matches('#picosass-output-feedback')) return;

        // Don't follow the link
        //event.preventDefault();

        //compile
        const compiled = picosass.Compile({   });

    }, false);
    */


});




