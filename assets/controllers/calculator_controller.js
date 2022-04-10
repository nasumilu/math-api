import { Controller } from '@hotwired/stimulus';
import {Dropdown} from "bootstrap";
/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

import MathApi from '../math_api';

export default class extends Controller {

    static targets = ['display'];
    
    
    connect() {
        this.api = new MathApi();
    }

    get mrow() {
        return this.displayTarget.querySelector('mrow');
    }

    mo(evt) {
        evt.preventDefault();
        let mo = document.createElement('mo');
        mo.innerText = evt.target.innerText;
        this.mrow.append(mo);
    }

    mn(evt) {
        evt.preventDefault();
        let mn = document.createElement('mn');
        mn.innerText = evt.target.innerText;
        this.mrow.append(mn);
    }

    bksp(evt) {
        const lastChild = this.mrow.lastChild;
        if(lastChild) {
            this.mrow.removeChild(lastChild);
        }
    }

    async calculate(evt) {
        const value = await this.api.calculate(this.mrow.parentElement);
        this.clear();
        this.mrow.append(value.querySelector('mrow').firstChild);
    }

    clear(evt) {
        evt?.preventDefault();
        const mrow = this.mrow;
        while(mrow.firstChild) {
            mrow.removeChild(mrow.firstChild);
        }
    }
}
