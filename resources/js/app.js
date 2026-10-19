const toTopButton = document.createElement('button');

toTopButton.type = 'button';
toTopButton.className = 'to-top';
toTopButton.setAttribute('aria-label', 'Наверх');
toTopButton.textContent = 'Наверх';
document.body.append(toTopButton);

const updateToTopButton = () => {
    toTopButton.classList.toggle('visible', window.scrollY > 300);
};

window.addEventListener('scroll', updateToTopButton, { passive: true });
toTopButton.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
updateToTopButton();
