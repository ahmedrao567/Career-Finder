// ================================
// Live Clock
// ================================

function updateTime() {
    const now = new Date();

    document.getElementById("time").innerHTML =
        now.toLocaleDateString() + " " + now.toLocaleTimeString();
}

setInterval(updateTime, 1000);
updateTime();


// ================================
// Load Containers
// ================================

async function loadContainers() {

    try {

        const response = await fetch("/api/containers");
        const data = await response.json();

        let html = "";

        let total = data.length;
        let healthy = 0;
        let critical = 0;

        data.forEach(c => {

            if (c.status === "running") {
                healthy++;
            } else {
                critical++;
            }

            html += `
                <tr>
                    <td>${c.name}</td>
                    <td>${c.image}</td>
                    <td>${c.status}</td>
                </tr>
            `;

        });

        document.getElementById("statusTable").innerHTML = html;

        document.getElementById("containers").innerHTML = total;
        document.getElementById("healthy").innerHTML = healthy;
        document.getElementById("critical").innerHTML = critical;
        document.getElementById("scan").innerHTML = "Just Now";

    } catch (err) {

        console.error(err);

    }

}


// ================================
// Load Logs
// ================================

async function loadLogs() {

    try {

        const response = await fetch("/api/logs");

        const logs = await response.json();

        let html = "";

        logs.forEach(log => {

            html += `<div class="log-line">${log}</div>`;

        });

        document.getElementById("logs").innerHTML = html;

    } catch (err) {

        console.error(err);

    }

}


// ================================
// AI Analysis
// ================================

async function loadAnalysis() {

    try {

        const response = await fetch("/api/analysis");

        const data = await response.json();

        document.getElementById("analysis").innerHTML =
            data.analysis || "No Analysis";

        if (document.getElementById("recommendation")) {

            document.getElementById("recommendation").innerHTML =
                data.recommendation || "Waiting...";

        }

    } catch (err) {

        console.error(err);

    }

}


// ================================
// Initial Load
// ================================

loadContainers();
loadLogs();
loadAnalysis();


// ================================
// Auto Refresh Every 10 Seconds
// ================================

setInterval(() => {

    loadContainers();
    loadLogs();
    loadAnalysis();

}, 10000);