document.addEventListener("DOMContentLoaded", function () {
  var exportBtn = document.getElementById("export-btn");
  var progressBar = document.getElementById("progress-bar");
  var progressContainer = document.getElementById("progress-container");
  var downloadBtn = document.getElementById("download-btn");
  var warningMessage = document.getElementById("warning-message");
  var fileSavedMessage = document.getElementById("file-saved-message");

  // Set the href attribute of the Go Back button
  var currentUrl = window.location.origin + window.location.pathname;
  document.getElementById("go-back-btn").setAttribute("href", currentUrl);

  let isExporting = false;
  let progressCheckInterval = null;

  function showWarning() {
    warningMessage.style.display = "block";
  }

  function hideWarning() {
    warningMessage.style.display = "none";
  }

  function showFileSavedMessage() {
    fileSavedMessage.style.display = "block";
  }

  // Start polling progress from the server
  function startProgressPolling() {
    let consecutiveErrors = 0;
    const maxErrors = 10;

    progressCheckInterval = setInterval(function () {
      fetch("api/export-csv.php?progress=true")
        .then(function (response) {
          if (!response.ok) throw new Error("Failed to check progress");
          return response.json();
        })
        .then(function (data) {
          consecutiveErrors = 0;
          const progress = data.progress || 0;
          const status = data.status || "running";
          const msg = data.msg || "";
          const file = data.file || "";
          const errors = data.errors || [];

          progressBar.style.width = progress + "%";
          progressBar.setAttribute("aria-valuenow", progress);

          // Update the percentage label above the bar
          var pctLabel = document.getElementById("export-pct");
          if (pctLabel) pctLabel.textContent = Math.round(progress) + "%";

          // Update status message
          var statusEl = document.getElementById("export-status");
          if (statusEl && msg && progress < 100) {
            statusEl.textContent = msg;
            statusEl.style.display = "block";
          }

          if (progress >= 100) {
            clearInterval(progressCheckInterval);
            progressCheckInterval = null;
            isExporting = false;
            hideWarning();

            // Hide status
            if (statusEl) statusEl.style.display = "none";

            if (status === "error" || !file) {
              progressBar.classList.add("bg-danger");

              if (statusEl) {
                statusEl.textContent =
                  msg || "Export failed (no file was generated).";
                statusEl.className = "text-center mt-2 text-danger";
                statusEl.style.display = "block";
              }

              // Re-enable export button
              exportBtn.disabled = false;
              exportBtn.innerHTML = "Start Export";
              return;
            }

            if (errors && errors.length > 0) {
              progressBar.classList.add("bg-warning");
              // Show errors
              var errorHtml =
                '<div class="alert alert-warning mt-3"><strong>Warnings (' +
                errors.length +
                "):</strong><br>";
              errors.forEach(function (err, idx) {
                errorHtml +=
                  "<small>" +
                  (idx + 1) +
                  ". " +
                  escapeHtml(err) +
                  "</small><br>";
              });
              errorHtml += "</div>";
              warningMessage.innerHTML = errorHtml;
              warningMessage.style.display = "block";
            } else {
              progressBar.classList.add("bg-success");
            }

            downloadBtn.href = file;
            downloadBtn.style.display = "inline-block";
            console.log("File ready for download: " + file);

            showFileSavedMessage();

            // Re-enable export button
            exportBtn.disabled = false;
            exportBtn.innerHTML = "Start Export";
          }
        })
        .catch(function (error) {
          consecutiveErrors++;
          console.error(
            "Error fetching progress:",
            error.message,
            "(" + consecutiveErrors + "/" + maxErrors + ")",
          );

          if (consecutiveErrors >= maxErrors) {
            clearInterval(progressCheckInterval);
            progressCheckInterval = null;
            isExporting = false;
            hideWarning();
            progressBar.classList.add("bg-danger");
            progressBar.innerText = "Error";

            var statusEl = document.getElementById("export-status");
            if (statusEl) {
              statusEl.textContent =
                "Lost connection to server. The export may still be running.";
              statusEl.className = "text-center mt-2 text-danger";
              statusEl.style.display = "block";
            }

            exportBtn.disabled = false;
            exportBtn.innerHTML = "Start Export";
          }
        });
    }, 800);
  }

  exportBtn.addEventListener("click", function () {
    if (isExporting) return;
    isExporting = true;

    // Reset UI
    progressContainer.style.display = "block";
    progressBar.style.width = "0%";
    progressBar.setAttribute("aria-valuenow", "0");
    progressBar.innerText = "0%";
    progressBar.classList.remove("bg-success", "bg-warning", "bg-danger");
    downloadBtn.style.display = "none";
    fileSavedMessage.style.display = "none";

    // Disable export button
    exportBtn.disabled = true;
    exportBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...';

    showWarning();

    // Start polling IMMEDIATELY
    startProgressPolling();

    // Make POST request to start export
    fetch("api/export-csv.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        table: tableName,
      }),
    })
      .then(function (response) {
        if (!response.ok) throw new Error("Failed to start export");
        return response.json();
      })
      .then(function (data) {
        if (data.status === "error") {
          // Immediate error from server
          clearInterval(progressCheckInterval);
          progressCheckInterval = null;
          isExporting = false;
          hideWarning();

          progressBar.style.width = "100%";
          progressBar.classList.add("bg-danger");
          progressBar.innerText = "Failed";

          var statusEl = document.getElementById("export-status");
          if (statusEl) {
            statusEl.textContent = data.msg;
            statusEl.className = "text-center mt-2 text-danger";
            statusEl.style.display = "block";
          }

          exportBtn.disabled = false;
          exportBtn.innerHTML = "Start Export";
        }
      })
      .catch(function (error) {
        console.error("Error starting export:", error);
        // Polling may handle the rest, but if the export never started,
        // stop polling after a short delay by leaving existing polling logic.
      });
  });

  // Warn user if they try to leave during export
  window.addEventListener("beforeunload", function (event) {
    if (isExporting) {
      var message =
        "Your export is still in progress. Are you sure you want to leave?";
      event.returnValue = message;
      return message;
    }
  });

  // Remove warning when download is clicked
  downloadBtn.addEventListener("click", function () {
    hideWarning();
  });

  // Helper: escape HTML
  function escapeHtml(str) {
    if (!str) return "";
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }
});
