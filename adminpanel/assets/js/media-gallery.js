// Function to add clear button to a single image element
function addClearButtonToSingleImage(imgElement, groupId) {
  var wrapper = ensureSingleImageWrapper(groupId);
  if (!wrapper) return;

  // Remove placeholder if present
  var placeholder = wrapper.querySelector(".single-image-placeholder");
  if (placeholder) placeholder.remove();

  // Ensure the media element lives inside wrapper
  if (imgElement.parentElement !== wrapper) {
    wrapper.insertBefore(imgElement, wrapper.firstChild);
  }

  ensureSingleImageClearButton(wrapper);
}

function getSingleEmptyLabel(groupId) {
  var btn = document.querySelector(
    '[data-selection="single"][group-id="' + groupId + '"]',
  );
  var type = btn ? btn.getAttribute("data-type") : "image";
  if (type === "video") return "No video selected";
  if (type === "pdf") return "No PDF selected";
  if (type === "csv") return "No file selected";
  return "No image selected";
}

function ensureSingleImageWrapper(groupId) {
  var inputElement = document.getElementById(groupId + "-input");
  if (!inputElement) return null;

  // Prefer an existing wrapper directly before the input
  var wrapper = null;
  var prev = inputElement.previousElementSibling;
  if (
    prev &&
    prev.classList &&
    prev.classList.contains("single-image-wrapper")
  ) {
    wrapper = prev;
  } else {
    wrapper = document.querySelector(
      '.single-image-wrapper[data-group-id="' + groupId + '"]',
    );
  }

  if (!wrapper) {
    wrapper = document.createElement("div");
    wrapper.className = "single-image-wrapper";
    wrapper.setAttribute("data-group-id", groupId);
    inputElement.parentNode.insertBefore(wrapper, inputElement);
  } else if (!wrapper.getAttribute("data-group-id")) {
    wrapper.setAttribute("data-group-id", groupId);
  }

  return wrapper;
}

function ensureSingleImageClearButton(wrapper) {
  if (!wrapper) return;
  var clearBtn = wrapper.querySelector(".single-image-clear");
  if (!clearBtn) {
    clearBtn = document.createElement("div");
    clearBtn.className = "single-image-clear";
    clearBtn.setAttribute("title", "Remove");
    wrapper.appendChild(clearBtn);
  }
}

function setSinglePreviewEmpty(groupId) {
  var wrapper = ensureSingleImageWrapper(groupId);
  if (!wrapper) return;

  wrapper.innerHTML = "";

  var placeholder = document.createElement("div");
  placeholder.className = "single-image-placeholder";
  placeholder.textContent = getSingleEmptyLabel(groupId);
  wrapper.appendChild(placeholder);
}

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function showUploadAlert(type, title, messages) {
  var container = document.getElementById("upload-alerts");
  if (!container) return;

  var safeType = type || "danger";
  var safeTitle = title || "Upload issue";
  var msgList = Array.isArray(messages) ? messages : [messages];
  msgList = msgList.filter(function (m) {
    return m !== null && m !== undefined && String(m).trim() !== "";
  });

  var html =
    "<div class='alert alert-" +
    safeType +
    " alert-dismissible fade show' role='alert'>";
  html += "<strong>" + escapeHtml(safeTitle) + ":</strong> ";

  if (msgList.length <= 1) {
    html += escapeHtml(msgList[0] || "Unknown error.");
  } else {
    html += "<ul class='mb-0 mt-2'>";
    msgList.slice(0, 8).forEach(function (m) {
      html += "<li><small>" + escapeHtml(m) + "</small></li>";
    });
    if (msgList.length > 8) {
      html +=
        "<li><small>...and " + (msgList.length - 8) + " more.</small></li>";
    }
    html += "</ul>";
  }

  html +=
    "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
  container.innerHTML = html;
}

function extractUploadErrors(xhr, fallbackError) {
  var messages = [];

  if (xhr && xhr.responseJSON) {
    if (Array.isArray(xhr.responseJSON.messages)) {
      messages = messages.concat(xhr.responseJSON.messages);
    }
    if (Array.isArray(xhr.responseJSON.details)) {
      messages = messages.concat(xhr.responseJSON.details);
    }
    if (xhr.responseJSON.message) messages.push(xhr.responseJSON.message);
  } else if (
    xhr &&
    typeof xhr.responseText === "string" &&
    xhr.responseText.trim()
  ) {
    try {
      var parsed = JSON.parse(xhr.responseText);
      if (Array.isArray(parsed.messages))
        messages = messages.concat(parsed.messages);
      if (Array.isArray(parsed.details))
        messages = messages.concat(parsed.details);
      if (parsed.message) messages.push(parsed.message);
    } catch (e) {
      messages.push(xhr.responseText);
    }
  }

  if (messages.length === 0) {
    messages.push(fallbackError || "Unknown upload error.");
  }

  return messages;
}

// Function to clear a single image and its input
function clearSingleImage(groupId) {
  var inputElement = document.getElementById(groupId + "-input");

  // Clear the input value
  if (inputElement) {
    inputElement.value = "";
  }

  // Keep the wrapper but show the empty state
  setSinglePreviewEmpty(groupId);
}

// Initialize clear buttons on page load for existing images
function initSingleImageClearButtons() {
  // Find all single selection buttons
  var singleSelectionButtons = document.querySelectorAll(
    '[data-selection="single"][group-id]',
  );

  singleSelectionButtons.forEach(function (btn) {
    var groupId = btn.getAttribute("group-id");
    var imgElement = document.getElementById(groupId + "-img");

    if (imgElement) {
      addClearButtonToSingleImage(imgElement, groupId);
    } else {
      setSinglePreviewEmpty(groupId);
    }
  });
}

function moveFileToIndex(currentIndex, targetIndex, inputId) {
  var inputFile = $("#" + inputId);
  // Get the current file names
  var fileNames = inputFile.val().split(",");
  // Removing file with currentIndex
  var removedFileName = fileNames.splice(currentIndex, 1)[0];
  // Inserting the removed file name at the targetIndex
  fileNames.splice(targetIndex, 0, removedFileName);
  // Updating
  inputFile.val(fileNames.join(","));
}

function updateOrder(id) {
  const parentDiv = document.getElementById(id);
  const childDivs = parentDiv.children;
  for (let i = 0; i < childDivs.length; i++) {
    childDivs[i].setAttribute("order", i);
  }
}

function SortableRefresh() {
  $(".upload__img-wrap").sortable({
    update: function (event, ui) {
      var parentGridId = ui.item.closest(".upload__img-wrap").attr("id");
      var currentIndex = ui.item.attr("order"); // gets index from attribute
      var targetIndex = ui.item.index(); // new index after sorting
      moveFileToIndex(
        currentIndex,
        targetIndex,
        parentGridId.replace("-grid", "-input"),
      );
      updateOrder(parentGridId);
    },
  });
}

function getFileNameFromUrl(url) {
  return url.substring(url.lastIndexOf("/") + 1);
}

function getFileNamesFromUrls(urlArray, dataId) {
  var fileNames = [];
  var grid = $("#" + dataId + "-grid");
  var lastIndex =
    grid.children().length > 0
      ? parseInt(grid.children(":last").attr("order"), 10)
      : 0;

  for (var i = 0; i < urlArray.length; i++) {
    var currentIndex = lastIndex + i + 1;
    var htmlContent = `<div class='upload__img-box' order='${currentIndex}'>
                            <div style='background-image: url(${urlArray[i]})' class='img-bg'>
                                <div class='upload__img-close'></div>
                            </div>
                        </div>`;
    grid.append(htmlContent);
    var fileName = getFileNameFromUrl(urlArray[i]);
    fileNames.push(fileName);
  }

  return fileNames;
}

function removeFileNameAtIndex(inputId, indexToRemove) {
  var fileNamesString = $("#" + inputId).val();
  var fileNamesArray = fileNamesString.split(",");

  fileNamesArray.splice(indexToRemove, 1);

  var updatedFileNamesString = fileNamesArray.join(",");

  $("#" + inputId).val(updatedFileNamesString);
}

function loadMedia(page, FileType) {
  if (!loadOrNot || mediaLoadInFlight) return;

  var grid = document.querySelector(".media-grid");
  if (!grid) return;
  var requestToken = mediaLoadToken;
  mediaLoadInFlight = true;
  setMediaLoadStatus("Loading media…");

  mediaRequest = $.ajax({
    url: "media-gallery.php",
    method: "POST",
    dataType: "json",
    data: { type: FileType, page: page, itemsPerPage: getMediaPageSize() },
    success: function (response) {
      if (requestToken !== mediaLoadToken) return;
      mediaLoadInFlight = false;
      mediaRequest = null;

      if (response.status !== "success") {
        mediaLoadFailed = true;
        setMediaLoadStatus("Could not load media.", true);
        return;
      }

      mediaLoadFailed = false;
      if (page === 1) grid.querySelectorAll(".media-tile").forEach(function (tile) { tile.remove(); });
      var sentinel = grid.querySelector(".media-load-sentinel");
      if (response.data) sentinel.insertAdjacentHTML("beforebegin", response.data);
      currentPage = page;
      loadOrNot = response.has_more;
      setMediaLoadStatus(loadOrNot ? "" : (grid.querySelector(".media-tile") ? "All media loaded" : "No media yet"));
      if (loadOrNot) setTimeout(checkMediaLoadPosition, 0);
    },
    error: function (xhr, status) {
      if (requestToken !== mediaLoadToken || status === "abort") return;
      mediaLoadInFlight = false;
      mediaRequest = null;
      mediaLoadFailed = true;
      setMediaLoadStatus("Could not load media.", true);
    },
  });
}

var selectionType = "single"; // Default selection type
var selectedIds = [];

var dataId = "";

var currentPage = 0; // Last successfully loaded page

var loadOrNot = true;
var mediaLoadInFlight = false;
var mediaLoadFailed = false;
var mediaLoadToken = 0;
var mediaRequest = null;
var mediaItemsPerPage = null;

var FileType = null;

function updateSelectButtonState() {
  var btn = document.getElementById("selectBTN");
  if (!btn) return;
  btn.disabled = !Array.isArray(selectedIds) || selectedIds.length === 0;
}

function resetMediaSelectionState() {
  selectedIds = [];
  $(".media-tile.selected_tile").removeClass("selected_tile");
  $("#deleteSelected").addClass("d-none");
  $('input[name="attachment-id"]').val("");
  $('input[name="attachment-desc"]').val("");
  $("#attachment-preview").html('<img class="img-fluid">');
  updateSelectButtonState();
}

function getMediaPageSize() {
  if (mediaItemsPerPage !== null) return mediaItemsPerPage;
  var grid = document.querySelector(".media-grid");
  var width = grid ? grid.clientWidth : window.innerWidth;
  var modal = grid && grid.closest(".modal");
  var visibleHeight = modal ? grid.clientHeight : window.innerHeight - grid.getBoundingClientRect().top;
  var columns = Math.max(1, Math.floor(width / 148));
  var rows = Math.ceil(Math.max(visibleHeight, 148) / 148) + 2;
  mediaItemsPerPage = Math.max(24, Math.min(100, columns * rows));
  return mediaItemsPerPage;
}

function setMediaLoadStatus(message, canRetry) {
  var status = document.querySelector(".media-load-sentinel");
  if (!status) return;
  status.querySelector(".media-load-message").textContent = message;
  status.querySelector(".media-load-retry").classList.toggle("d-none", !canRetry);
}

function checkMediaLoadPosition() {
  if (!loadOrNot || mediaLoadInFlight || mediaLoadFailed || currentPage === 0) return;
  var grid = document.querySelector(".media-grid");
  if (!grid) return;
  var modal = grid.closest(".modal");
  if (modal && !modal.classList.contains("show")) return;
  var sentinel = grid.querySelector(".media-load-sentinel");
  var limit = modal ? grid.getBoundingClientRect().bottom : window.innerHeight;
  if (sentinel.getBoundingClientRect().top <= limit + 150) {
    loadMedia(currentPage + 1, FileType);
  }
}

function resetMediaLoading() {
  mediaLoadToken++;
  if (mediaRequest) mediaRequest.abort();
  mediaRequest = null;
  mediaLoadInFlight = false;
  mediaLoadFailed = false;
  loadOrNot = true;
  currentPage = 0;
  mediaItemsPerPage = null;
  document.querySelectorAll(".media-grid .media-tile").forEach(function (tile) { tile.remove(); });
  setMediaLoadStatus("");
}

SortableRefresh(); //initiate all sortable component

document.addEventListener("DOMContentLoaded", function () {
  // Initialize clear buttons for existing single images
  initSingleImageClearButtons();
  updateSelectButtonState();

  var mediaGrid = document.querySelector(".media-grid");
  if (mediaGrid) {
    var sentinel = mediaGrid.querySelector(".media-load-sentinel");
    if ("IntersectionObserver" in window && sentinel) {
      var observer = new IntersectionObserver(checkMediaLoadPosition, {
        root: mediaGrid.closest(".modal") ? mediaGrid : null,
        rootMargin: "150px 0px",
      });
      observer.observe(sentinel);
    } else {
      (mediaGrid.closest(".modal") ? mediaGrid : window).addEventListener("scroll", checkMediaLoadPosition, { passive: true });
    }
    mediaGrid.querySelector(".media-load-retry")?.addEventListener("click", function () {
      mediaLoadFailed = false;
      loadMedia(currentPage + 1, FileType);
    });
  }

  // Reset stale modal selection state on open/close
  $("#mediaSelectionModal").on("shown.bs.modal hidden.bs.modal", function () {
    resetMediaSelectionState();
  });

  // Event handler for single image clear button
  document.addEventListener("click", function (event) {
    if (event.target.classList.contains("single-image-clear")) {
      var wrapper = event.target.closest(".single-image-wrapper");
      if (wrapper) {
        var groupId = wrapper.getAttribute("data-group-id");
        clearSingleImage(groupId);
      }
    }
  });

  // Get the file input element
  var fileInput = document.getElementById("add-new-media");
  var loader = document.getElementById("loader");

  var MAX_FILES_PER_BATCH = 15; // Prevent hitting PHP max_file_uploads default (20)
  var MAX_BATCH_BYTES = 20 * 1024 * 1024; // Keep each request under ~20MB

  function batchFiles(files) {
    var batches = [];
    var currentBatch = [];
    var currentSize = 0;

    files.forEach(function (file) {
      var exceedsCount = currentBatch.length >= MAX_FILES_PER_BATCH;
      var exceedsSize =
        currentBatch.length > 0 && currentSize + file.size > MAX_BATCH_BYTES;

      if (exceedsCount || exceedsSize) {
        batches.push(currentBatch);
        currentBatch = [];
        currentSize = 0;
      }

      currentBatch.push(file);
      currentSize += file.size;

      if (
        currentBatch.length >= MAX_FILES_PER_BATCH ||
        currentSize >= MAX_BATCH_BYTES
      ) {
        batches.push(currentBatch);
        currentBatch = [];
        currentSize = 0;
      }
    });

    if (currentBatch.length > 0) {
      batches.push(currentBatch);
    }

    return batches;
  }

  // Attach an event listener to the change event
  fileInput.addEventListener("change", function () {
    var files = Array.from(fileInput.files || []);
    if (files.length === 0) {
      return;
    }

    var batches = batchFiles(files);
    var batchIndex = 0;
    var aggregatedHtml = "";
    var errorMessages = [];

    function finalizeUploads() {
      loader.classList.add("d-none");
      fileInput.value = "";

      if (aggregatedHtml.length > 0) {
        var mediaGrid = document.querySelector(".media-grid");
        if (mediaGrid) {
          mediaGrid.insertAdjacentHTML("afterbegin", aggregatedHtml);
        }
      }

      if (errorMessages.length > 0) {
        console.error("Some files could not be uploaded:", errorMessages);
        showUploadAlert("danger", "Upload issue", errorMessages);
      } else {
        showUploadAlert(
          "success",
          "Upload complete",
          "Files uploaded successfully.",
        );
      }
    }

    function uploadNextBatch() {
      if (batchIndex >= batches.length) {
        finalizeUploads();
        return;
      }

      var formData = new FormData();
      batches[batchIndex].forEach(function (file) {
        formData.append("file[]", file);
      });

      $.ajax({
        url: "media-gallery.php",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
          if (response.status === "success" && response.data) {
            aggregatedHtml += response.data;
          } else if (response.status === "error") {
            if (Array.isArray(response.messages)) {
              response.messages.forEach(function (message) {
                errorMessages.push(message);
              });
            } else if (response.message) {
              errorMessages.push(response.message);
            }
          }
          batchIndex += 1;
          uploadNextBatch();
        },
        error: function (xhr, status, error) {
          var msgs = extractUploadErrors(xhr, error || "Unknown upload error.");
          msgs.forEach(function (m) {
            errorMessages.push(m);
          });
          batchIndex += 1;
          uploadNextBatch();
        },
      });
    }

    // Show loader and start sequential uploads
    loader.classList.remove("d-none");
    uploadNextBatch();
  });

  // Check if the clicked element has the class 'openMediaModal'
  document.addEventListener("click", function (event) {
    if (event.target.classList.contains("openMediaModal")) {
      var openModalButton = event.target;
      FileType = openModalButton.getAttribute("data-type");
      selectionType = openModalButton.getAttribute("data-selection");
      dataId = openModalButton.getAttribute("group-id");

      $("#SelectionModalLabel").text(FileType + " Selection");

      SortableRefresh();

      // Reset stale load/selection state
      resetMediaLoading();
      resetMediaSelectionState();

    }
  });

  $("#mediaSelectionModal").on("shown.bs.modal", function () {
    if (currentPage === 0 && !mediaLoadInFlight) loadMedia(1, FileType);
    else checkMediaLoadPosition();
  });

  $("#descSaveBtn").click(function () {
    // Get the value of the description input field
    const attachmentDesc = $("input[name=attachment-desc]").val();
    const attachmentID = $("input[name=attachment-id]").val();

    // Check if the description is not empty
    if (attachmentDesc.trim() !== "") {
      // Show loading spinner
      $("#descSaveBtn i")
        .removeClass("fa-floppy-o")
        .addClass("fa-spinner fa-spin");

      // Send AJAX request
      $.ajax({
        url: "media-gallery.php",
        method: "POST",
        data: {
          id: attachmentID,
          description: attachmentDesc,
        },
        success: function (response) {
          // request successful or not
          if (response.status === "success") {
            document
              .getElementById(attachmentID)
              .setAttribute("data-desc", attachmentDesc);
            // Replace the icon with a tick icon
            $("#descSaveBtn i")
              .removeClass("fa-spinner fa-spin")
              .addClass("fa-check");
          } else {
            // Show error message
            alert("Request failed: " + response.message);
            // Restore the floppy icon
            $("#descSaveBtn i")
              .removeClass("fa-spinner fa-spin")
              .addClass("fa-floppy-o");
          }
        },
        error: function (xhr, status, error) {
          if (xhr.responseText) {
            alert("Error loading media: " + xhr.responseText);
          } else {
            alert("Error loading media: " + error);
          }
          // Restore the floppy icon
          $("#descSaveBtn i")
            .removeClass("fa-spinner fa-spin")
            .addClass("fa-floppy-o");
        },
      });
    } else {
      // Show error message if description is empty
      alert("Please enter a description.");
    }
  });

  function updateAttachmentDetails(id) {
    var tile = $("#" + id);
    var type = tile.data("type");
    var file = tile.data("file");
    var icon = tile.find("img").attr("src");

    if (type == "image") {
      // Update image preview
      $("#attachment-preview").html(
        '<img class="img-fluid" alt="Attachment Preview" src="' + file + '">',
      );
    } else if (type == "video") {
      // Update image preview
      $("#attachment-preview").html(
        "<video controls><source src='" +
          file +
          "' type='video/mp4'>Your browser doesn't support this video.</video>",
      );
    } else if (type == "pdf") {
      $("#attachment-preview").html(
        '<object class="pdf" data="' + file + '"></object>',
      );
    } else {
      // Update image preview
      $("#attachment-preview").html(
        '<img class="img-fluid" alt="Attachment Preview" src="' + icon + '">',
      );
    }

    //attachment file link
    $("#attachment-link").attr("href", file);

    // Set hidden input field values
    $('input[name="attachment-id"]').val(tile.attr("id"));
    $('input[name="attachment-desc"]').val(tile.attr("data-desc"));

    // Reset save button icon
    $("#descSaveBtn i")
      .removeClass("fa-spinner fa-spin")
      .addClass("fa-floppy-o");
  }

  function toggleSelected(id) {
    var tile = $("#" + id);

    // Unselect previously selected tiles if selection type is 'single'
    if (selectionType === "single") {
      $(".media-tile.selected_tile").not(tile).removeClass("selected_tile");
    }

    // Toggle selected_tile class for the clicked tile
    tile.toggleClass("selected_tile");

    // Update selectedIds array based on selection type
    if (selectionType === "single") {
      selectedIds = tile.hasClass("selected_tile") ? [id] : [];
    } else if (selectionType === "multiple") {
      if (tile.hasClass("selected_tile")) {
        if (!selectedIds.includes(id)) selectedIds.push(id);
      } else {
        selectedIds = selectedIds.filter(function (item) {
          return item !== id;
        });
      }
    }

    if (typeof bulkSelect === "undefined" && selectionType === "single") {
      // Add classes
      $("#mediaSelectionModal .media-grid").addClass("col-lg-10");
      $("#mediaSelectionModal .attachment-detail").addClass("d-lg-block");
    }
    updateAttachmentDetails(id);
    updateSelectButtonState();
  }

  // Media Tile Click Event
  $(document).on("click", ".media-tile", function () {
    var id = $(this).attr("id");

    if (typeof bulkSelect !== "undefined") {
      if (bulkSelect === "on") {
        toggleSelected(id);
      }
    } else {
      toggleSelected(id);
    }

    if (typeof bulkSelect !== "undefined") {
      if (bulkSelect === "on" && selectedIds.length > 0) {
        $("#deleteSelected").removeClass("d-none");
      } else {
        $("#deleteSelected").addClass("d-none");

        updateAttachmentDetails(id);
        $("#attachmentModal").modal("show");
      }
    }
    console.log(selectedIds);
  });

  // Select Button Click Event
  $("#selectBTN").on("click", function () {
    if (!Array.isArray(selectedIds) || selectedIds.length === 0) {
      updateSelectButtonState();
      return;
    }

    // Get selected image URLs
    var selectedUrls = selectedIds.map(function (id) {
      return $("#" + id).data("file");
    });

    console.log(selectedUrls);

    // Check if we're in CKEditor mode
    if (window.ckeditorMediaGalleryMode && window.currentCKEditorInstance) {
      // Insert images into CKEditor
      insertSelectedImagesIntoCKEditor(selectedUrls);

      // Close the modal
      $("#mediaSelectionModal").modal("hide");

      // Reset selection
      selectedIds = [];
      $(".media-tile.selected_tile").removeClass("selected_tile");

      return; // Exit early, don't run the regular logic
    }

    // Regular logic for non-CKEditor usage
    if (selectionType === "single") {
      if (!selectedUrls[0]) {
        showUploadAlert("danger", "Selection issue", "Please select a file.");
        return;
      }
      document.getElementById(dataId + "-input").value = getFileNameFromUrl(
        selectedUrls[0],
      );

      var wrapper = ensureSingleImageWrapper(dataId);
      if (wrapper) {
        // Clear existing preview content, keep wrapper
        wrapper.innerHTML = "";

        var mediaEl = null;
        if (FileType == "image") {
          mediaEl = document.createElement("img");
          mediaEl.src = selectedUrls[0];
          mediaEl.id = dataId + "-img";
          mediaEl.className = "full-width";
          mediaEl.alt = "Selected image";
        } else if (FileType == "video") {
          mediaEl = document.createElement("video");
          mediaEl.id = dataId + "-img";
          mediaEl.controls = true;
          mediaEl.style.width = "100%";
          mediaEl.style.height = "100%";
          var source = document.createElement("source");
          source.src = selectedUrls[0];
          source.type = "video/mp4";
          mediaEl.appendChild(source);
        } else if (FileType == "pdf") {
          mediaEl = document.createElement("object");
          mediaEl.id = dataId + "-img";
          mediaEl.className = "pdf";
          mediaEl.setAttribute("data", selectedUrls[0]);
        } else if (FileType == "csv") {
          mediaEl = document.createElement("img");
          mediaEl.src = "assets/img/csv-icon.png";
          mediaEl.id = dataId + "-img";
          mediaEl.className = "full-width";
          mediaEl.alt = "CSV file";
        } else {
          mediaEl = document.createElement("img");
          mediaEl.src = "assets/img/dummy.png";
          mediaEl.id = dataId + "-img";
          mediaEl.className = "full-width";
          mediaEl.alt = "Selected file";
        }

        if (mediaEl) {
          wrapper.appendChild(mediaEl);
          addClearButtonToSingleImage(mediaEl, dataId);
        }
      }
    } else if (selectionType === "multiple") {
      var input_field = document.getElementById(dataId + "-input");
      var selectedFileArr = getFileNamesFromUrls(selectedUrls, dataId);

      // Update input
      if (input_field.value.length > 0) {
        input_field.value += "," + selectedFileArr;
      } else {
        input_field.value = selectedFileArr;
      }
    }

    selectedIds = [];
    $(".media-tile.selected_tile").removeClass("selected_tile");
    updateSelectButtonState();
  });

  //remove gallery img
  $("body").on("click", ".upload__img-close", function (e) {
    var parentGridId = $(this).closest(".upload__img-wrap").attr("id");
    var parentDiv = $(this).closest(".upload__img-box");
    var indexToRemove = $(this).closest(".upload__img-box").attr("order");
    //update input field
    removeFileNameAtIndex(
      parentGridId.replace("-grid", "-input"),
      indexToRemove,
    );

    // Removing the img element from ui
    parentDiv.remove();
    updateOrder(parentGridId);
  });
});
