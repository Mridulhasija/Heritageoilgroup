function formatPriceUsingConfig(price) {
  const config = window.__CURRENCY_FORMAT || {};
  const symbol = config.symbol || "";
  const thousand = config.thousand || ",";
  const decimal = config.decimal || ".";
  const precision =
    typeof config.precision === "number" ? config.precision : 2;

  const number = Number(price);
  const safe = Number.isFinite(number) ? number : 0;

  const fixed = safe.toFixed(precision);
  const [whole, frac] = fixed.split(".");
  const withThousands = whole.replace(/\B(?=(\d{3})+(?!\d))/g, thousand);

  if (precision <= 0) {
    return `${symbol}${withThousands}`;
  }

  return `${symbol}${withThousands}${decimal}${frac}`;
}

function parsePriceToNumber(text) {
  const config = window.__CURRENCY_FORMAT || {};
  const thousand = config.thousand || ",";
  const decimal = config.decimal || ".";

  let raw = String(text || "");
  raw = raw.replace(/\s+/g, "");

  if (thousand) {
    const escaped = thousand.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    raw = raw.replace(new RegExp(escaped, "g"), "");
  }

  if (decimal && decimal !== ".") {
    const escaped = decimal.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    raw = raw.replace(new RegExp(escaped, "g"), ".");
  }

  raw = raw.replace(/[^0-9.-]/g, "");
  const num = Number(raw);
  return Number.isFinite(num) ? num : 0;
}

function calculateCartTotal() {
  const totals = [...document.querySelectorAll(".final-item-price")];
  const total = totals.reduce((sum, el) => sum + parsePriceToNumber(el.innerText), 0);
  const flatRate = Number(document.getElementById("cartSummary")?.dataset.flatRate) || 0;
  const subtotalEl = document.getElementById("cartSubtotal");
  const shipping = document.querySelector('input[name="shipping_method"]:checked')?.value === "pickup" ? 0 : flatRate;
  if (subtotalEl) subtotalEl.innerText = formatPriceUsingConfig(total);
  const totalEl = document.getElementById("cartTotal");
  if (totalEl) {
    totalEl.innerText = formatPriceUsingConfig(total > 0 ? total + shipping : 0);
  }
  return total;
}

function toggleCheckoutButton(total) {
  const checkoutBtn = document.getElementById("checkoutBtn");
  if (!checkoutBtn) return;

  const hasTotal = typeof total === "number" ? total : calculateCartTotal();
  checkoutBtn.classList.toggle("hidden", !(hasTotal > 0));
}

function update_cart(id, quantity) {
  const cart = getCartFromCookie();
  const item = cart.find((entry) => entry.productID === id);
  if (!item) return;

  item.quantity = quantity;
  setCartCookie(cart);
  const total = calculateCartTotal();
  toggleCheckoutButton(total);
  viewCart();
}

function remove_item(id) {
  const cart = getCartFromCookie();
  const index = cart.findIndex((entry) => entry.productID === id);
  if (index === -1) return;

  const row = document.getElementById(id);
  if (row) {
    row.remove();
  }

  cart.splice(index, 1);
  setCartCookie(cart);
  const total = calculateCartTotal();
  toggleCheckoutButton(total);
  viewCart();
}

document.addEventListener("click", function (event) {
  const target = event.target;
  if (!target || !target.classList || !target.classList.contains("qtybtn")) {
    return;
  }

  const button = target;
  const inputField = button.parentElement ? button.parentElement.querySelector("input") : null;
  const cartItem = button.closest(".cart-item");

  if (!inputField || !cartItem) return;

  const oldValue = parseInt(inputField.value, 10);
  const safeOld = Number.isFinite(oldValue) && oldValue > 0 ? oldValue : 1;
  const newVal = button.classList.contains("inc")
    ? safeOld + 1
    : Math.max(safeOld - 1, 1);

  const priceEl = cartItem.querySelector(".item-price");
  const unitPrice = priceEl ? parsePriceToNumber(priceEl.innerText) : 0;
  const subtotal = newVal * unitPrice;

  const subtotalEl = cartItem.querySelector(".final-item-price");
  if (subtotalEl) {
    subtotalEl.innerText = formatPriceUsingConfig(subtotal);
  }
  inputField.value = String(newVal);

  update_cart(cartItem.id, newVal);
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('input[name="shipping_method"]').forEach((input) => {
    input.addEventListener("change", () => {
      document.cookie = `shipping_method=${input.value}; path=/; SameSite=Lax`;
      calculateCartTotal();
    });
  });
  calculateCartTotal();
  toggleCheckoutButton();
});

