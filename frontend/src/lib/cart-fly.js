/**
 * Helper to animate a fly-to-cart effect from the click location to the sticky cart button.
 * @param {MouseEvent} event - The mouse click event to determine starting coordinates
 * @param {string|null} imageUrl - The URL of the product image to display on the flying element
 */
export function animateFlyToCart(event, imageUrl) {
  // 1. Locate the target (Floating Sticky Cart Button)
  const target = document.querySelector(".floating-cart-btn");
  if (!target) return;

  const targetRect = target.getBoundingClientRect();
  const targetX = targetRect.left + targetRect.width / 2;
  const targetY = targetRect.top + targetRect.height / 2;

  // 2. Locate the starting coordinates from the event
  let startX = event?.clientX;
  let startY = event?.clientY;

  // Fallback if event is missing or doesn't have coordinates
  if (startX === undefined || startY === undefined) {
    if (event?.target) {
      const rect = event.target.getBoundingClientRect();
      startX = rect.left + rect.width / 2;
      startY = rect.top + rect.height / 2;
    } else {
      // Direct center fallback
      startX = window.innerWidth / 2;
      startY = window.innerHeight / 2;
    }
  }

  // 3. Create the outer wrapper (for horizontal motion)
  const wrapper = document.createElement("div");
  wrapper.style.position = "fixed";
  wrapper.style.left = "0";
  wrapper.style.top = "0";
  wrapper.style.zIndex = "9999";
  wrapper.style.pointerEvents = "none";
  wrapper.style.transform = `translate3d(${startX}px, ${startY}px, 0)`;
  // Linear transition for horizontal component
  wrapper.style.transition = "transform 0.75s linear";

  // 4. Create the inner element (for vertical motion, scale, rotation, and content styling)
  const inner = document.createElement("div");
  inner.style.width = "52px";
  inner.style.height = "52px";
  inner.style.borderRadius = "50%";
  inner.style.border = "2px solid hsl(141, 72%, 36%)"; // Deal Pabo Green border
  inner.style.boxShadow = "0 8px 20px rgba(0, 0, 0, 0.2)";
  inner.style.backgroundColor = "white";
  inner.style.overflow = "hidden";
  inner.style.display = "flex";
  inner.style.alignItems = "center";
  inner.style.justifyContent = "center";
  inner.style.transform = "translate3d(0, 0, 0) scale(1) rotate(0deg)";
  // Cubic-bezier for vertical arc and scaling
  inner.style.transition = "transform 0.75s cubic-bezier(0.06, 0.975, 0.195, 0.965), opacity 0.75s ease-out";

  // Append product image if available, otherwise fallback to cart icon
  if (imageUrl) {
    const img = document.createElement("img");
    img.src = imageUrl;
    img.style.width = "100%";
    img.style.height = "100%";
    img.style.objectFit = "cover";
    inner.appendChild(img);
  } else {
    inner.style.backgroundColor = "hsl(141, 72%, 36%)";
    inner.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="8" cy="21" r="1"/>
        <circle cx="19" cy="21" r="1"/>
        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
      </svg>
    `;
  }

  wrapper.appendChild(inner);
  document.body.appendChild(wrapper);

  // Force a browser reflow to register the starting state
  wrapper.offsetWidth;

  // 5. Calculate transition deltas
  const deltaX = targetX - startX;
  const deltaY = targetY - startY;

  // 6. Apply final transformations to trigger the animation
  // Wrapper moves horizontally to target X
  wrapper.style.transform = `translate3d(${targetX}px, ${startY}px, 0)`;
  // Inner moves vertically to target Y (relative offset deltaY) and shrinks/rotates
  inner.style.transform = `translate3d(0, ${deltaY}px, 0) scale(0.12) rotate(360deg)`;
  inner.style.opacity = "0.2";

  // 7. Clean up element from the DOM after transition completes
  setTimeout(() => {
    wrapper.remove();
  }, 780);
}
