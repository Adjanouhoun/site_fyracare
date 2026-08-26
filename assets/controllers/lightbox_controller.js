import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dialog', 'image', 'caption', 'previous', 'next'];

    connect() {
        this.currentIndex = 0;
        this.keydown = this.keydown.bind(this);
    }

    open(event) {
        this.items = Array.from(this.element.querySelectorAll('[data-lightbox-source-param]'));
        const clickedIndex = this.items.indexOf(event.currentTarget);
        this.currentIndex = clickedIndex >= 0 ? clickedIndex : 0;
        this.show(event.params.source, event.params.caption || '');
        this.dialogTarget.showModal();
        document.body.classList.add('lightbox-open');
        document.addEventListener('keydown', this.keydown);
    }

    show(source, caption) {
        this.imageTarget.src = source;
        this.imageTarget.alt = caption || 'FyraCare';
        this.captionTarget.textContent = caption || '';
        const hasSeveral = (this.items?.length || 0) > 1;
        this.previousTarget.hidden = !hasSeveral;
        this.nextTarget.hidden = !hasSeveral;
    }

    previous() { this.move(-1); }
    next() { this.move(1); }

    move(direction) {
        if (!this.items?.length) return;
        this.currentIndex = (this.currentIndex + direction + this.items.length) % this.items.length;
        const item = this.items[this.currentIndex];
        this.show(item.dataset.lightboxSourceParam, item.dataset.lightboxCaptionParam || '');
    }

    keydown(event) {
        if (event.key === 'Escape') this.close();
        if (event.key === 'ArrowLeft') this.previous();
        if (event.key === 'ArrowRight') this.next();
    }

    close() {
        this.dialogTarget.close();
        this.imageTarget.removeAttribute('src');
        document.body.classList.remove('lightbox-open');
        document.removeEventListener('keydown', this.keydown);
    }

    backdropClose(event) {
        if (event.target === this.dialogTarget) this.close();
    }
}
