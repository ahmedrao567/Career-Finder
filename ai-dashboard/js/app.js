document.getElementById("time").innerHTML =
new Date().toLocaleTimeString();

const containers=[

{

name:"career-finder-api",

status:"Critical",

cpu:"78%",

memory:"620MB"

},

{

name:"career-finder-web",

status:"Healthy",

cpu:"18%",

memory:"250MB"

},

{

name:"mysql",

status:"Healthy",

cpu:"21%",

memory:"350MB"

},

{

name:"grafana",

status:"Healthy",

cpu:"12%",

memory:"150MB"

}

];

let html="";

containers.forEach(c=>{

html+=`

<tr>

<td>${c.name}</td>

<td>${c.status}</td>

<td>${c.cpu}</td>

<td>${c.memory}</td>

</tr>

`;

});

document.getElementById("statusTable").innerHTML=html;

document.getElementById("logs").innerHTML=`

08:42 INFO Starting Gunicorn...

08:42 ERROR Worker Timeout

08:42 ERROR SystemExit:1

`;

document.getElementById("analysis").innerHTML=`

<b>Root Cause</b><br><br>

Gunicorn worker exceeded timeout while processing a request.

Likely caused by:

<ul>

<li>Large PDF</li>

<li>Heavy ML Model</li>

<li>Blocking Python Function</li>

</ul>

`;

document.getElementById("recommendation").innerHTML=`

Increase Gunicorn timeout

<pre>

gunicorn --timeout 180 app:app

</pre>

Move heavy ML work into a background queue.

`;