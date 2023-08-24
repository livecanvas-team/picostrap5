/// Picosass.js ///
import * as sass from 'https://jspm.dev/sass'; //import SASS module

//console.log(sass.compileStringAsync(` .box {width: 10px + 15px;} `)); //just a quick example of compilation

const theScssSelector = '#the-scss'; //the selector for the element containing the SCSS code element

const replaceLast = (str, pattern, replacement) => {
    const match =
        typeof pattern === 'string'
            ? pattern
            : (str.match(new RegExp(pattern.source, 'g')) || []).slice(-1)[0];
    if (!match) return str;
    const last = str.lastIndexOf(match);
    return last !== -1
        ? `${str.slice(0, last)}${replacement}${str.slice(last + match.length)}`
        : str;
};

function canonicalize(url) {
    //console.log('canonicalize ' + url);

    //if it's not the main file, or the main bs file, add underscores in front of scss file names
    if (!url.endsWith("/main") && !url.endsWith("/bootstrap")) {
        url = replaceLast(url, '/', '/_');
    }

    //create URL object to be consumed by the compiler, adding .scss to filename
    const base = document.querySelector(theScssSelector).getAttribute("baseurl") ?? window.location.toString();
    return new URL(url + '.scss', base);
}


async function load(canonicalUrl) {
    //console.log(`Importing ${canonicalUrl} (async)`);
    document.querySelector("#picosass-output-feedback span").innerHTML = `Importing ${canonicalUrl}`;

    const response = await fetch(canonicalUrl)
    if (!response.ok) {
        document.querySelector("#picosass-output-feedback").innerHTML = `Error reading   SCSS file:  ${canonicalUrl}`;
        throw new Error(`Failed to fetch ${canonicalUrl}: ${response.status} (${response.statusText})`);
    }
    const contents = await response.text()
    return {
        contents,
        syntax: canonicalUrl.pathname.endsWith('.sass') ? 'indented' : 'scss'
    }
}

async function runScssCompiler(theCode, sassParams ) {

    //set default output
    if (!sassParams.style) sassParams.style = "compressed";

    //set default importers
    if (!sassParams.importers) sassParams.importers = [{ canonicalize, load }];

    //set default charset
    if (!sassParams.charset) sassParams.charset = false;

    return await sass.compileStringAsync(theCode, sassParams)
}

export function Compile(sassParams = {}) {

    //if not present, add a DIV and some styling TO SHOW COMPILER MESSAGES / OUTPUT FEEDBACK 
    if (!document.querySelector("#picosass-output-feedback")) document.querySelector("html").insertAdjacentHTML("afterbegin", `
        <div id='picosass-output-feedback'></div> 
        <style> 
            #picosass-output-feedback { position: fixed; top:5px; z-index: 99999999; width:80%;font-size:30px; background:#212337; color:lime; font-family:courier; border:12px solid red; padding:15px; display:block;   } 
            #picosass-output-feedback span{display:block; font-size:16px;}
            #picosass-output-feedback:empty {display:none}
        </style>
        `);
    
    //is a Compile process already running? if so, abort
    //TODO:: make it cleaner
    if(document.querySelector("#picosass-output-feedback").innerHTML!='') {
        console.log("PicoSASS task is already running, aborting");
        //wait a bit and retrigger
        return false;
    }

    //if no SCSS source element is on the page, show message: No SCSS element to compile...
    if (!document.querySelector(theScssSelector)) document.querySelector("#picosass-output-feedback").innerHTML = ` No SCSS element to compile... `;

    //if SCSS source element is empty, exit
    const theCode = document.querySelector(theScssSelector).innerHTML;
    if (theCode.trim() == '')  {
        console.log("Empty SCSS source, aborting");
        return false; 
    }

    //show the first feedback message: Compiling .... 
    document.querySelector("#picosass-output-feedback").innerHTML = ` Compiling SCSS... <span></span>`;
    console.log("Running Compiler...");

    //run the compiler
    runScssCompiler(theCode, sassParams)

        .then((compiled) => {
            console.log("SCSS compiled successfully.");
            console.log(compiled);

            //if not present, add a new CSS element
            if (!document.querySelector("#picosass-injected-style")) document.head.insertAdjacentHTML("beforeend", `<style id="picosass-injected-style"> </style>`);

            //populate the element with the new CSS
            document.querySelector('#picosass-injected-style').innerHTML = compiled.css;

            //remove initial static CSS 
            document.querySelector(".picostrap-provisional-css")?.setAttribute("disabled", "true");

            //as there are no errors, clear the output feedback
            document.querySelector("#picosass-output-feedback").innerHTML = '';
        })

        .catch((error) => {
            //show error in output feedback 
            document.querySelector("#picosass-output-feedback").innerHTML = error;
        })
}

//MAKE THE COMPILE FUNCTION  GLOBALLY AVAILABLE
//eg: window.Picosass.Compile();
//or: window.Picosass.Compile({style: "expanded"});

window.Picosass = {
    Compile: Compile,
    Run: runScssCompiler
}

/////////////////////////////// ON DOM CONTENT LOADED: COMPILE ONCE & OBSERVE CHANGES TO SOURCE SCSS //////////////
window.addEventListener("DOMContentLoaded", (event) => {

    //run  the compiler, unless a special class is added to the body
    if (!document.querySelector("body").classList.contains("prevent-sass-autocompile")) {
        Compile();
    } 

    /*
    //attach observer to detect on-page scss code changes  
    let observer = new MutationObserver(mutationRecords => {
        //console.log(mutationRecords); 
        Compile();
    });

    // observe everything except attributes
    observer.observe(document.querySelector(theScssSelector), {
        childList: true, // observe direct children
        subtree: true, // and lower descendants too
        characterDataOldValue: true // pass old data to callback
    });

    */

}); //end onDOMContentLoaded
