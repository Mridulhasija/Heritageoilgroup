document.addEventListener("DOMContentLoaded", function () {
  const importInput = document.getElementById("import-csv-input");
  const importButton = document.getElementById("import-btn");
  const progressBar = document.getElementById("progress-bar");
  const progressContainer = document.getElementById("import-progress");
  const errorContainer = document.getElementById("import-errors");
  const messageElement = document.getElementById("message");
  const successText = document.getElementById("success-message");

  var currentUrl = window.location.origin + window.location.pathname;
  document.getElementById("go-back-btn").setAttribute("href", currentUrl);

  let isImporting = false;
  let progressCheckInterval = null;

  // Function to check if the import button should be shown or hidden
  function updateImportButtonVisibility() {
    const fileLabel = document.getElementById("csv-file-label");
    if (importInput.value) {
      importButton.classList.remove("d-none");
      // Show filename (last segment after / or \)
      if (fileLabel) {
        const parts = importInput.value.split(/[\/\\]/);
        fileLabel.textContent = parts[parts.length - 1] || importInput.value;
        fileLabel.classList.remove("text-muted");
        fileLabel.classList.add("text-success");
      }
    } else {
      importButton.classList.add("d-none");
      if (fileLabel) {
        fileLabel.textContent = "No file selected";
        fileLabel.classList.remove("text-success");
        fileLabel.classList.add("text-muted");
      }
    }
  }

  // Use MutationObserver to detect changes in the hidden input's value
  const observer = new MutationObserver(updateImportButtonVisibility);
  observer.observe(importInput, {
    attributes: true,
    attributeFilter: ["value"],
  });

  // Update progress bar UI
  function updateProgressUI(progress, msg, errors, stats) {
    const pct = Math.round(progress);
    progressBar.style.width = progress + "%";
    progressBar.setAttribute("aria-valuenow", progress);

    // Update the percentage label above the bar
    const pctLabel = document.getElementById("progress-pct");
    if (pctLabel) pctLabel.textContent = pct + "%";

    // Show current status message below progress bar
    const statusEl = document.getElementById("import-status");
    if (statusEl && msg && progress < 100) {
      statusEl.textContent = msg;
      statusEl.classList.remove("d-none");
    }
  }

  // Handle import completion
  function handleCompletion(msg, errors, stats) {
    isImporting = false;

    // Hide status message
    const statusEl = document.getElementById("import-status");
    if (statusEl) statusEl.classList.add("d-none");

    // Show errors if any
    if (errors && errors.length > 0) {
      if (errorContainer) {
        let errorHtml =
          "<strong>Issues during import (" + errors.length + "):</strong><br>";
        errors.forEach(function (err, idx) {
          errorHtml +=
            "<small>" + (idx + 1) + ". " + escapeHtml(err) + "</small><br>";
        });
        errorContainer.innerHTML = errorHtml;
        errorContainer.classList.remove("d-none");
      }
    }

    // Show stats if available
    if (stats && successText) {
      let summary = "";
      if (stats.inserted) summary += stats.inserted + " records processed. ";
      if (stats.skipped)
        summary += stats.skipped + " rows skipped due to errors. ";
      if (stats.failed_batches)
        summary += stats.failed_batches + " batch(es) had issues. ";
      if (stats.media_records)
        summary += stats.media_records + " media records handled.";

      if (errors && errors.length > 0) {
        successText.innerHTML =
          '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> ' +
          "Import completed with warnings.</span><br><small>" +
          escapeHtml(summary) +
          "</small>";
      } else {
        successText.innerHTML =
          '<span class="text-success"><i class="fa fa-check-circle"></i> ' +
          escapeHtml(msg) +
          "</span><br><small>" +
          escapeHtml(summary) +
          "</small>";
      }
    } else if (successText) {
      if (errors && errors.length > 0) {
        successText.innerHTML =
          '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> ' +
          escapeHtml(msg) +
          "</span>";
      } else {
        successText.innerHTML =
          '<span class="text-success"><i class="fa fa-check-circle"></i> ' +
          escapeHtml(msg) +
          "</span>";
      }
    }

    if (messageElement) {
      messageElement.classList.remove("d-none");
    }

    // Change progress bar color based on result
    if (errors && errors.length > 0) {
      progressBar.classList.remove("bg-success");
      progressBar.classList.add("bg-warning");
    } else {
      progressBar.classList.remove("bg-warning");
      progressBar.classList.add("bg-success");
    }

    // Re-enable import button
    importButton.disabled = false;
    importButton.innerHTML = "Start Import";
  }

  // Poll progress from the server
  function startProgressPolling() {
    let consecutiveErrors = 0;
    const maxErrors = 10;

    progressCheckInterval = setInterval(function () {
      fetch("api/import-csv.php?progress=true")
        .then(function (response) {
          if (!response.ok) throw new Error("Failed to check progress");
          return response.json();
        })
        .then(function (data) {
          consecutiveErrors = 0; // Reset error count on success
          const progress = data.progress || 0;
          const msg = data.msg || "";
          const errors = data.errors || [];
          const stats = data.stats || {};

          updateProgressUI(progress, msg, errors, stats);

          if (progress >= 100) {
            clearInterval(progressCheckInterval);
            progressCheckInterval = null;
            handleCompletion(msg, errors, stats);
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
            handleCompletion(
              "Lost connection to server while checking progress. The import may still be running.",
              ["Connection to server lost after " + maxErrors + " retries."],
              {},
            );
          }
        });
    }, 800); // Poll every 800ms for smoother updates
  }

  // Handle the import button click event
  importButton.addEventListener("click", function () {
    const csvFilePath = importInput.value;

    if (!csvFilePath || !tableName) {
      showAlert("Please select a CSV file.", "warning");
      return;
    }

    if (isImporting) {
      showAlert("An import is already in progress.", "info");
      return;
    }

    isImporting = true;

    // Reset UI
    progressContainer.classList.remove("d-none");
    progressBar.style.width = "0%";
    progressBar.setAttribute("aria-valuenow", "0");
    progressBar.innerText = "0%";
    progressBar.classList.remove("bg-success", "bg-warning", "bg-danger");
    if (errorContainer) {
      errorContainer.innerHTML = "";
      errorContainer.classList.add("d-none");
    }
    if (messageElement) messageElement.classList.add("d-none");

    // Disable import button
    importButton.disabled = true;
    importButton.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importing...';

    // Start polling IMMEDIATELY (before POST completes)
    startProgressPolling();

    const formData = new FormData();
    formData.append("csvFilePath", csvFilePath);
    formData.append("tableName", tableName);

    // Send data to the server
    fetch("api/import-csv.php", {
      method: "POST",
      body: formData,
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        if (data.status === "error") {
          // Server returned an immediate error (validation failure)
          clearInterval(progressCheckInterval);
          progressCheckInterval = null;
          isImporting = false;

          progressBar.style.width = "100%";
          progressBar.classList.add("bg-danger");
          progressBar.innerText = "Failed";
          handleCompletion(data.msg, [data.msg], {});
          importButton.disabled = false;
          importButton.innerHTML = "Start Import";
        }
        // If status === 'started', polling is already running
      })
      .catch(function (error) {
        console.error("Error starting import:", error);
        // Don't stop polling — the import might still be running server-side
        // The polling will detect completion or timeout
      });
  });

  // Warn user about leaving during import
  window.addEventListener("beforeunload", function (event) {
    if (isImporting) {
      var message =
        "Your import is still in progress. Are you sure you want to leave?";
      event.returnValue = message;
      return message;
    }
  });

  // Helper: escape HTML to prevent XSS
  function escapeHtml(str) {
    if (!str) return "";
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  // Helper: show a Bootstrap-style inline alert
  function showAlert(msg, type) {
    if (errorContainer) {
      errorContainer.className = "alert alert-" + type + " mt-3";
      errorContainer.innerHTML = escapeHtml(msg);
      errorContainer.classList.remove("d-none");
      setTimeout(function () {
        errorContainer.classList.add("d-none");
      }, 5000);
    }
  }

  // Initial check to set the import button visibility
  updateImportButtonVisibility();
});
