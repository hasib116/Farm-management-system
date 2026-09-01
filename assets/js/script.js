document.addEventListener("DOMContentLoaded", function () {
    initCart();
    autoHideAlerts();
});

function initCart() {
    const addButtons = document.querySelectorAll(".add-to-cart");
    if (!addButtons.length) return; // not on the marketplace page

    let cart = {};

    const cartList = document.getElementById("cart-list");
    const cartTotal = document.getElementById("cart-total");
    const cartDataInput = document.getElementById("cart_data");
    const orderForm = document.getElementById("order-form");

    addButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const qtyInput = document.querySelector('.qty-input[data-id="' + id + '"]');
            const qty = parseFloat(qtyInput.value);

            if (!qty || qty <= 0) {
                alert("Please enter a valid quantity.");
                return;
            }

            cart[id] = {
                qty: qty,
                price: parseFloat(qtyInput.dataset.price),
                name: qtyInput.dataset.name
            };

            renderCart();
        });
    });

    function renderCart() {
        const ids = Object.keys(cart);
        cartList.innerHTML = "";

        if (ids.length === 0) {
            cartList.innerHTML = '<li class="cart-empty">Cart is empty</li>';
            cartTotal.textContent = "0.00";
            return;
        }

        let total = 0;
        ids.forEach(id => {
            const item = cart[id];
            const subtotal = item.qty * item.price;
            total += subtotal;

            const li = document.createElement("li");
            li.innerHTML = `<span>${item.name} x ${item.qty}</span><span>৳${subtotal.toFixed(2)}
                <a href="#" data-remove="${id}" style="color:#c62828;margin-left:6px;">✕</a></span>`;
            cartList.appendChild(li);
        });

        cartTotal.textContent = total.toFixed(2);

        // wire up remove buttons
        cartList.querySelectorAll("[data-remove]").forEach(link => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                delete cart[this.dataset.remove];
                renderCart();
            });
        });
    }

    // Before submitting the order form, serialize the cart into the hidden input
    if (orderForm) {
        orderForm.addEventListener("submit", function (e) {
            if (Object.keys(cart).length === 0) {
                e.preventDefault();
                alert("Please add at least one product to your cart before ordering.");
                return;
            }

            const simpleCart = {};
            Object.keys(cart).forEach(id => {
                simpleCart[id] = cart[id].qty;
            });

            cartDataInput.value = JSON.stringify(simpleCart);
        });
    }
}

function autoHideAlerts() {
    const alerts = document.querySelectorAll(".alert:not(.alert-error)");
    alerts.forEach(a => {
        setTimeout(() => {
            a.style.transition = "opacity 0.5s";
            a.style.opacity = "0";
            setTimeout(() => a.remove(), 500);
        }, 4000);
    });
}
