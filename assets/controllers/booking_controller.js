import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['step', 'progress', 'back', 'next', 'submit', 'serviceSummary', 'slotSummary'];

    connect() {
        this.currentStep = 0;
        this.render();
    }

    next() {
        const fields = [...this.stepTargets[this.currentStep].querySelectorAll('input, select, textarea')];
        if (!fields.every((field) => field.checkValidity())) {
            fields.find((field) => !field.checkValidity())?.reportValidity();
            return;
        }

        if (this.currentStep < this.stepTargets.length - 1) {
            this.currentStep += 1;
            this.render();
        }
    }

    previous() {
        if (this.currentStep > 0) {
            this.currentStep -= 1;
            this.render();
        }
    }

    render() {
        this.stepTargets.forEach((step, index) => { step.hidden = index !== this.currentStep; });
        this.progressTargets.forEach((item, index) => {
            item.classList.toggle('active', index <= this.currentStep);
            item.setAttribute('aria-current', index === this.currentStep ? 'step' : 'false');
        });
        this.backTarget.hidden = this.currentStep === 0;
        this.nextTarget.hidden = this.currentStep === this.stepTargets.length - 1;
        this.submitTarget.hidden = this.currentStep !== this.stepTargets.length - 1;
        if (this.currentStep === this.stepTargets.length - 1) {
            this.updateSummary();
        }
    }

    updateSummary() {
        const service = this.element.querySelector('[name="appointment[service]"]');
        const slot = this.element.querySelector('[name="appointment[availability]"]');
        if (this.hasServiceSummaryTarget) this.serviceSummaryTarget.textContent = service?.selectedOptions[0]?.textContent?.trim() || '—';
        if (this.hasSlotSummaryTarget) this.slotSummaryTarget.textContent = slot?.selectedOptions[0]?.textContent?.trim() || '—';
    }
}
