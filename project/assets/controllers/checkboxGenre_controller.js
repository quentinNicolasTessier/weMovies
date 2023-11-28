import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    redirection(event) {
        if (event.target.checked == true) {
            window.location.href = "/genre/" + event.target.id;
        } else {
            window.location.href = "/";
        }
    }
}