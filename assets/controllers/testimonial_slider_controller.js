import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['slide', 'dot'];
    connect() {
        this.index = 0;
        this.render();
        if (this.slideTargets.length > 1) this.timer = setInterval(() => this.next(), 6500);
    }
    disconnect() { clearInterval(this.timer); }
    next() { this.index = (this.index + 1) % this.slideTargets.length; this.render(); }
    previous() { this.index = (this.index - 1 + this.slideTargets.length) % this.slideTargets.length; this.render(); }
    go(event) { this.index = Number(event.params.index); this.render(); }
    render() {
        this.slideTargets.forEach((slide, index) => { slide.hidden = index !== this.index; });
        this.dotTargets.forEach((dot, index) => dot.classList.toggle('active', index === this.index));
    }
}
