/////// IMPORT SASS ////////

import * as sass from 'sass';

/// FUNCTIONS ///
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

async function load(canonicalUrl) {
    //console.log(`SASS: Importing ${canonicalUrl} (async)`);

    //determine if we should force reading a locally-cached version of the file,  or force the download of a fresh copy of the requested scss file
    const fetchDataArgs = canonicalUrl.pathname.includes('cache') ? {
        cache: 'force-cache',
        headers: {
            'Cache-Control': 'max-age=3600',
            'Pragma': 'max-age=3600', // added for redundancy
        }
    } : { cache: "no-cache" }

    //fetch the URL and handle stuff
    const response = await fetch(canonicalUrl, fetchDataArgs)
    if (!response.ok) throw new Error(`Failed to fetch ${canonicalUrl}: ${response.status} (${response.statusText})`);
    const contents = await response.text()
    return {
        contents,
        syntax: canonicalUrl.pathname.endsWith('.sass') ? 'indented' : 'scss'
    }
}

function canonicalize(url) {

    //console.log('canonicalize '+url);

    //if it's not the main file, or the main bs file, add underscores in front of scss file names
    if (!url.endsWith("/main") && !url.endsWith("/bootstrap")) {
        url = replaceLast(url, '/', '/_');
    }

    //create URL object to be consumed by the compiler
    return new URL(url + ".scss", window.location.toString())
}

export async function Compile(sassParams = {  /* style: "compressed" */ }) {

    //show feedback message: we are compiling ....
    document.querySelector("#picosass-output-feedback").innerHTML = " Compiling SCSS... ";

    //set default importers
    if (!sassParams.importers) sassParams.importers = [{ canonicalize, load }];

    //get the sass code to be compiled: if no source element, alert
    if (!document.querySelector("#the-scss")) document.querySelector("#picosass-output-feedback").innerHTML = " No SCSS element to compile... ";
    
    const theCode = document.querySelector("#the-scss").innerHTML;  

    //setup and compile  
    try {

        const compiled = await sass.compileStringAsync(theCode, sassParams);

        //console.log(compiled); 

        //add the resulting CSS to the page 
        document.querySelector('#picosass-injected-style').innerHTML = compiled.css;

        //remove initial static CSS 
        document.querySelector("#picostrap-styles-css")?.setAttribute("disabled", "true");

        //as there are no errors, clear the output feedback
        document.querySelector("#picosass-output-feedback").innerHTML = '';

        //return for additional processing or saving 
        return compiled.css; //cant we have a string man?

    } catch (err) {

        //show error in output feedback 
        document.querySelector("#picosass-output-feedback").innerHTML = err;
    }

}


////// ON DOM CONTENT LOADED: COMPILE ONCE //////////////
window.addEventListener("DOMContentLoaded", (event) => {

    //prepare a space for the new CSS
    if (!document.querySelector("#picosass-injected-style")) document.head.insertAdjacentHTML("beforeend", `<style id="picosass-injected-style"> </style>`);

    //if not present, add a DIV and some styling TO SHOW COMPILER MESSAGES / OUTPUT FEEDBACK 
    if (!document.querySelector("#picosass-output-feedback")) document.querySelector("html").insertAdjacentHTML("afterbegin", `<div id='picosass-output-feedback'></div> <style> #picosass-output-feedback { position: fixed; z-index: 99999999; font-size:30px; background:#212337; color:lime; font-family:courier; border:8px solid red; padding:15px; display:block; user-select: none; } #picosass-output-feedback:empty {display:none} </style> `);

    //run  the compiler, unless a special class is added to the body
    if (!document.querySelector("body").classList.contains("prevent-sass-autocompile")) Compile();

    //attach observer to detect on-page scss code changes 
    //TODO: this is not working inside theme, yet
    let observer = new MutationObserver(mutationRecords => {
        //console.log(mutationRecords); 
        Compile();
    });

    // observe everything except attributes
    observer.observe(document.querySelector('#the-scss'), {
        childList: true, // observe direct children
        subtree: true, // and lower descendants too
        characterDataOldValue: true // pass old data to callback
    });
    


});

