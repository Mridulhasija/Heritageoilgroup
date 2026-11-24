(() => {
    const modal = document.getElementById('oil-enquiry-modal');
    if (!modal) return;

    const image = document.getElementById('oil-modal-image');
    const title = document.getElementById('oil-modal-title');
    const productTitle = document.getElementById('oil-modal-product-title');
    const message = modal.querySelector('[name="message"]');
    const form = modal.querySelector('form');
    const formId = form.dataset.formId;
    form.querySelector('.captcha-form-id').value = formId;
    form.querySelector('.captcha-image').dataset.formId = formId;
    form.querySelector('.captcha-image').src = modal.dataset.captchaUrl + '?generate=1&form_id=' + encodeURIComponent(formId);
    document.querySelectorAll('.oil-enquire').forEach(button => button.addEventListener('click', () => {
        title.textContent = button.dataset.oilName;
        productTitle.value = button.dataset.oilName;
        image.src = button.dataset.oilImage;
        image.alt = button.dataset.oilName;
        message.value = 'I would like to know more about ' + button.dataset.oilName + '.';
        modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.classList.add('overflow-hidden');
    }));
    const close = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); };
    modal.querySelector('.oil-modal-close').addEventListener('click', close);
    modal.addEventListener('click', event => { if (event.target === modal) close(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && !modal.classList.contains('hidden')) close(); });
})();
