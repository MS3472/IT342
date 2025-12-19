// Public/assets/js/cart.js

function getCart() {
  return JSON.parse(localStorage.getItem('cart') || '[]');
}

function saveCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartBadge();
}

function updateCartBadge() {
  const badge = document.getElementById('cartBadge');
  if (!badge) return;
  const count = getCart().reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
  badge.textContent = count;
}

function addItemToCart(item) {
  const cart = getCart();

  const existing = cart.findIndex(x => x.name === item.name);
  if (existing >= 0) {
    cart[existing].quantity = (Number(cart[existing].quantity) || 1) + (Number(item.quantity) || 1);
  } else {
    cart.push({
      id: item.id || null,
      name: item.name,
      price: Number(item.price),
      quantity: Number(item.quantity) || 1,
      description: item.description || '',
      image: item.image || ''
    });
  }

  saveCart(cart);
}

document.addEventListener('DOMContentLoaded', () => {
  updateCartBadge();

  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.product-card');
      if (!card) return;

      const name = card.querySelector('.product-name')?.textContent?.trim();
      const priceText = card.querySelector('.product-price')?.textContent?.trim() || '';
      const price = Number(priceText.replace('$', ''));
      const description = card.querySelector('.product-description')?.textContent?.trim() || '';
      const image = card.querySelector('img')?.getAttribute('src') || '';

      if (!name || !Number.isFinite(price)) {
        console.error('Add to cart failed: missing name/price');
        return;
      }

      addItemToCart({ name, price, quantity: 1, description, image });

      const original = btn.textContent;
      btn.textContent = '✓ Added';
      btn.disabled = true;
      setTimeout(() => {
        btn.textContent = original;
        btn.disabled = false;
      }, 800);
    });
  });
});
