document.addEventListener("DOMContentLoaded", function () {
  const errorMap = [
    "Invalid number",
    "Invalid country code",
    "Too short",
    "Too long",
    "Invalid number",
  ];

  const forms = document.querySelectorAll(".contact-form");

  forms.forEach(function (form) {
    const input = form.querySelector(".phone");
    const errorMsg = form.querySelector(".error-msg");
    const validMsg = form.querySelector(".valid-msg");
    const countryInput = form.querySelector('input[name="location"]');
    const captchaImage = form.querySelector(".captcha-image");
    const captchaRefreshBtn = form.querySelector(".captcha-refresh-btn");
    const captchaInput = form.querySelector(".captcha-input");
    const formId =
      form.dataset.formId || form.querySelector(".captcha-form-id")?.value;
    const responseEl = form.querySelector(".form-response-message");
    const submitBtn = form.querySelector('button[type="submit"]');

    const formAction = form.getAttribute("action") || "";
    const baseUrl = formAction.substring(0, formAction.lastIndexOf("/")) || "";

    if (!input || !errorMsg || !validMsg) {
      return;
    }

    function refreshCaptcha() {
      if (captchaImage && formId) {
        const timestamp = new Date().getTime();
        const captchaUrl = baseUrl ? `${baseUrl}/captcha.php` : "/captcha.php";
        captchaImage.src = `${captchaUrl}?generate=1&form_id=${formId}&t=${timestamp}`;
      }
      if (captchaInput) captchaInput.value = "";
    }

    if (captchaRefreshBtn) {
      captchaRefreshBtn.addEventListener("click", refreshCaptcha);
    }
    if (captchaImage) {
      captchaImage.addEventListener("click", refreshCaptcha);
    }

    // Usage with intl-tel-input
    const utilsUrl = baseUrl + "/assets/intlTelInput/utils.js";

    const iti = window.intlTelInput(input, {
      initialCountry: "auto",
      geoIpLookup: function (callback) {
        callback(getCountry());
      },
      loadUtilsOnInit: utilsUrl,
    });

    // Optional: Log detected country for debugging
    // console.log('Detected country:', getCountry());

    const reset = () => {
      input.classList.remove("error");
      errorMsg.innerHTML = "";
      validMsg.innerHTML = "";
      errorMsg.style.display = "none";
      validMsg.style.display = "none";
    };

    const validateForm = () => {
      reset();
      if (!input.value.trim()) {
        errorMsg.innerHTML = "Required";
        errorMsg.style.display = "inline";
        input.focus();
        return false;
      }
      if (!iti.isValidNumber()) {
        input.classList.add("error");
        const errorCode = iti.getValidationError();
        errorMsg.innerHTML = errorMap[errorCode] || "Invalid number";
        errorMsg.style.display = "inline";
        input.focus();
        return false;
      }
      validMsg.innerHTML = "✓ Valid";
      validMsg.style.display = "inline";

      if (countryInput) {
        const countryData = iti.getSelectedCountryData();
        countryInput.value = countryData.name.replace(/\s*\(.*?\)/, "");
      }
      return true;
    };

    function showResponse(success, message, variant) {
      if (!responseEl) return;
      const resolvedVariant = variant || (success ? "success" : "error");
      responseEl.classList.remove(
        "hidden",
        "bg-blue-50",
        "text-blue-800",
        "border-blue-200",
        "bg-green-50",
        "text-green-800",
        "border-green-200",
        "bg-red-50",
        "text-red-800",
        "border-red-200",
      );
      responseEl.classList.add("border");
      if (resolvedVariant === "success") {
        responseEl.classList.add(
          "bg-green-50",
          "text-green-800",
          "border-green-200",
        );
      } else if (resolvedVariant === "info") {
        responseEl.classList.add(
          "bg-blue-50",
          "text-blue-800",
          "border-blue-200",
        );
      } else {
        responseEl.classList.add("bg-red-50", "text-red-800", "border-red-200");
      }
      responseEl.textContent = message;
      responseEl.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }

    function setSubmitting(loading) {
      if (!submitBtn) return;
      submitBtn.disabled = loading;
      form.setAttribute("aria-busy", loading ? "true" : "false");

      if (loading) {
        if (!submitBtn.dataset.originalHtml) {
          submitBtn.dataset.originalHtml = submitBtn.innerHTML;
        }
        const loadingText = submitBtn.dataset.loadingText || "Sending...";
        submitBtn.classList.add("opacity-80", "cursor-not-allowed");
        submitBtn.innerHTML = `
          <span class="inline-flex items-center justify-center gap-2">
            <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" aria-hidden="true">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span>${loadingText}</span>
          </span>
        `;
      } else {
        submitBtn.classList.remove("opacity-80", "cursor-not-allowed");
        if (submitBtn.dataset.originalHtml) {
          submitBtn.innerHTML = submitBtn.dataset.originalHtml;
        }
      }
    }

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      if (!validateForm()) return;

      input.value = iti.getNumber();

      const formData = new FormData(form);
      const action = form.getAttribute("action");

      setSubmitting(true);
      showResponse(true, "Sending... Please wait.", "info");

      if (!action) {
        setSubmitting(false);
        showResponse(
          false,
          "Form action is missing. Please refresh and try again.",
        );
        refreshCaptcha();
        return;
      }

      fetch(action, {
        method: "POST",
        credentials: "same-origin",
        body: formData,
      })
        .then((res) => {
          const contentType = res.headers.get("content-type") || "";
          if (!contentType.includes("application/json")) {
            throw new Error("Server error. Please try again later.");
          }
          return res.json();
        })
        .then((data) => {
          if (!data || typeof data.success !== "boolean") {
            throw new Error("Unexpected server response. Please try again.");
          }

          showResponse(data.success, data.message);
          if (data.success) {
            form.reset();
            reset();
            refreshCaptcha();
            // Show floating alert on success
            try {
              if (typeof window.showAlert === "function") {
                window.showAlert(
                  data.message || "Form submitted successfully!",
                );
              }
            } catch (e) {
              // Ignore floating alert failures; inline confirmation is already shown.
            }
          } else {
            // Regenerate captcha on validation failure
            refreshCaptcha();
          }
        })
        .catch((err) => {
          showResponse(
            false,
            err.message || "Something went wrong. Please try again.",
          );
          refreshCaptcha();
        })
        .finally(() => {
          setSubmitting(false);
        });
    });
  });
});

// Comprehensive timezone to country mapping
const TZ_TO_COUNTRY = {
  // North America - US
  "America/New_York": "us",
  "America/Los_Angeles": "us",
  "America/Chicago": "us",
  "America/Denver": "us",
  "America/Phoenix": "us",
  "America/Anchorage": "us",
  "America/Honolulu": "us",
  "America/Detroit": "us",
  "America/Boise": "us",
  "America/Indiana/Indianapolis": "us",
  "America/Juneau": "us",
  "America/Adak": "us",
  "America/Nome": "us",
  "America/Sitka": "us",
  "America/Metlakatla": "us",
  "America/Yakutat": "us",
  "America/Kentucky/Louisville": "us",
  "America/Indiana/Vincennes": "us",
  "America/Indiana/Tell_City": "us",
  "America/North_Dakota/Center": "us",
  "Pacific/Honolulu": "us",

  // Canada
  "America/Toronto": "ca",
  "America/Vancouver": "ca",
  "America/Edmonton": "ca",
  "America/Winnipeg": "ca",
  "America/Halifax": "ca",
  "America/St_Johns": "ca",
  "America/Regina": "ca",
  "America/Whitehorse": "ca",
  "America/Yellowknife": "ca",
  "America/Iqaluit": "ca",
  "America/Moncton": "ca",
  "America/Goose_Bay": "ca",

  // Mexico
  "America/Mexico_City": "mx",
  "America/Tijuana": "mx",
  "America/Cancun": "mx",
  "America/Monterrey": "mx",
  "America/Mazatlan": "mx",
  "America/Chihuahua": "mx",
  "America/Hermosillo": "mx",
  "America/Merida": "mx",

  // South America
  "America/Sao_Paulo": "br",
  "America/Manaus": "br",
  "America/Fortaleza": "br",
  "America/Recife": "br",
  "America/Belem": "br",
  "America/Cuiaba": "br",
  "America/Buenos_Aires": "ar",
  "America/Cordoba": "ar",
  "America/Mendoza": "ar",
  "America/Santiago": "cl",
  "America/Bogota": "co",
  "America/Lima": "pe",
  "America/Caracas": "ve",
  "America/Montevideo": "uy",
  "America/Asuncion": "py",
  "America/La_Paz": "bo",
  "America/Guayaquil": "ec",
  "America/Cayenne": "gf",

  // Central America & Caribbean
  "America/Panama": "pa",
  "America/Costa_Rica": "cr",
  "America/Guatemala": "gt",
  "America/Havana": "cu",
  "America/Jamaica": "jm",
  "America/Santo_Domingo": "do",
  "America/Port_of_Spain": "tt",
  "America/Barbados": "bb",
  "America/Belize": "bz",

  // Europe - Western
  "Europe/London": "gb",
  "Europe/Paris": "fr",
  "Europe/Berlin": "de",
  "Europe/Madrid": "es",
  "Europe/Rome": "it",
  "Europe/Amsterdam": "nl",
  "Europe/Brussels": "be",
  "Europe/Vienna": "at",
  "Europe/Zurich": "ch",
  "Europe/Dublin": "ie",
  "Europe/Lisbon": "pt",
  "Europe/Luxembourg": "lu",

  // Europe - Northern
  "Europe/Stockholm": "se",
  "Europe/Oslo": "no",
  "Europe/Copenhagen": "dk",
  "Europe/Helsinki": "fi",
  "Europe/Reykjavik": "is",
  "Europe/Tallinn": "ee",
  "Europe/Riga": "lv",
  "Europe/Vilnius": "lt",

  // Europe - Central & Eastern
  "Europe/Warsaw": "pl",
  "Europe/Prague": "cz",
  "Europe/Budapest": "hu",
  "Europe/Bucharest": "ro",
  "Europe/Athens": "gr",
  "Europe/Sofia": "bg",
  "Europe/Belgrade": "rs",
  "Europe/Zagreb": "hr",
  "Europe/Bratislava": "sk",
  "Europe/Ljubljana": "si",
  "Europe/Sarajevo": "ba",

  // Europe - Eastern
  "Europe/Moscow": "ru",
  "Europe/Kyiv": "ua",
  "Europe/Minsk": "by",
  "Europe/Chisinau": "md",
  "Europe/Istanbul": "tr",

  // Middle East
  "Asia/Dubai": "ae",
  "Asia/Riyadh": "sa",
  "Asia/Kuwait": "kw",
  "Asia/Doha": "qa",
  "Asia/Bahrain": "bh",
  "Asia/Muscat": "om",
  "Asia/Baghdad": "iq",
  "Asia/Tehran": "ir",
  "Asia/Jerusalem": "il",
  "Asia/Beirut": "lb",
  "Asia/Damascus": "sy",
  "Asia/Amman": "jo",

  // South Asia
  "Asia/Kolkata": "in",
  "Asia/Calcutta": "in",
  "Asia/Karachi": "pk",
  "Asia/Dhaka": "bd",
  "Asia/Kathmandu": "np",
  "Asia/Colombo": "lk",

  // Southeast Asia
  "Asia/Bangkok": "th",
  "Asia/Singapore": "sg",
  "Asia/Kuala_Lumpur": "my",
  "Asia/Jakarta": "id",
  "Asia/Manila": "ph",
  "Asia/Ho_Chi_Minh": "vn",
  "Asia/Yangon": "mm",
  "Asia/Phnom_Penh": "kh",
  "Asia/Vientiane": "la",
  "Asia/Brunei": "bn",

  // East Asia
  "Asia/Shanghai": "cn",
  "Asia/Hong_Kong": "hk",
  "Asia/Tokyo": "jp",
  "Asia/Seoul": "kr",
  "Asia/Taipei": "tw",
  "Asia/Macau": "mo",
  "Asia/Ulaanbaatar": "mn",

  // Central Asia
  "Asia/Almaty": "kz",
  "Asia/Tashkent": "uz",
  "Asia/Bishkek": "kg",
  "Asia/Dushanbe": "tj",
  "Asia/Ashgabat": "tm",

  // Oceania
  "Australia/Sydney": "au",
  "Australia/Melbourne": "au",
  "Australia/Perth": "au",
  "Australia/Brisbane": "au",
  "Australia/Adelaide": "au",
  "Australia/Darwin": "au",
  "Australia/Hobart": "au",
  "Australia/Canberra": "au",
  "Pacific/Auckland": "nz",
  "Pacific/Fiji": "fj",
  "Pacific/Port_Moresby": "pg",
  "Pacific/Guam": "gu",
  "Pacific/Tahiti": "pf",
  "Pacific/Tongatapu": "to",

  // Africa
  "Africa/Cairo": "eg",
  "Africa/Johannesburg": "za",
  "Africa/Lagos": "ng",
  "Africa/Nairobi": "ke",
  "Africa/Casablanca": "ma",
  "Africa/Algiers": "dz",
  "Africa/Tunis": "tn",
  "Africa/Accra": "gh",
  "Africa/Addis_Ababa": "et",
  "Africa/Dar_es_Salaam": "tz",
  "Africa/Kampala": "ug",
  "Africa/Khartoum": "sd",
  "Africa/Tripoli": "ly",
  "Africa/Kinshasa": "cd",
};

/**
 * Get country from browser timezone
 */
function getCountryFromTimezone() {
  try {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (!tz) return null;

    // Direct match
    if (TZ_TO_COUNTRY[tz]) {
      return TZ_TO_COUNTRY[tz];
    }

    // Fallback: try to match by timezone prefix (e.g., "America/Unknown" → check other America zones)
    const tzPrefix = tz.split("/")[0];
    for (const [key, country] of Object.entries(TZ_TO_COUNTRY)) {
      if (key.startsWith(tzPrefix + "/")) {
        return country; // Return first matching country in same region
      }
    }

    return null;
  } catch (error) {
    console.warn("Timezone detection failed:", error);
    return null;
  }
}

/**
 * Get UTC offset and infer likely region
 */
function getCountryFromOffset() {
  try {
    const offset = -new Date().getTimezoneOffset() / 60;

    // Common offset to country mapping (fallback only)
    const offsetMap = {
      "-10": "us",
      "-9": "us",
      "-8": "us",
      "-7": "us",
      "-6": "us",
      "-5": "us",
      "-4": "us",
      0: "gb",
      1: "fr",
      2: "de",
      3: "sa",
      5.5: "in",
      8: "sg",
      9: "jp",
    };

    return offsetMap[offset.toString()] || null;
  } catch (error) {
    return null;
  }
}

/**
 * Main function: Get country code with multiple fallback strategies
 */
function getCountry() {
  // Priority 1: Timezone (most reliable)
  const tzCountry = getCountryFromTimezone();
  if (tzCountry) return tzCountry;

  // Priority 3: UTC offset (least reliable, but better than nothing)
  const offsetCountry = getCountryFromOffset();
  if (offsetCountry) return offsetCountry;

  // Default fallback
  return "us";
}
