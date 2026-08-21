import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'item', 'empty'];

    filter() {
        const query = this.inputTarget.value.trim().toLocaleLowerCase();
        let visible = 0;

        this.itemTargets.forEach((item) => {
            const matches = item.dataset.searchValue.includes(query);
            item.hidden = !matches;
            if (matches) visible += 1;
        });

        this.emptyTarget.hidden = visible !== 0;
    }
}
