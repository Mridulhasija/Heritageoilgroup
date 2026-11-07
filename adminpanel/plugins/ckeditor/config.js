function MediaGalleryPlugin(editor) {
  // Add custom media gallery button to toolbar
  editor.ui.componentFactory.add("mediaGallery", (locale) => {
    // Get ButtonView class from an existing button
    const existingButton = editor.ui.componentFactory.create("bold");
    const ButtonView = existingButton.constructor;

    const button = new ButtonView(locale);

    button.set({
      label: "Media Gallery",
      icon: '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6.91 10.54c.26-.23.64-.21.88.03l3.36 3.14 2.23-2.06a.64.64 0 0 1 .87 0l2.52 2.97V4.5H3.2v10.12l3.71-4.08zm10.27-7.51c.6 0 1.09.47 1.09 1.05v11.84c0 .59-.49 1.06-1.09 1.06H2.79c-.6 0-1.09-.47-1.09-1.06V4.08c0-.58.49-1.05 1.1-1.05h14.38zm-5.22 5.56a1.96 1.96 0 1 1 3.4-1.96 1.96 1.96 0 0 1-3.4 1.96z"/></svg>',
      tooltip: true,
    });

    button.on("execute", () => {
      openMediaGalleryForCKEditor(editor);
    });

    return button;
  });
}

// Global function to handle media gallery integration with CKEditor
function openMediaGalleryForCKEditor(editorInstance) {
  // Store the editor instance globally so we can access it after selection
  window.currentCKEditorInstance = editorInstance;

  // Set media gallery parameters for CKEditor mode
  window.ckeditorMediaGalleryMode = true;

  // Trigger your existing media gallery modal
  // You can either programmatically trigger it or simulate a button click
  const modalTrigger = document.createElement("button");
  modalTrigger.classList.add("openMediaModal");
  modalTrigger.setAttribute("data-bs-toggle", "modal");
  modalTrigger.setAttribute("data-bs-target", "#mediaSelectionModal");
  modalTrigger.setAttribute("data-type", "image");
  modalTrigger.setAttribute("data-selection", "multiple"); // Allow multiple selection
  modalTrigger.setAttribute("group-id", "ckeditor-temp");
  modalTrigger.style.display = "none";

  document.body.appendChild(modalTrigger);
  modalTrigger.click();
  document.body.removeChild(modalTrigger);
}

// Function to insert selected images into CKEditor
function insertSelectedImagesIntoCKEditor(imageUrls) {
  const editor = window.currentCKEditorInstance;

  if (!editor) {
    console.error("No CKEditor instance found");
    return;
  }

  // Insert each selected image
  imageUrls.forEach((url) => {
    editor.model.change((writer) => {
      const imageElement = writer.createElement("imageBlock", {
        src: url,
      });

      editor.model.insertContent(imageElement, editor.model.document.selection);
    });
  });

  // Clean up
  window.currentCKEditorInstance = null;
  window.ckeditorMediaGalleryMode = false;
}

// Mobile device detection
function isMobileDevice() {
  return window.innerWidth <= 768;
}

const removedPlugin = [
  "AIAssistant",
  "CKBox",
  "CKFinder",
  "EasyImage",
  "Base64UploadAdapter",
  "RealTimeCollaborativeComments",
  "RealTimeCollaborativeTrackChanges",
  "RealTimeCollaborativeRevisionHistory",
  "PresenceList",
  "Comments",
  "TrackChanges",
  "TrackChangesData",
  "RevisionHistory",
  "Pagination",
  "WProofreader",
  "MathType",
  "SlashCommand",
  "Template",
  "DocumentOutline",
  "FormatPainter",
  "TableOfContents",
  "PasteFromOfficeEnhanced",
];

// Initialize CKEditor with media gallery support
if (document.querySelectorAll(".ckeditor").length > 0) {
  document.querySelectorAll(".ckeditor").forEach((editorElement) => {
    if (isMobileDevice()) {
      // Mobile Configuration
      CKEDITOR.ClassicEditor.create(editorElement, {
        toolbar: [
          "heading",
          "|",
          "bold",
          "italic",
          "link",
          "bulletedList",
          "numberedList",
          "todoList",
          "outdent",
          "indent",
          "|",
          "mediaGallery",
          "blockQuote",
          "insertTable",
          "|",
          "undo",
          "redo",
        ],
        extraPlugins: [MediaGalleryPlugin],
        // ... rest of mobile config
        heading: {
          options: [
            {
              model: "paragraph",
              title: "Paragraph",
              class: "ck-heading_paragraph",
            },
            {
              model: "heading1",
              view: "h1",
              title: "Heading 1",
              class: "ck-heading_heading1",
            },
            {
              model: "heading2",
              view: "h2",
              title: "Heading 2",
              class: "ck-heading_heading2",
            },
            {
              model: "heading3",
              view: "h3",
              title: "Heading 3",
              class: "ck-heading_heading3",
            },
          ],
        },
        image: {
          toolbar: [
            "imageTextAlternative",
            "|",
            "imageStyle:inline",
            "imageStyle:wrapText",
            "imageStyle:breakText",
          ],
        },
        list: {
          properties: { styles: true, startIndex: true, reversed: true },
        },
        table: {
          contentToolbar: ["tableColumn", "tableRow", "mergeTableCells"],
        },
        removePlugins: removedPlugin,
      });
    } else {
      // Desktop Configuration
      CKEDITOR.ClassicEditor.create(editorElement, {
        extraPlugins: [MediaGalleryPlugin],
        toolbar: {
          items: [
            "exportPDF",
            "exportWord",
            "|",
            "findAndReplace",
            "selectAll",
            "|",
            "heading",
            "|",
            "bold",
            "italic",
            "strikethrough",
            "underline",
            "code",
            "subscript",
            "superscript",
            "removeFormat",
            "|",
            "bulletedList",
            "numberedList",
            "todoList",
            "|",
            "sourceEditing",
            "removeFormat",
            "-",
            "fontSize",
            "fontFamily",
            "fontColor",
            "fontBackgroundColor",
            "highlight",
            "|",
            "alignment",
            "|",
            "link",
            "mediaGallery",
            "blockQuote",
            "insertTable",
            "mediaEmbed",
            "codeBlock",
            "htmlEmbed",
            "|",
            "specialCharacters",
            "horizontalLine",
            "pageBreak",
            "|",
            "undo",
            "redo",
          ],
          shouldNotGroupWhenFull: true,
        },
        // ... rest of desktop config
        list: {
          properties: { styles: true, startIndex: true, reversed: true },
        },
        heading: {
          options: [
            {
              model: "paragraph",
              title: "Paragraph",
              class: "ck-heading_paragraph",
            },
            {
              model: "heading1",
              view: "h1",
              title: "Heading 1",
              class: "ck-heading_heading1",
            },
            {
              model: "heading2",
              view: "h2",
              title: "Heading 2",
              class: "ck-heading_heading2",
            },
            {
              model: "heading3",
              view: "h3",
              title: "Heading 3",
              class: "ck-heading_heading3",
            },
            {
              model: "heading4",
              view: "h4",
              title: "Heading 4",
              class: "ck-heading_heading4",
            },
            {
              model: "heading5",
              view: "h5",
              title: "Heading 5",
              class: "ck-heading_heading5",
            },
            {
              model: "heading6",
              view: "h6",
              title: "Heading 6",
              class: "ck-heading_heading6",
            },
          ],
        },
        fontFamily: {
          options: [
            "default",
            "Arial, Helvetica, sans-serif",
            "Courier New, Courier, monospace",
            "Georgia, serif",
            "Lucida Sans Unicode, Lucida Grande, sans-serif",
            "Tahoma, Geneva, sans-serif",
            "Times New Roman, Times, serif",
            "Trebuchet MS, Helvetica, sans-serif",
            "Verdana, Geneva, sans-serif",
          ],
          supportAllValues: true,
        },
        fontSize: {
          options: [10, 12, 14, "default", 18, 20, 22],
          supportAllValues: true,
        },
        htmlSupport: {
          allow: [
            { name: /.*/, attributes: true, classes: true, styles: true },
          ],
        },
        htmlEmbed: {
          showPreviews: true,
        },
        link: {
          decorators: {
            addTargetToExternalLinks: true,
            defaultProtocol: "https://",
            toggleDownloadable: {
              mode: "manual",
              label: "Downloadable",
              attributes: { download: "file" },
            },
            openInNewTab: {
              mode: "manual",
              label: "Open in a new tab",
              attributes: { target: "_blank", rel: "noopener noreferrer" },
            },
          },
        },
        removePlugins: removedPlugin,
      });
    }
  });
}
