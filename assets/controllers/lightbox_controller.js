import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dialog', 'image'];

    open(event) {
        this.imageTarget.src = event.params.source;
        this.dialogTarget.showModal();
        document.body.classList.add('lightbox-open');
    }

    close() {
        this.dialogTarget.close();
        this.imageTarget.removeAttribute('src');
        document.body.classList.remove('lightbox-open');
    }

    backdropClose(event) {
        if (event.target === this.dialogTarget) this.close();
    }
}
