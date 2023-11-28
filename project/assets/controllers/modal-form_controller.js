import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';

export default class extends Controller {
    static targets = ['modal'];


    openModal(event) {
        const modal = new Modal(this.modalTarget);
        let movieId = event.target.id;
        let url = "http://localhost:8741/detailMovie/" + movieId;
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById("movie-" + movieId).setAttribute("src", data);
                modal.show();

            })
            .catch(error => {
                console.error('Error:', error);
            });


    }
}