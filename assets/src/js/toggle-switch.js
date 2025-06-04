/** LightAdminBundle/assets/scripts/toggle-switch.js */
export default class ToggleSwitch {
    constructor(field) {
        this.field = field;
        this.field.addEventListener('change', this.#updateFieldValue.bind(this));
    }

    #updateFieldValue() {
        let elem = this.field;

        let csrfToken = elem.dataset.token;
        let urlValue = elem.dataset.toggleUrl;
        let propertyName = elem.dataset.propertyName

        let options = {
                'credentials': 'same-origin',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                // Potentially add the CSRF token to headers if your backend expects it there
                'X-CSRF-Token': csrfToken,
        };

        fetch(urlValue, {
            method: "POST",
            mode: 'cors',
            body: JSON.stringify({
                propertyName: propertyName,
                _token: csrfToken, // Include the CSRF token in the body as well
            }),
            headers: options,
        })
        .then((response) => {
            //console.log(response);
        })
        //.then(() => { /* do nothing else when the toggle request is successful */ })
        .catch(() => this.#disableField());
    }

    // used in case of error, to restore the original toggle field value and disable it
    #disableField() {
        this.field.checked = !this.field.checked;
        this.field.disabled = true;
        this.field.closest('.form-switch').classList.add('disabled');
    }
}
