import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['panel', 'toggle'];

    connect() {
        this.close();
    }

    toggle() {
        this.element.classList.contains('menu-open') ? this.close() : this.open();
    }

    open() {
        this.element.classList.add('menu-open');
        this.toggleTarget.setAttribute('aria-expanded', 'true');
    }

    close() {
        this.element.classList.remove('menu-open');
        this.toggleTarget.setAttribute('aria-expanded', 'false');
    }
}
