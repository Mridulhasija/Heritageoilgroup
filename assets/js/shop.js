function getCartFromCookie() {
  const cartCookie = document.cookie
    .split("; ")
    .find((row) => row.startsWith("cart="));

  if (!cartCookie) {
    return [];
  }

  try {
    const parsed = JSON.parse(cartCookie.split("=")[1]);
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

function setCartCookie(cart) {
  document.cookie = `cart=${JSON.stringify(cart)}; path=/`;
}

function normalizeQuantity(quantity) {
  const parsed = parseInt(quantity, 10);
  return Number.isNaN(parsed) || parsed < 1 ? 1 : parsed;
}

function getQuantityField(button) {
  if (!button) {
    return null;
  }

  if (button.dataset.quantitySelector) {
    const targetedField = document.querySelector(button.dataset.quantitySelector);
    if (targetedField) {
      return targetedField;
    }
  }

  const containers = [
    button.closest("[data-quantity-container]"),
    button.closest(".quantity-wrapper"),
    button.closest(".flex"),
  ];

  for (const container of containers) {
    if (!container) {
      continue;
    }

    const field = container.querySelector(
      'input[data-quantity], input[name="quantity"], input.quantity-input, #quantityInput'
    );

    if (field) {
      return field;
    }
  }

  return document.querySelector("#quantityInput");
}

function addToCart(productID, requestedQuantity = 1) {
  const quantityToAdd = normalizeQuantity(requestedQuantity);
  const currentCart = getCartFromCookie();

  const existingItemIndex = currentCart.findIndex(
    (item) => item.productID === productID
  );

  if (existingItemIndex !== -1) {
    currentCart[existingItemIndex].quantity += quantityToAdd;
  } else {
    currentCart.push({ productID, quantity: quantityToAdd });
  }

  setCartCookie(currentCart);
  viewCart();
}

function updateCartCount(count) {
  const cartCountElements = document.querySelectorAll(".cart-count");

  if (count > 0) {
    cartCountElements.forEach((element) => {
      element.textContent = count;
      element.style.display = "inline-flex";
    });
  } else {
    cartCountElements.forEach((element) => {
      element.style.display = "none";
    });
  }
}

function viewCart() {
  const currentCart = getCartFromCookie();
  const totalQuantity = currentCart.reduce(
    (total, item) => total + normalizeQuantity(item.quantity),
    0
  );

  updateCartCount(totalQuantity);
}

function showAlert(msg, cartLink = false) {
  let alertContainer = document.getElementById("floating-alert-container");
  if (!alertContainer) {
    alertContainer = document.createElement("div");
    alertContainer.id = "floating-alert-container";
    alertContainer.dataset.cartUrl = document.querySelector(".cart-count")?.closest("a")?.href || "/cart";
    alertContainer.className = "fixed right-4 top-24 z-[120] grid max-w-sm gap-3";
    document.body.appendChild(alertContainer);
  }

  const alertMessage = document.createElement("div");
  alertMessage.className =
    "bg-green-50 border border-green-200 text-green-900 px-4 py-3 rounded-2xl shadow-lg flex items-center gap-3";

  const content = document.createElement(cartLink ? "a" : "span");
  content.className = "flex-1 font-semibold";
  content.textContent = msg;
  if (cartLink) {
    content.href = alertContainer.dataset.cartUrl;
    const action = document.createElement("span");
    action.className = "block mt-1 text-sm underline";
    action.textContent = "View cart →";
    content.appendChild(action);
  }
  const close = document.createElement("button");
  close.type = "button";
  close.className = "text-green-800 text-xl";
  close.setAttribute("aria-label", "Close notification");
  close.textContent = "×";
  alertMessage.append(content, close);

  const closeBtn = alertMessage.querySelector("button");
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      alertMessage.remove();
    });
  }

  alertContainer.appendChild(alertMessage);

  setTimeout(() => {
    alertMessage.remove();
  }, 6000);
}

document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".btn-cart");
  buttons.forEach((button) => {
    if (button.hasAttribute("data-id")) {
      button.addEventListener("click", (e) => {
        e.preventDefault();
        const quantityField = getQuantityField(e.currentTarget);
        const quantity = quantityField ? normalizeQuantity(quantityField.value) : 1;
        addToCart(e.currentTarget.getAttribute("data-id"), quantity);
        showAlert("Product added to your cart.", true);
        const confirmation = document.getElementById("cart-confirmation");
        if (confirmation) confirmation.classList.remove("hidden");
      });
    }
  });

  viewCart();
});

