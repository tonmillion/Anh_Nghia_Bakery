let isSliding = false;

function slideCategories(direction) {
    if (isSliding) return;
    isSliding = true;
    
    const slider = document.getElementById('categorySlider');
    // gap = 20 from CSS
    const gap = 20; 
    const cardWidth = slider.firstElementChild.offsetWidth;
    const slideWidth = cardWidth + gap;
    
    if (direction === 1) {
        // Next
        slider.style.transition = 'transform 0.4s ease-in-out';
        slider.style.transform = `translateX(-${slideWidth}px)`;
        
        setTimeout(() => {
            slider.style.transition = 'none';
            slider.appendChild(slider.firstElementChild);
            slider.style.transform = 'translateX(0)';
            isSliding = false;
        }, 400);
    } else {
        // Prev
        slider.prepend(slider.lastElementChild);
        slider.style.transition = 'none';
        slider.style.transform = `translateX(-${slideWidth}px)`;
        
        // Force hardware reflow to apply the start position immediately
        void slider.offsetWidth; 
        
        slider.style.transition = 'transform 0.4s ease-in-out';
        slider.style.transform = 'translateX(0)';
        
        setTimeout(() => {
            isSliding = false;
        }, 400);
    }
}
